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
            $table->unsignedBigInteger('edition_id')->index()->nullable();
            $table->string('article_title')->nullable()->index();
            $table->string('author')->nullable()->index();
            $table->string('affiliation')->nullable()->index();
            $table->string('country')->nullable()->index();
            $table->json('keywords')->nullable();
            $table->json('abstract')->nullable();
            $table->json('reference')->nullable();
            $table->string('path')->nullable();
            $table->string('slug')->nullable()->index();
            $table->string('year')->nullable()->index();
            $table->softDeletes();
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
