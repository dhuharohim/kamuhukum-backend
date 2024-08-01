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
        Schema::table('journals', function (Blueprint $table) {
            $table->string('abstract_two');
            $table->string('slug')->index()->change();
            $table->string('upload_by')->index()->change();
            $table->unsignedBigInteger('category_id')->index()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            $table->dropColumn('abstract_two');
            $table->string('slug')->change();
            $table->string('upload_by')->change();
            $table->unsignedBigInteger('category_id')->change();
        });
    }
};
