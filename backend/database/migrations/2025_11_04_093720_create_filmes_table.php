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
        Schema::create('filmes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('titulo');
            $table->text('sinopse');
            $table->integer('ano');
            $table->string('categoria');
            $table->decimal('valor_locacao', 10, 2);
            $table->integer('quantidade_disponivel')->default(0);
            $table->string('imagem_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('uuid');
            $table->index('categoria');
            $table->index('ano');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('filmes');
    }
};
