<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('working_paper_id')->constrained()->onDelete('cascade');
            $table->foreignId('rental_property_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('section_type'); // wage, rental, sole_trader, bas, ctax, ttax, smsf
            $table->enum('field_type', ['a', 'b', 'c'])->nullable(); // A/B/C categories
            $table->text('description');
            $table->decimal('amount_inc_gst', 15, 2)->nullable();
            $table->decimal('gst_amount', 15, 2)->nullable();
            $table->decimal('net_ex_gst', 15, 2)->nullable();
            $table->enum('quarter', ['all', 'q1', 'q2', 'q3', 'q4'])->nullable();
            $table->text('client_comment')->nullable();
            $table->text('own_comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_items');
    }
};