<?php

namespace App\Http\Controllers;

use App\Models\Conta;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ContaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $contas = $request->user()->contas()->with('lancamentos')->get();
        
        $contas = $contas->map(function ($conta) {
            return [
                'id' => $conta->id,
                'nome' => $conta->nome,
                'tipo' => $conta->tipo,
                'saldo_inicial' => $conta->saldo_inicial,
                'saldo_atual' => $conta->saldo_atual,
                'created_at' => $conta->created_at,
                'updated_at' => $conta->updated_at,
            ];
        });

        return response()->json($contas);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:banco,carteira,cartao',
            'saldo_inicial' => 'nullable|numeric|min:0',
        ]);

        $conta = $request->user()->contas()->create([
            'nome' => $validated['nome'],
            'tipo' => $validated['tipo'],
            'saldo_inicial' => $validated['saldo_inicial'] ?? 0,
        ]);

        return response()->json([
            'message' => 'Conta criada com sucesso!',
            'conta' => [
                'id' => $conta->id,
                'nome' => $conta->nome,
                'tipo' => $conta->tipo,
                'saldo_inicial' => $conta->saldo_inicial,
                'saldo_atual' => $conta->saldo_atual,
            ]
        ], 201);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $conta = $request->user()->contas()->with('lancamentos')->findOrFail($id);

        return response()->json([
            'id' => $conta->id,
            'nome' => $conta->nome,
            'tipo' => $conta->tipo,
            'saldo_inicial' => $conta->saldo_inicial,
            'saldo_atual' => $conta->saldo_atual,
            'lancamentos' => $conta->lancamentos,
            'created_at' => $conta->created_at,
            'updated_at' => $conta->updated_at,
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $conta = $request->user()->contas()->findOrFail($id);

        $validated = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'tipo' => 'sometimes|required|in:banco,carteira,cartao',
            'saldo_inicial' => 'sometimes|nullable|numeric|min:0',
        ]);

        $conta->update($validated);

        return response()->json([
            'message' => 'Conta atualizada com sucesso!',
            'conta' => [
                'id' => $conta->id,
                'nome' => $conta->nome,
                'tipo' => $conta->tipo,
                'saldo_inicial' => $conta->saldo_inicial,
                'saldo_atual' => $conta->saldo_atual,
            ]
        ]);
    }

    public function destroy(Request $request, $id): JsonResponse
    {
        $conta = $request->user()->contas()->findOrFail($id);
        $conta->delete();

        return response()->json([
            'message' => 'Conta excluída com sucesso!'
        ]);
    }
}

