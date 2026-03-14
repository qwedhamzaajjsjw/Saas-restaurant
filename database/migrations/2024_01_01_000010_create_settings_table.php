<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Key-value settings store — supports both system-wide (restaurant_id = NULL)
 * and per-restaurant settings.
 * Depends on: restaurants
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();

            // NULL = system/global setting
            // integer = per-restaurant setting
            $table->foreignId('restaurant_id')
                  ->nullable()
                  ->constrained('restaurants')
                  ->cascadeOnDelete();

            $table->string('key');
            $table->longText('value')->nullable();
            $table->string('type')->default('string');   // string | boolean | integer | json

            $table->timestamps();

            // A setting key is unique per scope (global or per restaurant)
            $table->unique(['restaurant_id', 'key']);
            $table->index('key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
