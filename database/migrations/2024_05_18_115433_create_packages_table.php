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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('senders_name');
            $table->string('senders_email');
            $table->string('senders_number');
            $table->string('recievers_name');
            $table->string('recievers_email');
            $table->string('recievers_number');
            $table->string('origin');
            $table->string('destination');
            $table->string('distance');
            $table->string('time_taken');
            $table->string('weight');
            $table->string('description');
            $table->string('tracking_id')->unique();
            $table->enum('status', ['pending', 'in transit', 'delivered'])->default('pending');
            $table->string('price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
