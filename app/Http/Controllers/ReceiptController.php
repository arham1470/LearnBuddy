<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReceiptController extends Controller
{
    /**
     * Display a paginated list of student's payment history and orders.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();

        $orders = Order::where('user_id', $user->id)
            ->with(['course', 'deliveryOption', 'latestPayment'])
            ->latest()
            ->paginate(15);

        return view('receipts.index', compact('orders'));
    }

    /**
     * Display a detailed, printable transaction receipt for a completed/paid order.
     */
    public function show(Order $order): View
    {
        Gate::authorize('view', $order);

        $order->load([
            'course.category',
            'deliveryOption',
            'batch.location',
            'user',
            'payments' => fn ($q) => $q->latest(),
            'latestPayment',
        ]);

        return view('receipts.show', compact('order'));
    }
}
