<?php

namespace App\Http\Controllers;

use App\Models\Lancamento;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LancamentoController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $lancamentos = $request->user()->lancamentos()
            ->with('conta')
            ->orderBy('data', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($lancamentos);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'conta_id' => 'required|exists:contas,id',
            'tipo' => 'required|in:entrada,saida',
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0.01',
            'data' => 'required|date',
        ]);

        // Verificar se a conta pertence ao usuário
        $conta = $request->user()->contas()->findOrFail($validated['conta_id']);

        $lancamento = $request->user()->lancamentos()->create([
            'conta_id' => $validated['conta_id'],
            'tipo' => $validated['tipo'],
            'descricao' => $validated['descricao'],
            'valor' => $validated['valor'],
            'data' => $validated['data'],
        ]);

        return response()->json([
            'message' => 'Lançamento criado com sucesso!',
            'lancamento' => $lancamento->load('conta')
        ], 201);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $lancamento = $request->user()->lancamentos()->with('conta')->findOrFail($id);

        return response()->json($lancamento);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $lancamento = $request->user()->lancamentos()->findOrFail($id);

        $validated = $request->validate([
            'conta_id' => 'sometimes|required|exists:contas,id',
            'tipo' => 'sometimes|required|in:entrada,saida',
            'descricao' => 'sometimes|required|string|max:255',
            'valor' => 'sometimes|required|numeric|min:0.01',
            'data' => 'sometimes|required|date',
        ]);

        // Se mudou a conta, verificar se pertence ao usuário
        if (isset($validated['conta_id'])) {
            $request->user()->contas()->findOrFail($validated['conta_id']);
        }

        $lancamento->update($validated);

        return response()->json([
            'message' => 'Lançamento atualizado com sucesso!',
            'lancamento' => $lancamento->load('conta')
        ]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $lancamento = $request->user()->lancamentos()->findOrFail($id);
        $lancamento->delete();

        return response()->json([
            'message' => 'Lançamento excluído com sucesso!'
        ]);
    }
}

