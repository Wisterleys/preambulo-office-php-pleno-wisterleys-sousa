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
        Schema::create('locacoes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('pessoa_id')->constrained('pessoas')->onDelete('cascade');
            $table->date('data_inicio');
            $table->date('data_prevista_devolucao');
            $table->date('data_devolucao')->nullable();
            $table->enum('status', ['ativa', 'devolvida', 'atrasada', 'cancelada'])->default('ativa');
            $table->decimal('valor_total', 10, 2);
            $table->decimal('multa_total', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('uuid');
            $table->index('pessoa_id');
            $table->index('status');
            $table->index('data_inicio');
            $table->index('data_prevista_devolucao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locacoes');
    }
};
