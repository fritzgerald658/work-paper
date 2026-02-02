<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wage_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('working_paper_id')->constrained()->onDelete('cascade');
            $table->decimal('salary_wages', 15, 2)->nullable();
            $table->decimal('tax_withheld', 15, 2)->nullable();
            $table->text('other_employment_items')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wage_data');
    }
};