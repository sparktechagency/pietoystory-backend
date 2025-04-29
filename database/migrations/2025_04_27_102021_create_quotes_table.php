<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('zip_code')->nullable();
            $table->string('how_often')->nullable();
            $table->unsignedInteger('amount_of_dogs')->nullable();
            $table->unsignedInteger('total_area')->nullable();
            $table->string('area_to_clean')->nullable();
            $table->unsignedInteger('cost')->nullable();
            $table->string('payment_intent_id')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
