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
        Schema::table('articles', function (Blueprint $table) {
            if (Schema::hasColumn('articles', 'editor_id')) {
                $table->dropColumn('editor_id');
            }
            if (Schema::hasColumn('articles', 'assigned_on')) {
                $table->dropColumn('assigned_on');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'editor_id')) {
                $table->foreignId('editor_id')->constrained('users')->onDelete('cascade');
            }
            if (!Schema::hasColumn('articles', 'assigned_on')) {
                $table->timestamp('assigned_on')->nullable();
            }
        });
    }
};
