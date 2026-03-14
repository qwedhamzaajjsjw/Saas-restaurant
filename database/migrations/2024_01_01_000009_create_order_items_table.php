<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Individual line items within an order.
 * Prices are snapshotted at time of order — never change if product price changes.
 * Depends on: orders, products
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->cascadeOnDelete();

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->restrictOnDelete();   // prevent deleting a product used in orders

            // ── Snapshot data (frozen at order time) ──────────────────
            $table->string('product_name');                   // snapshot
            $table->decimal('unit_price', 10, 2);             // snapshot
            $table->unsignedInteger('quantity');
            $table->decimal('subtotal', 10, 2);               // unit_price * quantity

            // ── Customisation (selected options, extras) ──────────────
            $table->json('options')->nullable();              // e.g. {"size":"large","extra":"cheese"}
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
