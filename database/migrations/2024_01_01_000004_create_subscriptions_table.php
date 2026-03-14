<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tracks which plan each restaurant is subscribed to and for how long.
 * Depends on: restaurants, plans
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('restaurant_id')
                  ->constrained('restaurants')
                  ->cascadeOnDelete();

            $table->foreignId('plan_id')
                  ->constrained('plans')
                  ->restrictOnDelete();

            // ── Period ────────────────────────────────────────────────
            $table->timestamp('starts_at');
            $table->timestamp('ends_at')->nullable();        // NULL = lifetime / no expiry

            // ── Status ────────────────────────────────────────────────
            $table->enum('status', ['active', 'expired', 'cancelled', 'trial'])
                  ->default('trial');

            // ── Payment reference (optional, for future payment gateway) ─
            $table->string('payment_reference')->nullable();
            $table->decimal('amount_paid', 10, 2)->default(0);

            $table->timestamps();

            $table->index(['restaurant_id', 'status']);
            $table->index('ends_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
