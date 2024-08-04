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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('uuid')->nullable()->index();
            $table->unsignedBigInteger('edition_id')->index()->nullable();
            $table->enum('article_for', ['law', 'economic'])->nullable();
            $table->string('prefix')->nullable();
            $table->string('title')->nullable()->index();
            $table->string('subtitle')->nullable();
            $table->enum('section', ['general_article', 'article'])->default('article')->index();
            $table->enum('status', ['incomplete', 'submission', 'review', 'production'])->default('incomplete')->index();
            $table->text('comments_for_editor')->nullable();
            $table->longText('abstract')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('slug')->nullable()->index();
            $table->unsignedBigInteger('viewed')->index()->default(0);
            $table->softDeletes()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
