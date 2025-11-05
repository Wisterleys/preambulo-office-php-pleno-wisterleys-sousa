<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\UserRepositoryInterface;

class PerfilController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function show()
    {
        $user = auth('api')->user();
        $pessoa = $user->pessoa;

        return response()->json([
            'sucesso' => true,
            'dados' => [
                'user' => $user,
                'pessoa' => $pessoa,
            ]
        ]);
    }

    public function update(Request $request)
    {
        $user = auth('api')->user();
        $updatedUser = $this->userRepository->update($user->id, $request->all());

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Perfil atualizado com sucesso',
            'dados' => $updatedUser
        ]);
    }
}
