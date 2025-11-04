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
        Schema::create('locacao_filme', function (Blueprint $table) {
            $table->id();
            $table->foreignId('locacao_id')->constrained('locacoes')->onDelete('cascade');
            $table->foreignId('filme_id')->constrained('filmes')->onDelete('cascade');
            $table->integer('quantidade')->default(1);
            $table->decimal('valor_unitario', 10, 2);
            $table->timestamps();
            
            $table->index('locacao_id');
            $table->index('filme_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locacao_filme');
    }
};
