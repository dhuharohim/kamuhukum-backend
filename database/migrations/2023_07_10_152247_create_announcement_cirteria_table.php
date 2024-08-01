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
        Schema::create('announcement_cirteria', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('announcement_id')->references('id')->on('announcements');
            $table->string('name_criteria')->nullable();
            $table->softDeletes()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_cirteria');
    }
};
