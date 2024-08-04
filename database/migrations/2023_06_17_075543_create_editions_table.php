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
        Schema::create('editions', function (Blueprint $table) {
            $table->id();
            $table->string('name_edition')->nullable()->index();
            $table->enum('edition_for', ['law', 'economic']);
            $table->string('slug')->nullable()->index();
            $table->string('img_path')->nullable();
            $table->integer('volume')->nullable()->index();
            $table->integer('issue')->nullable()->index();
            $table->year('year')->nullable()->index();
            $table->text('description')->nullable();
            $table->timestamp('publish_date')->nullable()->index();
            $table->enum('status', ['Draft', 'Archive', 'Published'])->default('Draft')->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('editions');
    }
};
