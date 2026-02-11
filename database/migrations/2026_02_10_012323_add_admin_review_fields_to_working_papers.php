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
        Schema::table('working_papers', function (Blueprint $table) {
            $table->enum('status', ['draft', 'submitted', 'resubmitted', 'approved', 'rejected'])->default('draft')->change();

            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null')->after('submitted_at');
            $table->text('admin_comment')->nullable()->after('reviewed_by');
            $table->timestamp('reviewed_at')->nullable()->after('admin_comment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('working_papers', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['reviewed_by', 'admin_comment', 'reviewed_at']);
            
            $table->enum('status', ['draft', 'submitted', 'completed'])->default('draft')->change();
        });
    }
};
