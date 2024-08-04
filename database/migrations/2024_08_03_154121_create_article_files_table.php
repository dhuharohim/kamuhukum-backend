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
        Schema::create('article_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_id')->nullable()->index();
            $table->string('file_name')->nullable()->index();
            $table->string('file_path')->nullable();
            $table->enum('type', [
                'Article Text',
                'Plagiarism Report',
                'Research Instrument',
                'Research Materials',
                'Research Result',
                'Transcripts',
                'Data Analysis',
                'Data Set',
                'Source Texts',
                'Other'
            ])->default('Article Text');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_files');
    }
};
