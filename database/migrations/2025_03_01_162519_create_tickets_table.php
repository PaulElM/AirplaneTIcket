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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('passport_id');
            $table->foreignId('source_airport')->constrained('airports')->onDelete('cascade');
            $table->foreignId('destination_airport')->constrained('airports')->onDelete('cascade');
            $table->dateTime('departure_time');
            $table->string('aircraft_number');
            $table->string('seat');
            $table->enum('status', ['booked', 'cancelled'])->default('booked');
            $table->timestamps();

            $table->unique(['aircraft_number', 'departure_time', 'seat']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
