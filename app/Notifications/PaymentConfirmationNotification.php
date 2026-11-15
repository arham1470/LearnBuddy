<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\Payment;
use App\Models\UserNotificationPreference;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 60, 180];

    public function __construct(
        public Order $order,
        public Payment $payment
    ) {}

    public function via(object $notifiable): array
    {
        $prefs = UserNotificationPreference::forUser($notifiable);
        $channels = [];

        if ($prefs->canReceive('payment', 'database')) {
            $channels[] = 'database';
        }

        if ($prefs->canReceive('payment', 'mail')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Payment Receipt: {$this->order->formattedAmount()} · LearnBuddy")
            ->greeting("Hello, {$notifiable->name}!")
            ->line("We have received your payment of **{$this->order->formattedAmount()}** for **{$this->order->course->title}**.")
            ->line("Order Number: **{$this->order->order_number}**")
            ->line("Receipt Number: **{$this->payment->receipt_number}**")
            ->action('View Official Tax Receipt', route('receipts.show', $this->order))
            ->line('Your payment was securely verified via PayHere.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_confirmation',
            'title' => 'Payment Successful',
            'body' => "Payment of {$this->order->formattedAmount()} confirmed for {$this->order->course->title}.",
            'icon' => 'fas fa-receipt',
            'action_url' => route('receipts.show', $this->order),
            'metadata' => [
                'order_number' => $this->order->order_number,
                'receipt_number' => $this->payment->receipt_number,
                'amount' => (float) $this->payment->amount,
                'currency' => $this->payment->currency,
            ],
        ];
    }
}
