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
            $table->string('title');
            $table->longText('content')->nullable();
            $table->longText('trail_text')->nullable();
            $table->foreignId('source_id')->constrained();
            $table->foreignId('category_id')->nullable()->constrained();
            $table->foreignId('author_id')->nullable()->constrained();
            $table->longText('url')->nullable();
            $table->longText('photo')->nullable();
            $table->timestamp('published_at');
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
