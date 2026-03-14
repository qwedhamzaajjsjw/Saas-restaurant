<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menu items / products sold by a restaurant.
 * Depends on: restaurants, categories
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // ── Tenant + category ─────────────────────────────────────
            $table->foreignId('restaurant_id')
                  ->constrained('restaurants')
                  ->cascadeOnDelete();

            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->cascadeOnDelete();

            // ── Identity ──────────────────────────────────────────────
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            // ── Pricing ───────────────────────────────────────────────
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();  // optional discounted price

            // ── Stock & Availability ──────────────────────────────────
            $table->boolean('is_available')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('preparation_time')->default(15); // minutes

            // ── Metadata ──────────────────────────────────────────────
            $table->json('options')->nullable();               // e.g. sizes, extras
            $table->unsignedInteger('calories')->nullable();
            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // ── Indexes ───────────────────────────────────────────────
            $table->unique(['restaurant_id', 'slug']);
            $table->index(['restaurant_id', 'category_id', 'is_available']);
            $table->index(['restaurant_id', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
