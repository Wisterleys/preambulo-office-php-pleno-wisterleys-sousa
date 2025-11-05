<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\PessoaRepositoryInterface;

class PessoaController extends Controller
{
    protected $pessoaRepository;

    public function __construct(PessoaRepositoryInterface $pessoaRepository)
    {
        $this->pessoaRepository = $pessoaRepository;
    }

    public function index()
    {
        $pessoas = $this->pessoaRepository->all();
        return response()->json(['sucesso' => true, 'dados' => $pessoas]);
    }

    public function store(Request $request)
    {
        $pessoa = $this->pessoaRepository->create($request->all());
        return response()->json(['sucesso' => true, 'dados' => $pessoa], 201);
    }

    public function show(string $id)
    {
        $pessoa = $this->pessoaRepository->find($id);
        return response()->json(['sucesso' => true, 'dados' => $pessoa]);
    }

    public function update(Request $request, string $id)
    {
        $pessoa = $this->pessoaRepository->update($id, $request->all());
        return response()->json(['sucesso' => true, 'dados' => $pessoa]);
    }

    public function destroy(string $id)
    {
        $this->pessoaRepository->delete($id);
        return response()->json(['sucesso' => true, 'mensagem' => 'Pessoa excluída com sucesso']);
    }
}
