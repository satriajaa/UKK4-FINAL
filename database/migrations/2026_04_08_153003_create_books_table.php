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
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('title', 200);
            $table->string('author', 150);
            $table->string('publisher', 150);
            $table->year('publication_year');
            $table->string('isbn', 30)->nullable();
            $table->integer('stock')->default(0);
            $table->string('shelf_location', 50)->nullable();
            $table->string('cover', 255)->nullable();
            $table->boolean('is_available')->default(true);
            $table->text('synopsis')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('school_id');
            $table->index(['school_id', 'isbn']);
            $table->index('title');
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
