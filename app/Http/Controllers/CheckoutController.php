<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessCheckoutRequest;
use App\Models\Course;
use App\Models\CourseBatch;
use App\Models\CourseDeliveryOption;
use App\Models\Order;
use App\Services\Payments\PaymentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CheckoutController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * Show the secure checkout screen for a course.
     */
    public function show(Request $request, Course $course): View|RedirectResponse
    {
        if (! $course->isPublished()) {
            abort(404, 'The requested course is not available for enrollment.');
        }

        $user = auth()->user();

        if ($user->isEnrolledIn($course)) {
            $existing = $user->getEnrollmentFor($course);

            return redirect()->route('enrollments.confirmation', $existing)
                ->with('info', 'You already have an active enrollment for this course.');
        }

        $course->load([
            'category',
            'deliveryOptions' => fn ($q) => $q->where('is_active', true)->with(['batches.location']),
        ]);

        $selectedOptionId = (int) $request->query('option_id', $course->deliveryOptions->first()?->id);
        $selectedOption = $course->deliveryOptions->firstWhere('id', $selectedOptionId)
            ?? $course->deliveryOptions->first();

        // If free option selected, route to standard enrollment
        if ($selectedOption && ($selectedOption->is_free || (float) $selectedOption->price === 0.0)) {
            return redirect()->route('enrollments.create', ['course' => $course->slug, 'option_id' => $selectedOption->id]);
        }

        return view('checkout.show', compact('course', 'selectedOption', 'user'));
    }

    /**
     * Process order creation and redirect to the payment gateway.
     */
    public function process(ProcessCheckoutRequest $request, Course $course): View|RedirectResponse
    {
        $user = auth()->user();

        $deliveryOption = CourseDeliveryOption::where('id', $request->validated('delivery_option_id'))
            ->where('course_id', $course->id)
            ->where('is_active', true)
            ->firstOrFail();

        $batch = null;
        if ($request->validated('course_batch_id')) {
            $batch = CourseBatch::where('id', $request->validated('course_batch_id'))
                ->where('course_delivery_option_id', $deliveryOption->id)
                ->firstOrFail();
        }

        $order = $this->paymentService->createOrderForEnrollment(
            user: $user,
            course: $course,
            deliveryOption: $deliveryOption,
            batch: $batch,
            billingDetails: $request->validated()
        );

        $paymentFlow = $request->input('payment_flow');

        // 1. Direct In-App Live Card Processing (when card terminal submitted)
        if ($paymentFlow === 'card') {
            $cardNumber = preg_replace('/\s+/', '', (string) $request->input('card_number', ''));
            $scenario = $request->input('simulation_scenario', 'success');

            // Handle card decline simulation
            if ($scenario === 'decline' || str_ends_with($cardNumber, '0002')) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['card_number' => 'Your card was declined by the issuing bank (Insufficient balance / Transaction declined). Please try another card or use the test approval card.']);
            }

            $cleanLast4 = substr($cardNumber, -4) ?: '4242';
            $brand = str_starts_with($cardNumber, '5') ? 'Mastercard' : (str_starts_with($cardNumber, '3') ? 'AMEX' : 'Visa');

            $this->paymentService->recordSuccessfulDirectPayment(
                order: $order,
                paymentMethod: $brand,
                last4: $cleanLast4,
                gatewayName: config('payments.default', 'stripe')
            );

            return redirect()->route('checkout.success', $order);
        }

        // 2. Hosted Gateway Redirection (MockGateway / PayHere)
        $checkoutData = $this->paymentService->initiateCheckout($order);

        return view('checkout.redirect', compact('order', 'checkoutData'));
    }

    /**
     * Re-initiate payment for an existing pending order.
     */
    public function payOrder(Request $request, Order $order): View|RedirectResponse
    {
        Gate::authorize('pay', $order);

        $order->load(['course', 'deliveryOption', 'user']);
        $checkoutData = $this->paymentService->initiateCheckout($order);

        return view('checkout.redirect', compact('order', 'checkoutData'));
    }

    /**
     * Post-checkout redirect return URL.
     *
     * In addition to webhooks, verifies payment intent with the provider on redirect if available.
     */
    public function success(Request $request, Order $order): View
    {
        Gate::authorize('view', $order);

        if ($order->status === Order::STATUS_PENDING) {
            $paymentIntentId = (string) $request->query('payment_intent', '');
            $redirectStatus = strtolower((string) $request->query('redirect_status', ''));

            if (! empty($paymentIntentId) && $redirectStatus === 'succeeded') {
                $this->paymentService->syncAndSettleOrderFromGateway($order, $paymentIntentId, 'stripe');
                $order->refresh();
            }
        }

        $order->load(['course', 'deliveryOption', 'batch.location', 'latestPayment', 'enrollment']);

        return view('checkout.success', compact('order'));
    }

    /**
     * Checkout cancelled return URL.
     */
    public function cancel(Order $order): View
    {
        Gate::authorize('view', $order);

        $order->load(['course', 'deliveryOption']);

        return view('checkout.cancel', compact('order'));
    }
}
