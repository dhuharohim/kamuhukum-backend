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
        Schema::create('article_contributors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_id')->index();
            $table->enum('contributor_role', ['author', 'translator'])->index();
            $table->string('given_name', 50)->nullable();
            $table->string('family_name', 50)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('contact', 255)->nullable();
            $table->string('preferred_name', 50)->nullable();
            $table->string('affilation', 100)->nullable();
            $table->string('country', 50)->nullable();
            $table->string('img_url', 50)->nullable();
            $table->string('homepage_url', 50)->nullable();
            $table->string('orcid_id', 19)->nullable();
            $table->text('mailing_address')->nullable();
            $table->text('bio_statement')->nullable();
            $table->text('reviewing_interest')->nullable();
            $table->tinyInteger('principal_contact')->default(0);
            $table->tinyInteger('in_browse_list')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_contributors');
    }
};
