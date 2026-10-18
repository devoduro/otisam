<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One-time SMS codes for sign-up verification and password reset. Only a hash of the
        // code is stored.
        Schema::create('phone_verifications', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 15);
            $table->string('purpose', 20);
            $table->string('code_hash');
            $table->unsignedTinyInteger('attempts')->default(0);
            // DATETIME, not TIMESTAMP: MariaDB adds ON UPDATE CURRENT_TIMESTAMP to the first non-null
            // TIMESTAMP column, which would reset the expiry whenever attempts is incremented.
            $table->dateTime('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['phone', 'purpose', 'created_at']);
        });

        Schema::create('login_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('identifier_hash', 64)->nullable();
            $table->boolean('success');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });

        // Insert-only trail of who changed what (jhs_portal's activity_logs, plus old/new values
        // for domain events such as approvals and role changes).
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('category', 40)->index();
            $table->string('action', 80);
            $table->string('description', 500);
            $table->nullableMorphs('auditable');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('route_name')->nullable();
            $table->string('method', 10)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
        });

        Schema::create('notification_messages', function (Blueprint $table) {
            $table->id();
            $table->string('idempotency_key')->nullable()->unique();
            $table->string('event_key', 80)->index();
            $table->string('category', 20);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel', 10);
            $table->string('recipient');
            $table->string('subject')->nullable();
            $table->text('body');
            $table->string('status', 20)->default('queued')->index();
            $table->string('provider', 20)->nullable();
            $table->string('provider_message_id')->nullable()->index();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->string('last_error', 500)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });

        // Non-secret, admin-editable settings. API keys and passwords live in .env only.
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 40)->default('general');
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('notification_messages');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('login_events');
        Schema::dropIfExists('phone_verifications');
    }
};
