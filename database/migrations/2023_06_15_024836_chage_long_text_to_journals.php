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
        if (Schema::hasColumn('journals', 'abstract'))
        {
            Schema::table('journals', function (Blueprint $table)
            {
                $table->dropColumn('abstract');
            });
        }
        
        Schema::table('journals', function (Blueprint $table) {
            $table->longText('abstract');
            $table->longText('abstrak');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            
            $table->dropColumn('abstract');
            $table->dropColumn('abstrak');
        });
    }
};
