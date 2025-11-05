<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ], [
            'email.required' => 'O email é obrigatório',
            'email.email' => 'Email inválido',
            'password.required' => 'A senha é obrigatória',
            'password.min' => 'A senha deve ter no mínimo 8 caracteres',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Erro de validação',
                'erros' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Credenciais inválidas'
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    public function me()
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

    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'sucesso' => true,
            'mensagem' => 'Logout realizado com sucesso'
        ]);
    }

    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    protected function respondWithToken($token)
    {
        $user = auth('api')->user();
        $pessoa = $user->pessoa;

        return response()->json([
            'sucesso' => true,
            'token' => $token,
            'tipo_token' => 'bearer',
            'expira_em' => auth('api')->factory()->getTTL() * 60,
            'usuario' => [
                'nome' => $user->name,
                'email' => $user->email,
                'role' => $pessoa->role ?? 'customer',
                'uuid_pessoa' => $pessoa->uuid ?? null,
            ]
        ]);
    }
}
