<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('working_papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('financial_year'); // e.g., "2024-25"
            $table->json('selected_types')->nullable(); // ["wage", "rental", "bas", etc.]
            $table->enum('status', ['draft', 'submitted', 'completed'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('working_papers');
    }
};