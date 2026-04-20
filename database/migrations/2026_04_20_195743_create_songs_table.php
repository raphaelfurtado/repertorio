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
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repertoire_block_id')->constrained()->cascadeOnDelete();
            $table->string('artist_name');
            $table->string('title');
            $table->string('key_tonic', 3)->nullable();
            $table->enum('key_mode', ['major', 'minor'])->nullable();
            $table->unsignedSmallInteger('bpm')->nullable();
            $table->text('intro_excerpt')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['repertoire_block_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
