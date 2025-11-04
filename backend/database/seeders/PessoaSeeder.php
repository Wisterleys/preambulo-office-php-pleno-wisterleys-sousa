<?php

namespace Database\Seeders;

use App\Models\Pessoa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PessoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Evitar duplicação
        if (User::where('email', 'admin@preambulo.com')->exists()) {
            $this->command->info('Seeders de Pessoa já executados. Ignorando...');
            return;
        }

        $this->command->info('Executando PessoaSeeder...');

        // 1. Criar Admin
        $adminUser = User::create([
            'name' => 'Admin Préambulo',
            'email' => 'admin@preambulo.com',
            'password' => Hash::make('preambulo123'), // Senha padrão: password
        ]);

        Pessoa::create([
            'user_id' => $adminUser->id,
            'nome_completo' => 'Admin Préambulo',
            'cpf' => '000.000.000-00',
            'data_nascimento' => Carbon::create(1980, 1, 1),
            'endereco' => 'Rua Admin, 100, Centro, Cidade Fictícia',
            'role' => 'admin',
        ]);

        $this->command->info('Usuário Admin criado: admin@preambulo.com / password');

        // 2. Criar Atendente
        $attendantUser = User::create([
            'name' => 'Atendente Préambulo',
            'email' => 'atendente@preambulo.com',
            'password' => Hash::make('preambulo123'), // Senha padrão: password
        ]);

        Pessoa::create([
            'user_id' => $attendantUser->id,
            'nome_completo' => 'Atendente Préambulo',
            'cpf' => '111.111.111-11',
            'data_nascimento' => Carbon::create(1990, 5, 15),
            'endereco' => 'Rua Atendente, 200, Centro, Cidade Fictícia',
            'role' => 'attendant',
        ]);

        $this->command->info('Usuário Atendente criado: atendente@preambulo.com / password');

        // 3. Criar Cliente
        $customerUser = User::create([
            'name' => 'Cliente Préambulo',
            'email' => 'cliente@preambulo.com',
            'password' => Hash::make('preambulo123'), // Senha padrão: password
        ]);

        Pessoa::create([
            'user_id' => $customerUser->id,
            'nome_completo' => 'Cliente Préambulo',
            'cpf' => '222.222.222-22',
            'data_nascimento' => Carbon::create(2000, 10, 20),
            'endereco' => 'Rua Cliente, 300, Bairro, Cidade Fictícia',
            'role' => 'customer',
        ]);

        $this->command->info('Usuário Cliente criado: cliente@preambulo.com / password');
    }
}
