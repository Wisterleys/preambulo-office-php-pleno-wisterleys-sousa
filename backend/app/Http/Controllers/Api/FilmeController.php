<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\FilmeRepositoryInterface;

class FilmeController extends Controller
{
    protected $filmeRepository;

    public function __construct(FilmeRepositoryInterface $filmeRepository)
    {
        $this->filmeRepository = $filmeRepository;
    }

    public function index()
    {
        $filmes = $this->filmeRepository->all();
        return response()->json(['sucesso' => true, 'dados' => $filmes]);
    }

    public function store(Request $request)
    {
        $filme = $this->filmeRepository->create($request->all());
        return response()->json(['sucesso' => true, 'dados' => $filme], 201);
    }

    public function show(string $id)
    {
        $filme = $this->filmeRepository->find($id);
        return response()->json(['sucesso' => true, 'dados' => $filme]);
    }

    public function update(Request $request, string $id)
    {
        $filme = $this->filmeRepository->update($id, $request->all());
        return response()->json(['sucesso' => true, 'dados' => $filme]);
    }

    public function destroy(string $id)
    {
        $this->filmeRepository->delete($id);
        return response()->json(['sucesso' => true, 'mensagem' => 'Filme excluído com sucesso']);
    }
}
