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
            $table->enum('announcement_for', ['law', 'economic'])->nullable();
            $table->string('slug')->index()->nullable();
            $table->string('title')->nullable();
            $table->date('submission_deadline_date')->nullable();
            $table->date('published_date')->index()->nullable();
            $table->longText('description')->nullable();
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
