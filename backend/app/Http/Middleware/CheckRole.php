<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth('api')->check()) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Não autenticado'
            ], 401);
        }

        $user = auth('api')->user();
        $pessoa = $user->pessoa;

        if (!$pessoa) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Perfil de pessoa não encontrado'
            ], 403);
        }

        if (!in_array($pessoa->role, $roles)) {
            return response()->json([
                'sucesso' => false,
                'mensagem' => 'Acesso negado. Você não tem permissão para acessar este recurso'
            ], 403);
        }

        return $next($request);
    }
}
