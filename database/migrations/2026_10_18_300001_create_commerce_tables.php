<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Plans, payments and subscriptions (BLUEPRINT §5.2 and §7). Money is stored in minor units
 * (pesewas) as integers. The unique keys are the safety net for idempotency:
 * payments.reference, payments.provider_transaction_id, payment_webhook_events.dedupe_key,
 * subscriptions.payment_id, coupon_redemptions.payment_id and receipts.payment_id.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('slug', 90)->unique();
            $table->string('description', 500)->nullable();
            $table->unsignedTinyInteger('duration_months');
            $table->unsignedInteger('price_minor');
            $table->char('currency', 3)->default('GHS');
            $table->json('benefits')->nullable();
            $table->string('entitlement_key', 40)->default('premium_standard');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->string('description', 255)->nullable();
            // percent: value is 1–100; fixed: value is an amount in minor units.
            $table->string('type', 10);
            $table->unsignedInteger('value');
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->unsignedInteger('max_redemptions')->nullable();
            $table->unsignedSmallInteger('per_user_limit')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->ulid('ulid')->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('plan_id')->constrained()->restrictOnDelete();
            $table->string('reference', 60)->unique();
            $table->string('provider', 20);
            // What we expect to be charged; verification must match this exactly.
            $table->unsignedInteger('amount_minor');
            $table->unsignedInteger('list_price_minor');
            $table->unsignedInteger('discount_minor')->default(0);
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->char('currency', 3);
            // initiated | pending | success | failed | abandoned | refunded
            $table->string('status', 20)->default('initiated');
            $table->string('provider_transaction_id', 60)->nullable()->unique();
            $table->string('channel', 30)->nullable();
            $table->string('customer_email', 150)->nullable();
            $table->string('authorization_url', 255)->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('verified_at')->nullable();
            // callback | webhook | reconcile | admin | coupon
            $table->string('verification_source', 20)->nullable();
            $table->json('provider_payload')->nullable();
            $table->string('failure_reason', 255)->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        Schema::create('payment_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 20);
            $table->string('event_type', 60);
            $table->char('dedupe_key', 64)->unique();
            $table->string('reference', 60)->nullable()->index();
            $table->json('payload');
            $table->dateTime('received_at');
            $table->dateTime('processed_at')->nullable();
            $table->string('error', 255)->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payment_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('entitlement_key', 40)->default('premium_standard');
            // payment | admin_grant | coupon_free
            $table->string('source', 20);
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->dateTime('grace_ends_at')->nullable();
            // active | revoked | refunded (expiry is derived from the dates)
            $table->string('status', 20)->default('active');
            $table->foreignId('granted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reason', 255)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'entitlement_key', 'ends_at']);
            $table->index(['status', 'ends_at']);
        });

        Schema::create('coupon_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_id')->unique()->constrained()->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['coupon_id', 'user_id']);
        });

        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('number', 30)->nullable()->unique();
            $table->dateTime('issued_at');
            $table->timestamps();
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('amount_minor');
            $table->string('provider_reference', 60)->nullable()->unique();
            $table->string('reason', 255);
            $table->boolean('revoked_access')->default(true);
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('receipts');
        Schema::dropIfExists('coupon_redemptions');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('payment_webhook_events');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('plans');
    }
};
