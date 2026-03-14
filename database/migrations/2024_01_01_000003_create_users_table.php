<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Users table — supports all three roles in one table.
 * Depends on: restaurants
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // ── Profile ───────────────────────────────────────────────
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone', 20)->nullable();
            $table->string('avatar')->nullable();

            // ── Role ──────────────────────────────────────────────────
            $table->enum('role', ['super_admin', 'restaurant_owner', 'customer'])
                  ->default('customer');

            // ── Tenant link ───────────────────────────────────────────
            // NULL for super_admin
            // restaurant_id for restaurant_owner
            // NULL for customer (customers are linked via orders)
            $table->foreignId('restaurant_id')
                  ->nullable()
                  ->constrained('restaurants')
                  ->nullOnDelete();

            // ── Status & Auth ─────────────────────────────────────────
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            // ── Indexes ───────────────────────────────────────────────
            $table->index(['role', 'restaurant_id']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
