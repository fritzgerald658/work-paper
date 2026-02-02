<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rental_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('working_paper_id')->constrained()->onDelete('cascade');
            $table->string('address_label'); // Nickname or address
            $table->decimal('ownership_percentage', 5, 2)->nullable(); // 0.00 to 100.00
            $table->string('period_rented')->nullable(); // e.g., "Full Year", "Jan-Jun"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_properties');
    }
};