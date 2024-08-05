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
        Schema::create('profile_authors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('author_type', ['law', 'economic'])->index();
            $table->string('given_name', 50)->nullable();
            $table->string('family_name', 50)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 20)->nullable();
            $table->string('preferred_name', 50)->nullable();
            $table->string('affilation', 100)->nullable();
            $table->string('country', 50)->nullable();
            $table->string('img_url', 50)->nullable();
            $table->string('homepage_url', 50)->nullable();
            $table->string('orchid_id', 19)->nullable();
            $table->text('mailing_address')->nullable();
            $table->text('bio_statement')->nullable();
            $table->text('reviewing_interest')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_authors');
    }
};
