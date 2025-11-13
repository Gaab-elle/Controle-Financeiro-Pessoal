<template>
  <div class="bg-white p-4 sm:p-6 rounded-lg shadow-lg h-full" style="border: 2px solid #90CAF9;">
    <h3 class="text-base sm:text-lg font-semibold mb-4" style="color: #0A192F;">{{ title }}</h3>
    <div class="relative" style="height: 250px; min-height: 250px;">
      <canvas ref="chartCanvas"></canvas>
      <div v-if="!data || data.length === 0" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-90 z-10 rounded">
        <div class="text-center">
          <i class="fas fa-chart-pie text-gray-400 text-4xl mb-2"></i>
          <p class="text-xs text-gray-500 font-medium">Nenhuma despesa no período</p>
          <p class="text-[10px] text-gray-400 mt-1">Adicione lançamentos de saída para visualizar</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, onBeforeUnmount } from 'vue'
import {
  Chart as ChartJS,
  ArcElement,
  Tooltip,
  Legend,
  PieController
} from 'chart.js'

// Registrar todos os elementos e controllers necessários para gráfico de pizza
ChartJS.register(
  PieController,
  ArcElement,
  Tooltip,
  Legend
)

const props = defineProps({
  data: {
    type: Array,
    default: () => []
  },
  title: {
    type: String,
    default: 'Gráfico'
  }
})

const chartCanvas = ref(null)
let chartInstance = null

const colors = [
  '#2196F3', // Blue 500 - Primária
  '#1976D2', // Blue 700 - Hover/Destaques
  '#90CAF9', // Blue 200 - Secundária
  '#64B5F6', // Blue 300
  '#42A5F5', // Blue 400
  '#1E88E5', // Blue 600
  '#1565C0', // Blue 800
  '#0D47A1', // Blue 900
]

function renderChart() {
  if (!chartCanvas.value) return
  
  if (!props.data || props.data.length === 0) {
    if (chartInstance) {
      chartInstance.destroy()
      chartInstance = null
    }
    return
  }

  if (chartInstance) {
    chartInstance.destroy()
  }

  const chartData = {
    labels: props.data.map(item => item.categoria),
    datasets: [
      {
        data: props.data.map(item => item.valor),
        backgroundColor: colors.slice(0, props.data.length),
        borderColor: '#ffffff',
        borderWidth: 2,
      },
    ],
  }

  const config = {
    type: 'pie',
    data: chartData,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            padding: 15,
            font: {
              size: 12,
            },
          },
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              const label = context.label || ''
              const value = context.parsed || 0
              const total = context.dataset.data.reduce((a, b) => a + b, 0)
              const percentage = ((value / total) * 100).toFixed(1)
              return `${label}: R$ ${value.toFixed(2)} (${percentage}%)`
            },
          },
        },
      },
    },
  }

  chartInstance = new ChartJS(chartCanvas.value, config)
}

onMounted(() => {
  // Aguardar o próximo tick para garantir que o canvas está renderizado
  setTimeout(() => {
    renderChart()
  }, 50)
})

watch(() => props.data, () => {
  // Aguardar o próximo tick para garantir que o canvas está renderizado
  setTimeout(() => {
    renderChart()
  }, 50)
}, { deep: true })

onBeforeUnmount(() => {
  if (chartInstance) {
    chartInstance.destroy()
    chartInstance = null
  }
})
</script>

