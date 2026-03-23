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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('order_id');
            $table->string('name');
            $table->string('email');
            $table->string('status')->default('pending');
            $table->string('redirect_url')->nullable();
            $table->string('token');
            $table->string('package_name');
            $table->integer('packgae_qty')->default(1);
            $table->integer('total')->default(0);
            $table->string('payment_method');
            $table->json('participants');
            $table->dateTime('checkin');
            $table->dateTime('checkout');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
