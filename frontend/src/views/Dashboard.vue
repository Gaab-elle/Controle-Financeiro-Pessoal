<template>
  <div class="px-4 sm:px-6 lg:px-8 pb-8">
      <!-- Header com título e filtros -->
    <div class="mb-6">
      <div class="mb-4">
        <h1 class="text-xl sm:text-2xl font-semibold" style="color: #0A192F;">
          Bem-vindo(a) ao <span style="color: #2196F3;">TôNoAzul</span> - seu controle financeiro simplificado.
        </h1>
      </div>
      <div class="flex flex-col sm:flex-row gap-3 sm:items-end">
        <!-- Filtro de Mês Início -->
        <div class="flex-1 sm:flex-initial">
          <label class="block text-xs font-medium mb-1" style="color: #0A192F;">Mês Início</label>
          <input
            v-model="filtros.mesInicio"
            type="month"
            @change="loadResumo"
            class="input-custom w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 text-sm bg-white"
          />
        </div>
        <!-- Filtro de Mês Fim -->
        <div class="flex-1 sm:flex-initial">
          <label class="block text-xs font-medium mb-1" style="color: #0A192F;">Mês Fim</label>
          <input
            v-model="filtros.mesFim"
            type="month"
            @change="loadResumo"
            class="input-custom w-full sm:w-auto px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 text-sm bg-white"
          />
        </div>
        <!-- Botão Resetar -->
        <div class="flex-1 sm:flex-initial">
          <button
            @click="resetarFiltros"
            class="w-full sm:w-auto px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm font-medium"
          >
            Resetar
          </button>
        </div>
      </div>
    </div>

    <!-- Cards de Resumo -->
    <div v-if="loading" class="mt-8 text-center">
      <p class="text-gray-500">Carregando...</p>
    </div>

    <div v-else>
      <!-- Cards Principais -->
      <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5 mb-6">
        <!-- Card Saldo Total -->
        <div class="bg-white overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-shadow" style="border: 2px solid #90CAF9;">
          <div class="p-4">
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <p class="text-xs font-medium mb-1" style="color: #2196F3;">Saldo Total</p>
                <p class="text-xl sm:text-2xl font-bold" style="color: #0A192F;">
                  R$ {{ formatCurrency(resumo.saldo_total || 0) }}
                </p>
              </div>
              <div class="flex-shrink-0 ml-4">
                <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background-color: #2196F3;">
                  <i class="fas fa-wallet text-white text-lg"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Card Entradas -->
        <div class="bg-white overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-shadow" style="border: 2px solid #90CAF9;">
          <div class="p-4">
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <p class="text-xs font-medium mb-1" style="color: #10B981;">Total de Entradas</p>
                <p class="text-xl sm:text-2xl font-bold" style="color: #0A192F;">
                  R$ {{ formatCurrency(resumo.total_entradas || 0) }}
                </p>
                <div v-if="resumo.percentual_entradas !== undefined" class="mt-1.5">
                  <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full"
                        :class="resumo.percentual_entradas >= 0 
                          ? 'bg-green-200 text-green-800' 
                          : 'bg-red-200 text-red-800'">
                    <i v-if="resumo.percentual_entradas >= 0" class="fas fa-arrow-up text-[10px]"></i>
                    <i v-else class="fas fa-arrow-down text-[10px]"></i>
                    {{ formatPercentual(resumo.percentual_entradas) }}% vs mês anterior
                  </span>
                </div>
              </div>
              <div class="flex-shrink-0 ml-4">
                <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                  <i class="fas fa-arrow-up text-white text-lg"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Card Saídas -->
        <div class="bg-white overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-shadow" style="border: 2px solid #90CAF9;">
          <div class="p-4">
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <p class="text-xs font-medium mb-1" style="color: #EF4444;">Total de Saídas</p>
                <p class="text-xl sm:text-2xl font-bold" style="color: #0A192F;">
                  R$ {{ formatCurrency(resumo.total_saidas || 0) }}
                </p>
                <div v-if="resumo.percentual_saidas !== undefined" class="mt-1.5">
                  <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full"
                        :class="resumo.percentual_saidas >= 0 
                          ? 'bg-red-200 text-red-800' 
                          : 'bg-green-200 text-green-800'">
                    <i v-if="resumo.percentual_saidas >= 0" class="fas fa-arrow-up text-[10px]"></i>
                    <i v-else class="fas fa-arrow-down text-[10px]"></i>
                    {{ formatPercentual(resumo.percentual_saidas) }}% vs mês anterior
                  </span>
                </div>
              </div>
              <div class="flex-shrink-0 ml-4">
                <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center">
                  <i class="fas fa-arrow-down text-white text-lg"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Card Total Contas -->
        <div class="bg-white overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-shadow" style="border: 2px solid #90CAF9;">
          <div class="p-4">
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <p class="text-xs font-medium mb-1" style="color: #0A192F;">Total de Contas</p>
                <p class="text-xl sm:text-2xl font-bold" style="color: #0A192F;">
                  {{ resumo.total_contas || 0 }}
                </p>
                <p class="text-[10px] mt-1" style="color: #0A192F; opacity: 0.7;">{{ resumo.total_lancamentos || 0 }} lançamentos</p>
              </div>
              <div class="flex-shrink-0 ml-4">
                <div class="w-10 h-10 rounded-full flex items-center justify-center" style="background-color: #2196F3;">
                  <i class="fas fa-university text-white text-lg"></i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Gráficos -->
      <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 sm:gap-6 mb-6">
        <!-- Gráfico de Pizza - Despesas por Categoria -->
        <ExpenseChart 
          :despesas="resumo.despesas_por_categoria || []" 
          :loading="loading"
        />

        <!-- Gráfico de Linha - Evolução do Saldo -->
        <EvolutionChart 
          :evolucao="resumo.evolucao_mensal || []" 
          :loading="loading"
        />
      </div>

      <!-- Tabela de Despesas por Categoria -->
      <div v-if="resumo.despesas_por_categoria && resumo.despesas_por_categoria.length > 0" 
           class="bg-white rounded-lg shadow-lg p-4 sm:p-6">
        <h3 class="text-sm sm:text-base font-semibold text-gray-800 mb-3">Despesas por Categoria</h3>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-3 sm:px-6 py-2 text-left text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Categoria
                </th>
                <th class="px-3 sm:px-6 py-2 text-left text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Valor
                </th>
                <th class="px-3 sm:px-6 py-2 text-left text-[10px] sm:text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Percentual
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(item, index) in resumo.despesas_por_categoria" :key="index" 
                  class="hover:bg-gray-50 transition-colors">
                <td class="px-3 sm:px-6 py-2 sm:py-3 whitespace-nowrap text-xs font-medium text-gray-900">
                  {{ item.categoria }}
                </td>
                <td class="px-3 sm:px-6 py-2 sm:py-3 whitespace-nowrap text-xs font-semibold text-gray-700">
                  R$ {{ formatCurrency(item.valor) }}
                </td>
                <td class="px-3 sm:px-6 py-2 sm:py-3 whitespace-nowrap text-xs text-gray-500">
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-100 text-blue-800">
                    {{ calcularPercentual(item.valor, resumo.total_saidas) }}%
                  </span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'
import ExpenseChart from '../components/ExpenseChart.vue'
import EvolutionChart from '../components/EvolutionChart.vue'

const resumo = ref({
  saldo_total: 0,
  total_entradas: 0,
  total_saidas: 0,
  total_contas: 0,
  total_lancamentos: 0,
  percentual_entradas: 0,
  percentual_saidas: 0,
  despesas_por_categoria: [],
  evolucao_mensal: [],
  periodo: {
    inicio: '',
    fim: ''
  }
})

const loading = ref(true)

const filtros = ref({
  mesInicio: new Date().toISOString().slice(0, 7), // YYYY-MM
  mesFim: new Date().toISOString().slice(0, 7)     // YYYY-MM
})

function formatCurrency(value) {
  return new Intl.NumberFormat('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value)
}

function calcularPercentual(valor, total) {
  if (!total || total === 0) return 0
  return ((valor / total) * 100).toFixed(1)
}

function formatPercentual(valor) {
  if (valor === undefined || valor === null) return 0
  return Math.abs(valor).toFixed(1)
}

function resetarFiltros() {
  const hoje = new Date()
  filtros.value.mesInicio = hoje.toISOString().slice(0, 7)
  filtros.value.mesFim = hoje.toISOString().slice(0, 7)
  loadResumo()
}

async function loadResumo() {
  try {
    loading.value = true
    const params = {
      mes_inicio: filtros.value.mesInicio,
      mes_fim: filtros.value.mesFim
    }
    const response = await api.get('/dashboard', { params })
    resumo.value = response.data
  } catch (error) {
    console.error('Erro ao carregar resumo:', error)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadResumo()
})
</script>

<style scoped>
h1 {
  font-family: 'Inter', sans-serif;
}

.input-custom:focus {
  border-color: #2196F3 !important;
  box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.2) !important;
  outline: none;
}

/* Estilos responsivos adicionais */
@media (max-width: 640px) {
  .grid {
    grid-template-columns: 1fr;
  }
}
</style>
