<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->foreignId('course_delivery_option_id')->constrained('course_delivery_options')->cascadeOnDelete();
            $table->foreignId('course_batch_id')->nullable()->constrained('course_batches')->nullOnDelete();
            $table->foreignId('enrollment_id')->nullable()->constrained('enrollments')->nullOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('LKR');
            $table->string('status', 30)->default('pending'); // pending, completed, failed, cancelled, refunded
            $table->string('billing_name', 150)->nullable();
            $table->string('billing_email', 150)->nullable();
            $table->string('billing_phone', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['course_id', 'status']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('payment_gateway', 50)->default('payhere');
            $table->string('transaction_reference', 100)->unique();
            $table->string('gateway_payment_id', 100)->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('LKR');
            $table->string('status', 30)->default('pending'); // pending, successful, failed, cancelled, refunded, chargedback
            $table->string('payment_method', 50)->nullable();
            $table->string('receipt_number', 50)->nullable()->unique();
            $table->string('gateway_response_code', 50)->nullable();
            $table->text('gateway_response_message')->nullable();
            $table->string('idempotency_key', 100)->unique();
            $table->json('raw_payload')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('refunded_at')->nullable();
            $table->decimal('refund_amount', 12, 2)->nullable();
            $table->text('refund_reason')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('gateway_payment_id');
        });

        Schema::create('payment_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type', 50);
            $table->string('status_before', 30)->nullable();
            $table->string('status_after', 30)->nullable();
            $table->json('payload')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['order_id', 'event_type']);
            $table->index('event_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_audit_logs');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('orders');
    }
};
