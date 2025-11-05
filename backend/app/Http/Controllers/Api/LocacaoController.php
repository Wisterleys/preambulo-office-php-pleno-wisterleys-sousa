<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\LocacaoRepositoryInterface;

class LocacaoController extends Controller
{
    protected $locacaoRepository;

    public function __construct(LocacaoRepositoryInterface $locacaoRepository)
    {
        $this->locacaoRepository = $locacaoRepository;
    }

    public function index()
    {
        $locacoes = $this->locacaoRepository->all();
        return response()->json(['sucesso' => true, 'dados' => $locacoes]);
    }

    public function store(Request $request)
    {
        $locacao = $this->locacaoRepository->create($request->all());
        return response()->json(['sucesso' => true, 'dados' => $locacao], 201);
    }

    public function show(string $id)
    {
        $locacao = $this->locacaoRepository->find($id);
        return response()->json(['sucesso' => true, 'dados' => $locacao]);
    }

    public function update(Request $request, string $id)
    {
        $locacao = $this->locacaoRepository->update($id, $request->all());
        return response()->json(['sucesso' => true, 'dados' => $locacao]);
    }

    public function destroy(string $id)
    {
        $this->locacaoRepository->delete($id);
        return response()->json(['sucesso' => true, 'mensagem' => 'Locação excluída com sucesso']);
    }
}
