<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Customer orders placed at a specific restaurant.
 * Depends on: restaurants, users
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // ── Tenant ────────────────────────────────────────────────
            $table->foreignId('restaurant_id')
                  ->constrained('restaurants')
                  ->cascadeOnDelete();

            // ── Customer (nullable to allow guest checkout) ───────────
            $table->foreignId('customer_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // ── Customer info (snapshot in case user deletes account) ─
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone', 20)->nullable();

            // ── Order number (human-readable) ─────────────────────────
            $table->string('order_number')->unique();         // e.g. ORD-2024-00001

            // ── Type ──────────────────────────────────────────────────
            $table->enum('type', ['delivery', 'pickup', 'dine_in'])->default('pickup');

            // ── Delivery address (for delivery orders) ────────────────
            $table->string('delivery_address')->nullable();
            $table->string('delivery_city')->nullable();
            $table->string('delivery_notes')->nullable();

            // ── Financials ────────────────────────────────────────────
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('delivery_fee', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);

            // ── Payment ───────────────────────────────────────────────
            $table->enum('payment_method', ['cash', 'card', 'online'])->default('cash');
            $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending');

            // ── Order Status ──────────────────────────────────────────
            $table->enum('status', [
                'pending',      // just placed
                'confirmed',    // restaurant accepted
                'preparing',    // kitchen working on it
                'ready',        // ready for pickup/delivery
                'delivered',    // handed to customer
                'cancelled',    // cancelled
            ])->default('pending');

            // ── Notes ─────────────────────────────────────────────────
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // ── Indexes ───────────────────────────────────────────────
            $table->index(['restaurant_id', 'status']);
            $table->index(['restaurant_id', 'created_at']);
            $table->index('customer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
