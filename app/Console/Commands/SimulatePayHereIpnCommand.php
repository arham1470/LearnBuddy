<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\Payment;
use App\Services\Payments\Gateways\PayHereGateway;
use App\Services\Payments\PaymentService;
use Illuminate\Console\Command;

class SimulatePayHereIpnCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payhere:simulate-ipn
                            {order? : Order number (e.g. ORD-20261115-XXXX) or ID. Defaults to latest pending order}
                            {--status=2 : PayHere status code (2 = Success, -2 = Failed, -1 = Cancelled)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate an incoming PayHere IPN webhook to verify signatures, settle orders, and activate enrollments';

    public function handle(PaymentService $paymentService): int
    {
        $orderInput = $this->argument('order');
        $statusCode = (string) $this->option('status');

        $query = Order::query()->with(['user', 'course', 'deliveryOption']);

        if ($orderInput) {
            $order = is_numeric($orderInput)
                ? $query->where('id', $orderInput)->first()
                : $query->where('order_number', $orderInput)->first();
        } else {
            $order = $query->where('status', Order::STATUS_PENDING)->latest()->first();
        }

        if (! $order) {
            $this->error('No pending order found to simulate.');
            $this->line('You can create a pending order by going through checkout on http://127.0.0.1:8000/courses');

            return self::FAILURE;
        }

        $this->info("Simulating PayHere IPN for Order: [{$order->order_number}]");
        $this->line("Customer: {$order->user->name} ({$order->user->email})");
        $this->line("Course: {$order->course->title}");
        $this->line("Amount: {$order->currency} {$order->formattedAmount()}");

        $merchantId = (string) config('payments.gateways.payhere.merchant_id', '1200001');
        $merchantSecret = (string) config('payments.gateways.payhere.merchant_secret', 'secret');

        $formattedAmount = number_format((float) $order->amount, 2, '.', '');
        $gateway = new PayHereGateway();
        $md5sig = $gateway->generateIpnHash(
            merchantId: $merchantId,
            orderId: $order->order_number,
            amount: $formattedAmount,
            currency: $order->currency,
            statusCode: $statusCode,
            merchantSecret: $merchantSecret
        );

        $fakePaymentId = 'PH-SIM-'.mt_rand(100000, 999999);

        $payload = [
            'merchant_id' => $merchantId,
            'order_id' => $order->order_number,
            'payment_id' => $fakePaymentId,
            'payhere_amount' => $formattedAmount,
            'payhere_currency' => $order->currency,
            'status_code' => $statusCode,
            'md5sig' => $md5sig,
            'custom_1' => (string) $order->id,
            'custom_2' => (string) $order->user_id,
            'status_message' => $statusCode === '2' ? 'Successfully completed.' : 'Transaction cancelled or failed.',
            'method' => 'TEST_VISA',
            'card_holder_name' => $order->billing_name ?: $order->user->name,
            'card_no' => '************4242',
            'card_expiry' => '12/28',
        ];

        $this->line('Posting simulated payload to PaymentService...');

        $result = $paymentService->processWebhook(
            payload: $payload,
            gatewayName: 'payhere',
            ipAddress: '127.0.0.1',
            userAgent: 'PayHere-IPN-Simulator/1.0'
        );

        if ($result['status'] === 'rejected' || $result['code'] >= 400) {
            $this->error("Simulation rejected: [{$result['code']}] {$result['message']}");

            return self::FAILURE;
        }

        $order->refresh();

        $this->newLine();
        $this->info('=== SIMULATION RESULTS ===');
        $this->line("Order Status: <fg=green>{$order->status}</>");

        $payment = Payment::where('order_id', $order->id)->latest()->first();
        if ($payment) {
            $this->line("Payment Status: <fg=green>{$payment->status}</>");
            $this->line("Payment Receipt: <fg=yellow>{$payment->receipt_number}</>");
            $this->line("Transaction Ref: {$payment->transaction_reference}");
        }

        if ($order->enrollment) {
            $this->line("Enrollment Code: <fg=green>{$order->enrollment->enrollment_code}</>");
            $this->line("Enrollment Status: <fg=green>{$order->enrollment->status}</>");
        }

        $this->newLine();
        $this->info("You can now refresh http://127.0.0.1:8000/orders/{$order->order_number}/success or check http://127.0.0.1:8000/my-receipts");

        return self::SUCCESS;
    }
}
