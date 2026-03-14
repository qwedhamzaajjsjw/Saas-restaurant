<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Core tenant table. Every restaurant is a tenant in the SaaS platform.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();

            // ── Identity ──────────────────────────────────────────────
            $table->string('name');
            $table->string('slug')->unique();               // URL-friendly: /restaurant/{slug}
            $table->text('description')->nullable();

            // ── Branding ──────────────────────────────────────────────
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('primary_color', 7)->default('#f97316'); // Tailwind orange-500

            // ── Contact ───────────────────────────────────────────────
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();

            // ── Plan / Subscription ───────────────────────────────────
            $table->foreignId('plan_id')
                  ->nullable()
                  ->constrained('plans')
                  ->nullOnDelete();

            // ── Status ────────────────────────────────────────────────
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');

            // ── Config ────────────────────────────────────────────────
            $table->string('timezone')->default('UTC');
            $table->string('currency', 3)->default('USD');
            $table->boolean('accepts_orders')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};
