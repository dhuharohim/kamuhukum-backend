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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('edition_id')->index()->nullable();
            $table->string('announcement_title')->index()->nullable();     
            $table->date('submission_deadline_date')->index()->nullable();
            $table->date('published_date')->index()->nullable();
            $table->longText('announcement_description')->nullable();
            $table->date('extend_submission_date')->index()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
