<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Subscription plans offered by the SaaS platform.
 * Created before restaurants because restaurants reference a plan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();

            $table->string('name');                          // e.g. Starter, Pro, Enterprise
            $table->string('slug')->unique();                // e.g. starter, pro
            $table->text('description')->nullable();

            // ── Pricing ───────────────────────────────────────────────
            $table->decimal('price', 10, 2)->default(0);    // monthly price
            $table->string('currency', 3)->default('USD');

            // ── Limits ───────────────────────────────────────────────
            $table->unsignedInteger('max_products')->default(50);
            $table->unsignedInteger('max_orders_per_month')->default(500);
            $table->unsignedInteger('max_categories')->default(10);

            // ── Features (JSON array of feature keys) ─────────────────
            $table->json('features')->nullable();            // ["analytics","export","custom_domain"]

            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
