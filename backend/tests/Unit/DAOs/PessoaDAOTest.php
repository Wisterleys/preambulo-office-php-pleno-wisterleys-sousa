<?php

namespace Tests\Unit\DAOs;

use App\Models\Pessoa;
use App\Models\User;
use App\Repositories\DAOs\PessoaDAO;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PessoaDAOTest extends TestCase
{
    use RefreshDatabase;

    protected PessoaDAO $pessoaDAO;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pessoaDAO = new PessoaDAO();
    }

    public function testPessoaDAOAllReturnsPaginatedPessoasWithUsers()
    {
        Pessoa::factory()->count(30)->create();

        $pessoas = $this->pessoaDAO->all();

        $this->assertEquals(15, $pessoas->count());
        $this->assertInstanceOf(Pessoa::class, $pessoas->first());
        $this->assertTrue($pessoas->first()->relationLoaded('user'));
    }

    public function testPessoaDAOFindByUuidReturnsPessoaWithRelations()
    {
        $pessoa = Pessoa::factory()->create();

        $found = $this->pessoaDAO->findByUuid($pessoa->uuid);
        
        $this->assertInstanceOf(Pessoa::class, $found);
        $this->assertEquals($pessoa->uuid, $found->uuid);
        $this->assertTrue($found->relationLoaded('user'));
        $this->assertTrue($found->relationLoaded('locacoes'));
    }

    public function testPessoaDAOFindByUuidReturnsNullWhenNotFound()
    {
        $result = $this->pessoaDAO->findByUuid('non-existent-uuid');

        $this->assertNull($result);
    }

    public function testPessoaDAOFindByUserIdReturnsPessoaWithUser()
    {
        $pessoa = Pessoa::factory()->create();
        
        $found = $this->pessoaDAO->findByUserId($pessoa->user_id);
        
        $this->assertInstanceOf(Pessoa::class, $found);
        $this->assertEquals($pessoa->user_id, $found->user_id);
        $this->assertTrue($found->relationLoaded('user'));
    }

    public function testPessoaDAOFindByUserIdReturnsNullWhenNotFound()
    {
        $result = $this->pessoaDAO->findByUserId(999);
        
        $this->assertNull($result);
    }

    public function testPessoaDAOFindByCpfReturnsPessoa()
    {
        $pessoa = Pessoa::factory()->create(['cpf' => '123.456.789-09']);

        $found = $this->pessoaDAO->findByCpf('123.456.789-09');
        
        $this->assertInstanceOf(Pessoa::class, $found);
        $this->assertEquals($pessoa->cpf, $found->cpf);
    }

    public function testPessoaDAOFindByCpfReturnsNullWhenNotFound()
    {
        $result = $this->pessoaDAO->findByCpf('non-existent-cpf');

        $this->assertNull($result);
    }

    public function testPessoaDAOCreateSavesNewPessoa()
    {
        $user = User::factory()->create();
        $pessoaData = [
            'user_id' => $user->id,
            'nome_completo' => 'Fulano de Tal',
            'cpf' => '123.456.789-09',
            'data_nascimento' => '1990-01-01',
            'telefone' => '(11) 99999-9999',
            'endereco' => 'Rua Teste, 123',
            'role' => 'customer'
        ];
        
        $created = $this->pessoaDAO->create($pessoaData);

        $this->assertInstanceOf(Pessoa::class, $created);
        $this->assertEquals('Fulano de Tal', $created->nome_completo);
        $this->assertNotNull($created->uuid);
    }

    public function testPessoaDAOUpdateModifiesExistingPessoa()
    {
        $pessoa = Pessoa::factory()->create();
        $updateData = ['nome_completo' => 'Novo Nome'];
        
        $updated = $this->pessoaDAO->update($pessoa->uuid, $updateData);
        
        $this->assertInstanceOf(Pessoa::class, $updated);
        $this->assertEquals('Novo Nome', $updated->nome_completo);
        $this->assertDatabaseHas('pessoas', [
            'uuid' => $pessoa->uuid,
            'nome_completo' => 'Novo Nome'
        ]);
    }

    public function testPessoaDAOUpdateThrowsExceptionWhenUuidNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Pessoa não encontrada');
        
        $this->pessoaDAO->update('non-existent-uuid', ['nome' => 'Nome']);
    }

    public function testPessoaDAODeleteRemovesPessoa()
    {
        $pessoa = Pessoa::factory()->create();
        
        $result = $this->pessoaDAO->delete($pessoa->uuid);
        
        $this->assertTrue($result);
        $this->assertSoftDeleted('pessoas', ['uuid' => $pessoa->uuid]);
    }

    public function testPessoaDAODeleteThrowsExceptionWhenUuidNotFound()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Pessoa não encontrada');
        
        $this->pessoaDAO->delete('non-existent-uuid');
    }

    public function testPessoaDAOFindByRoleReturnsPaginatedPessoas()
    {
        Pessoa::factory()->count(10)->create(['role' => 'admin']);
        Pessoa::factory()->count(5)->create(['role' => 'customer']);
        
        $adminPessoas = $this->pessoaDAO->findByRole('admin');

        $this->assertEquals(10, $adminPessoas->total());
        $this->assertTrue($adminPessoas->every(function ($pessoa) {
            return $pessoa->role === 'admin';
        }));
        $this->assertTrue($adminPessoas->first()->relationLoaded('user'));
    }

    public function testPessoaDAOFindByRoleReturnsEmptyWhenNoResults()
    {
        $result = $this->pessoaDAO->findByRole('attendant');
        
        $this->assertEquals(0, $result->total());
    }
}