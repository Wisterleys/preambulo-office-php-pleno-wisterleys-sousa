<?php

namespace Tests\Unit\DAOs;

use App\Models\Filme;
use App\Repositories\DAOs\FilmeDAO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class FilmeDAOTest extends TestCase
{
    use RefreshDatabase;

    protected FilmeDAO $filmeDAO;

    protected function setUp(): void
    {
        parent::setUp();
        $this->filmeDAO = new FilmeDAO(new Filme());
    }

    public function testFilmeDAOAllReturnsPaginatedFilmesOrderedByTitulo()
    {
        Filme::factory()->create(['titulo' => 'Z Filme']);
        Filme::factory()->create(['titulo' => 'A Filme']);
        Filme::factory()->create(['titulo' => 'M Filme']);

        $filmes = $this->filmeDAO->all();

        $this->assertEquals(3, $filmes->total());
        $this->assertEquals(20, $filmes->perPage());
        $this->assertEquals('A Filme', $filmes->first()->titulo);
        $this->assertEquals('Z Filme', $filmes->last()->titulo);
    }

    public function testFilmeDAOFindByUuidReturnsFilme()
    {
        $filme = Filme::factory()->create();

        $found = $this->filmeDAO->findByUuid($filme->uuid);

        $this->assertInstanceOf(Filme::class, $found);
        $this->assertEquals($filme->uuid, $found->uuid);
        $this->assertEquals($filme->titulo, $found->titulo);
    }

    public function testFilmeDAOFindByUuidReturnsNullWhenNotFound()
    {
        $result = $this->filmeDAO->findByUuid('non-existent-uuid');

        $this->assertNull($result);
    }

    public function testFilmeDAOCreateSavesNewFilme()
    {
        $filmeData = [
            'titulo' => 'O Poderoso Chefão',
            'sinopse' => 'Uma família mafiosa luta para estabelecer sua supremacia nos Estados Unidos depois da Segunda Guerra Mundial.',
            'ano' => 1972,
            'categoria' => 'Drama',
            'valor_locacao' => 15.50,
            'quantidade_disponivel' => 5,
            'imagem_path' => 'images/poderoso-chefao.jpg'
        ];

        $created = $this->filmeDAO->create($filmeData);

        $this->assertInstanceOf(Filme::class, $created);
        $this->assertNotNull($created->uuid);
        $this->assertEquals('O Poderoso Chefão', $created->titulo);
        $this->assertEquals(1972, $created->ano);
        $this->assertEquals(15.50, $created->valor_locacao);
    }

    public function testFilmeDAOUpdateModifiesExistingFilme()
    {
        $filme = Filme::factory()->create(['valor_locacao' => 10.00]);
        $updateData = ['valor_locacao' => 12.50];

        $updated = $this->filmeDAO->update($filme->uuid, $updateData);

        $this->assertInstanceOf(Filme::class, $updated);
        $this->assertEquals(12.50, $updated->valor_locacao);
        $this->assertDatabaseHas('filmes', [
            'uuid' => $filme->uuid,
            'valor_locacao' => 12.50
        ]);
    }

    public function testFilmeDAOUpdateThrowsExceptionWhenUuidNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Filme não encontrado');

        $this->filmeDAO->update('non-existent-uuid', ['titulo' => 'Novo Título']);
    }

    public function testFilmeDAODeleteRemovesFilme()
    {
        $filme = Filme::factory()->create();

        $result = $this->filmeDAO->delete($filme->uuid);

        $this->assertTrue($result);
        $this->assertSoftDeleted('filmes', ['uuid' => $filme->uuid]);
    }

    public function testFilmeDAODeleteThrowsExceptionWhenUuidNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Filme não encontrado');

        $this->filmeDAO->delete('non-existent-uuid');
    }

    public function testFilmeDAOFindByCategoriaReturnsPaginatedFilmes()
    {
        Filme::factory()->count(5)->create(['categoria' => 'Ação']);
        Filme::factory()->count(3)->create(['categoria' => 'Drama']);
        Filme::factory()->count(2)->create(['categoria' => 'Comédia']);

        $filmesAcao = $this->filmeDAO->findByCategoria('Ação');

        $this->assertEquals(5, $filmesAcao->total());
        $this->assertTrue($filmesAcao->every(function ($filme) {
            return $filme->categoria === 'Ação';
        }));
        $this->assertEquals(20, $filmesAcao->perPage());
    }

    public function testFilmeDAOFindByCategoriaReturnsEmptyWhenNoResults()
    {
        $result = $this->filmeDAO->findByCategoria('Ficção Científica');

        $this->assertEquals(0, $result->total());
    }

    public function testFilmeDAOFindDisponiveisReturnsOnlyAvailableFilmes()
    {
        Filme::factory()->count(4)->create(['quantidade_disponivel' => 3]);
        Filme::factory()->count(2)->create(['quantidade_disponivel' => 0]);
        Filme::factory()->create(['quantidade_disponivel' => 1]);

        $disponiveis = $this->filmeDAO->findDisponiveis();

        $this->assertEquals(5, $disponiveis->total());
        $this->assertTrue($disponiveis->every(function ($filme) {
            return $filme->quantidade_disponivel > 0;
        }));
    }

    public function testFilmeDAOFindDisponiveisReturnsEmptyWhenNoneAvailable()
    {
        Filme::factory()->count(3)->create(['quantidade_disponivel' => 0]);

        $result = $this->filmeDAO->findDisponiveis();

        $this->assertEquals(0, $result->total());
    }

    public function testFilmeDAOGetCatalogoReturnsCachedCollection()
    {
        Cache::tags(['filmes', 'catalogo'])->flush();
        
        Filme::factory()->create([
            'titulo' => 'Filme A',
            'sinopse' => 'Sinopse A',
            'ano' => 2020,
            'categoria' => 'Ação',
            'valor_locacao' => 10.00,
            'quantidade_disponivel' => 5,
            'imagem_path' => 'path/a.jpg'
        ]);
        
        Filme::factory()->create([
            'titulo' => 'Filme B',
            'sinopse' => 'Sinopse B', 
            'ano' => 2021,
            'categoria' => 'Drama',
            'valor_locacao' => 12.00,
            'quantidade_disponivel' => 3,
            'imagem_path' => 'path/b.jpg'
        ]);

        $catalogo = $this->filmeDAO->getCatalogo();

        $this->assertCount(2, $catalogo);
        $this->assertEquals('Filme A', $catalogo->first()->titulo);
        $this->assertEquals('Filme B', $catalogo->last()->titulo);
        
        // Verifica se está em cache
        $this->assertTrue(Cache::tags(['filmes', 'catalogo'])->has('catalogo_completo'));
        
        // Verifica se tem apenas os campos selecionados
        $firstItem = $catalogo->first();
        $this->assertArrayHasKey('uuid', $firstItem->toArray());
        $this->assertArrayHasKey('titulo', $firstItem->toArray());
        $this->assertArrayHasKey('sinopse', $firstItem->toArray());
        $this->assertArrayHasKey('ano', $firstItem->toArray());
        $this->assertArrayHasKey('categoria', $firstItem->toArray());
        $this->assertArrayHasKey('valor_locacao', $firstItem->toArray());
        $this->assertArrayHasKey('quantidade_disponivel', $firstItem->toArray());
        $this->assertArrayHasKey('imagem_path', $firstItem->toArray());
        $this->assertArrayNotHasKey('created_at', $firstItem->toArray());
        $this->assertArrayNotHasKey('updated_at', $firstItem->toArray());
    }

    public function testFilmeDAOGetCatalogoUsesCache()
    {
        Cache::tags(['filmes', 'catalogo'])->flush();
        
        $mockData = collect([
            new Filme([
                'uuid' => 'cached-uuid',
                'titulo' => 'Filme Cache',
                'sinopse' => 'Sinopse Cache',
                'ano' => 2023,
                'categoria' => 'Cache',
                'valor_locacao' => 15.00,
                'quantidade_disponivel' => 10,
                'imagem_path' => 'cache.jpg'
            ])
        ]);

        // Simula que já existe no cache
        Cache::tags(['filmes', 'catalogo'])->put('catalogo_completo', $mockData, 3600);

        $catalogo = $this->filmeDAO->getCatalogo();
        // - Deve retornar os dados do cache, não do banco
        $this->assertCount(1, $catalogo);
        $this->assertEquals('Filme Cache', $catalogo->first()->titulo);
        $this->assertEquals('Cache', $catalogo->first()->categoria);
    }

    public function testFilmeDAOGetCatalogoOrdersByTitulo()
    {
        Cache::tags(['filmes', 'catalogo'])->flush();
        
        Filme::factory()->create(['titulo' => 'Zorro']);
        Filme::factory()->create(['titulo' => 'Avatar']);
        Filme::factory()->create(['titulo' => 'Matrix']);

        $catalogo = $this->filmeDAO->getCatalogo();

        $this->assertEquals('Avatar', $catalogo->first()->titulo);
        $this->assertEquals('Matrix', $catalogo[1]->titulo);
        $this->assertEquals('Zorro', $catalogo->last()->titulo);
    }

    public function testFilmeDAOAllOrdersByTitulo()
    {
        Filme::factory()->create(['titulo' => 'Cidade de Deus']);
        Filme::factory()->create(['titulo' => 'Tropa de Elite']);
        Filme::factory()->create(['titulo' => 'Central do Brasil']);

        $filmes = $this->filmeDAO->all();

        $this->assertEquals('Central do Brasil', $filmes->first()->titulo);
        $this->assertEquals('Cidade de Deus', $filmes[1]->titulo);
        $this->assertEquals('Tropa de Elite', $filmes->last()->titulo);
    }

    public function testFilmeDAOFindByCategoriaOrdersByTitulo()
    {
        Filme::factory()->create(['categoria' => 'Ação', 'titulo' => 'Velozes e Furiosos']);
        Filme::factory()->create(['categoria' => 'Ação', 'titulo' => 'John Wick']);
        Filme::factory()->create(['categoria' => 'Ação', 'titulo' => 'Mad Max']);

        $filmesAcao = $this->filmeDAO->findByCategoria('Ação');

        $this->assertEquals('John Wick', $filmesAcao->first()->titulo);
        $this->assertEquals('Mad Max', $filmesAcao[1]->titulo);
        $this->assertEquals('Velozes e Furiosos', $filmesAcao->last()->titulo);
    }
}