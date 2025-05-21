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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique(); // si tu veux éviter les doublons
            $table->string('author');
            $table->string('category')->nullable();
            $table->text('description')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 8, 2)->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('cover_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
