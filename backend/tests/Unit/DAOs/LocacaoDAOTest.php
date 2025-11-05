<?php

namespace Tests\Unit\DAOs;

use App\Models\Locacao;
use App\Models\Pessoa;
use App\Models\Filme;
use App\Repositories\DAOs\LocacaoDAO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocacaoDAOTest extends TestCase
{
    use RefreshDatabase;

    protected LocacaoDAO $locacaoDAO;

    protected function setUp(): void
    {
        parent::setUp();
        $this->locacaoDAO = new LocacaoDAO();
    }

    public function testLocacaoDAOAllReturnsPaginatedLocacoesWithRelations()
    {
        Locacao::factory()->count(20)->create();

        $locacoes = $this->locacaoDAO->all();

        $this->assertEquals(15, $locacoes->count());
        $this->assertInstanceOf(Locacao::class, $locacoes->first());
        $this->assertTrue($locacoes->first()->relationLoaded('pessoa'));
        $this->assertTrue($locacoes->first()->relationLoaded('filmes'));
    }

    public function testLocacaoDAOFindByUuidReturnsLocacaoWithRelations()
    {
        $locacao = Locacao::factory()->create();

        $found = $this->locacaoDAO->findByUuid($locacao->uuid);

        $this->assertInstanceOf(Locacao::class, $found);
        $this->assertEquals($locacao->uuid, $found->uuid);
        $this->assertTrue($found->relationLoaded('pessoa'));
        $this->assertTrue($found->relationLoaded('filmes'));
    }

    public function testLocacaoDAOFindByUuidReturnsNullWhenNotFound()
    {
        $result = $this->locacaoDAO->findByUuid('non-existent-uuid');

        $this->assertNull($result);
    }

    public function testLocacaoDAOCreateSavesNewLocacao()
    {
        $pessoa = Pessoa::factory()->create();
        $filmes = Filme::factory()->count(2)->create();
        
        $locacaoData = [
            'pessoa_id' => $pessoa->id,
            'data_inicio' => now()->format('Y-m-d'),
            'data_prevista_devolucao' => now()->addDays(7)->format('Y-m-d'),
            'valor_total' => 50.00,
            'status' => 'ativa'
        ];

        $created = $this->locacaoDAO->create($locacaoData);

        $this->assertInstanceOf(Locacao::class, $created);
        $this->assertNotNull($created->uuid);
        $this->assertEquals(50.00, $created->valor_total);
        $this->assertEquals('ativa', $created->status);
    }

    public function testLocacaoDAOUpdateModifiesExistingLocacao()
    {
        $locacao = Locacao::factory()->create(['valor_total' => 30.00]);
        $updateData = ['valor_total' => 45.00];

        $updated = $this->locacaoDAO->update($locacao->uuid, $updateData);

        $this->assertInstanceOf(Locacao::class, $updated);
        $this->assertEquals(45.00, $updated->valor_total);
        $this->assertDatabaseHas('locacoes', [
            'uuid' => $locacao->uuid,
            'valor_total' => 45.00
        ]);
    }

    public function testLocacaoDAOUpdateThrowsExceptionWhenUuidNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Locação não encontrada');

        $this->locacaoDAO->update('non-existent-uuid', ['valor_total' => 100.00]);
    }

    public function testLocacaoDAODeleteRemovesLocacao()
    {
        $locacao = Locacao::factory()->create();

        $result = $this->locacaoDAO->delete($locacao->uuid);

        $this->assertTrue($result);
        $this->assertSoftDeleted('locacoes', ['uuid' => $locacao->uuid]);
    }

    public function testLocacaoDAODeleteThrowsExceptionWhenUuidNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Locação não encontrada');

        $this->locacaoDAO->delete('non-existent-uuid');
    }

    public function testLocacaoDAOFindByPessoaReturnsPaginatedLocacoes()
    {
        $pessoa = Pessoa::factory()->create();
        Locacao::factory()->count(5)->create(['pessoa_id' => $pessoa->id]);
        Locacao::factory()->count(3)->create(); // outras locações

        $locacoesPessoa = $this->locacaoDAO->findByPessoa($pessoa->uuid);

        $this->assertEquals(5, $locacoesPessoa->total());
        $this->assertTrue($locacoesPessoa->every(function ($locacao) use ($pessoa) {
            return $locacao->pessoa_id === $pessoa->id;
        }));
        $this->assertTrue($locacoesPessoa->first()->relationLoaded('filmes'));
    }

    public function testLocacaoDAOFindByPessoaReturnsEmptyWhenNoResults()
    {
        $pessoa = Pessoa::factory()->create();

        $result = $this->locacaoDAO->findByPessoa($pessoa->uuid);

        $this->assertEquals(0, $result->total());
    }

    public function testLocacaoDAOFindAtivasReturnsCollectionWithRelations()
    {
        Locacao::factory()->count(3)->create(['status' => 'ativa']);
        Locacao::factory()->count(2)->create(['status' => 'devolvida']);

        $ativas = $this->locacaoDAO->findAtivas();

        $this->assertCount(3, $ativas);
        $this->assertTrue($ativas->every(function ($locacao) {
            return $locacao->status === 'ativa';
        }));
        $this->assertTrue($ativas->first()->relationLoaded('pessoa'));
        $this->assertTrue($ativas->first()->relationLoaded('filmes'));
    }

    public function testLocacaoDAOFindAtrasadasReturnsCollectionWithRelations()
    {
        Locacao::factory()->count(4)->create(['status' => 'atrasada']);
        Locacao::factory()->count(2)->create(['status' => 'ativa']);

        $atrasadas = $this->locacaoDAO->findAtrasadas();

        $this->assertCount(4, $atrasadas);
        $this->assertTrue($atrasadas->every(function ($locacao) {
            return $locacao->status === 'atrasada';
        }));
        $this->assertTrue($atrasadas->first()->relationLoaded('pessoa'));
        $this->assertTrue($atrasadas->first()->relationLoaded('filmes'));
    }

    public function testLocacaoDAODevolverUpdatesStatusToDevolvida()
    {
        $locacao = Locacao::factory()->create([
            'status' => 'ativa',
            'data_devolucao' => null
        ]);

        $devolvida = $this->locacaoDAO->devolver($locacao->uuid);

        $this->assertInstanceOf(Locacao::class, $devolvida);
        $this->assertEquals('devolvida', $devolvida->status);
        $this->assertNotNull($devolvida->data_devolucao);
    }

    public function testLocacaoDAODevolverThrowsExceptionWhenUuidNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Locação não encontrada');

        $this->locacaoDAO->devolver('non-existent-uuid');
    }

    public function testLocacaoDAODevolverThrowsExceptionWhenAlreadyDevolvida()
    {
        $locacao = Locacao::factory()->create([
            'status' => 'devolvida',
            'data_devolucao' => now()
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Locação já foi devolvida');

        $this->locacaoDAO->devolver($locacao->uuid);
    }

    public function testLocacaoDAOFindAtivasOrdersByDataPrevistaDevolucao()
    {
        $locacao1 = Locacao::factory()->create([
            'status' => 'ativa',
            'data_prevista_devolucao' => now()->addDays(3)
        ]);
        $locacao2 = Locacao::factory()->create([
            'status' => 'ativa', 
            'data_prevista_devolucao' => now()->addDays(1)
        ]);
        $locacao3 = Locacao::factory()->create([
            'status' => 'ativa',
            'data_prevista_devolucao' => now()->addDays(5)
        ]);

        $ativas = $this->locacaoDAO->findAtivas();

        $this->assertEquals($locacao2->id, $ativas->first()->id); // Deve vir primeiro a com menor data
        $this->assertEquals($locacao3->id, $ativas->last()->id); // Deve vir por último a com maior data
    }

    public function testLocacaoDAOFindAtrasadasOrdersByDataPrevistaDevolucao()
    {
        $locacao1 = Locacao::factory()->create([
            'status' => 'atrasada',
            'data_prevista_devolucao' => now()->subDays(5)
        ]);
        $locacao2 = Locacao::factory()->create([
            'status' => 'atrasada',
            'data_prevista_devolucao' => now()->subDays(2)
        ]);

        $atrasadas = $this->locacaoDAO->findAtrasadas();

        $this->assertEquals($locacao1->id, $atrasadas->first()->id); // Mais antiga primeiro
        $this->assertEquals($locacao2->id, $atrasadas->last()->id); // Menos antiga por último
    }
}