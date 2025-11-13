<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function resumo(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Obter filtros de período
        $mesInicio = $request->input('mes_inicio', Carbon::now()->startOfMonth()->format('Y-m'));
        $mesFim = $request->input('mes_fim', Carbon::now()->endOfMonth()->format('Y-m'));
        
        // Converter para datas
        try {
            $dataInicio = Carbon::createFromFormat('Y-m', $mesInicio)->startOfMonth();
            $dataFim = Carbon::createFromFormat('Y-m', $mesFim)->endOfMonth();
        } catch (\Exception $e) {
            $dataInicio = Carbon::now()->startOfMonth();
            $dataFim = Carbon::now()->endOfMonth();
        }

        // Buscar todas as contas do usuário com lançamentos
        $contas = $user->contas()->with('lancamentos')->get();

        // Calcular saldo total
        $saldoTotal = 0;
        foreach ($contas as $conta) {
            $saldoTotal += $conta->saldo_atual;
        }

        // Buscar lançamentos do período
        $lancamentos = $user->lancamentos()
            ->whereBetween('data', [$dataInicio, $dataFim])
            ->get();

        // Buscar lançamentos do mês anterior para comparação
        $mesAnteriorInicio = $dataInicio->copy()->subMonth()->startOfMonth();
        $mesAnteriorFim = $dataInicio->copy()->subMonth()->endOfMonth();
        
        $lancamentosMesAnterior = $user->lancamentos()
            ->whereBetween('data', [$mesAnteriorInicio, $mesAnteriorFim])
            ->get();

        // Calcular totais do período atual
        $totalEntradas = $lancamentos->where('tipo', 'entrada')->sum('valor');
        $totalSaidas = $lancamentos->where('tipo', 'saida')->sum('valor');

        // Calcular totais do mês anterior
        $totalEntradasMesAnterior = $lancamentosMesAnterior->where('tipo', 'entrada')->sum('valor');
        $totalSaidasMesAnterior = $lancamentosMesAnterior->where('tipo', 'saida')->sum('valor');

        // Calcular percentuais de crescimento
        $percentualEntradas = $totalEntradasMesAnterior > 0 
            ? round((($totalEntradas - $totalEntradasMesAnterior) / $totalEntradasMesAnterior) * 100, 2)
            : ($totalEntradas > 0 ? 100 : 0);
        
        $percentualSaidas = $totalSaidasMesAnterior > 0
            ? round((($totalSaidas - $totalSaidasMesAnterior) / $totalSaidasMesAnterior) * 100, 2)
            : ($totalSaidas > 0 ? 100 : 0);

        // Distribuição de despesas por categoria (baseada na descrição)
        $despesasPorCategoria = $this->agruparDespesasPorCategoria($lancamentos->where('tipo', 'saida'));

        // Evolução mensal do saldo (últimos 6 meses)
        $dataInicioEvolucao = Carbon::now()->subMonths(5)->startOfMonth();
        $evolucaoMensal = $this->calcularEvolucaoMensal($user, $dataInicioEvolucao);

        return response()->json([
            'saldo_total' => round($saldoTotal, 2),
            'total_entradas' => round($totalEntradas, 2),
            'total_saidas' => round($totalSaidas, 2),
            'total_contas' => $contas->count(),
            'total_lancamentos' => $lancamentos->count(),
            'percentual_entradas' => $percentualEntradas,
            'percentual_saidas' => $percentualSaidas,
            'despesas_por_categoria' => $despesasPorCategoria,
            'evolucao_mensal' => $evolucaoMensal,
            'periodo' => [
                'inicio' => $dataInicio->format('Y-m-d'),
                'fim' => $dataFim->format('Y-m-d'),
            ],
        ]);
    }

    private function agruparDespesasPorCategoria($despesas)
    {
        $categorias = [
            'Alimentação' => ['comida', 'restaurante', 'supermercado', 'mercado', 'lanche', 'almoço', 'jantar', 'café'],
            'Transporte' => ['uber', 'taxi', 'ônibus', 'gasolina', 'combustível', 'estacionamento', 'transporte'],
            'Moradia' => ['aluguel', 'condomínio', 'luz', 'água', 'energia', 'internet', 'telefone', 'iptu'],
            'Saúde' => ['farmácia', 'médico', 'hospital', 'plano', 'saúde', 'medicamento'],
            'Educação' => ['curso', 'escola', 'faculdade', 'livro', 'material', 'educação'],
            'Lazer' => ['cinema', 'show', 'viagem', 'hotel', 'passeio', 'lazer', 'diversão'],
            'Compras' => ['roupa', 'calçado', 'eletrônico', 'compra', 'shopping'],
            'Outros' => [],
        ];

        $agrupadas = [];
        
        foreach ($despesas as $despesa) {
            $categoriaEncontrada = 'Outros';
            $descricaoLower = strtolower($despesa->descricao);
            
            foreach ($categorias as $categoria => $palavrasChave) {
                foreach ($palavrasChave as $palavra) {
                    if (strpos($descricaoLower, $palavra) !== false) {
                        $categoriaEncontrada = $categoria;
                        break 2;
                    }
                }
            }
            
            if (!isset($agrupadas[$categoriaEncontrada])) {
                $agrupadas[$categoriaEncontrada] = 0;
            }
            
            $agrupadas[$categoriaEncontrada] += $despesa->valor;
        }

        // Converter para formato de gráfico
        $resultado = [];
        foreach ($agrupadas as $categoria => $valor) {
            if ($valor > 0) {
                $resultado[] = [
                    'categoria' => $categoria,
                    'valor' => round($valor, 2),
                ];
            }
        }

        // Ordenar por valor decrescente
        usort($resultado, function($a, $b) {
            return $b['valor'] <=> $a['valor'];
        });

        return $resultado;
    }

    private function calcularEvolucaoMensal($user, $dataInicio)
    {
        $evolucao = [];
        $dataAtual = $dataInicio->copy();
        $dataFim = Carbon::now();
        
        // Sempre retornar 6 meses
        $mesesLimite = 6;
        $contador = 0;

        // Buscar todas as contas do usuário uma vez
        $contas = $user->contas()->get();

        while ($contador < $mesesLimite) {
            $mesInicio = $dataAtual->copy()->startOfMonth();
            $mesFim = $dataAtual->copy()->endOfMonth();
            
            // Se o mês atual não terminou, usar a data atual
            if ($mesFim->isFuture()) {
                $mesFim = Carbon::now();
            }

            $saldoMes = 0;
            
            // Se não houver contas, saldo será 0
            if ($contas->count() > 0) {
                foreach ($contas as $conta) {
                    $saldoInicial = $conta->saldo_inicial ?? 0;
                    
                    // Buscar lançamentos até o final do mês (ou data atual)
                    $lancamentos = $conta->lancamentos()
                        ->where('data', '<=', $mesFim->format('Y-m-d'))
                        ->get();
                    
                    $saldo = $saldoInicial;
                    foreach ($lancamentos as $lancamento) {
                        if ($lancamento->tipo === 'entrada') {
                            $saldo += $lancamento->valor;
                        } else {
                            $saldo -= $lancamento->valor;
                        }
                    }
                    
                    $saldoMes += $saldo;
                }
            }

            // Formatar nome do mês em português
            $meses = [
                'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun',
                'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'
            ];
            $mesIndex = (int)$dataAtual->format('n') - 1;
            $mesNome = $meses[$mesIndex] . '/' . $dataAtual->format('y');

            $evolucao[] = [
                'mes' => $dataAtual->format('Y-m'),
                'mes_nome' => $mesNome,
                'saldo' => round($saldoMes, 2),
            ];

            $dataAtual->addMonth();
            $contador++;
        }

        return $evolucao;
    }
}

