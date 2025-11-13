<template>
  <div class="bg-white p-4 sm:p-6 rounded-lg shadow-lg h-full" style="border: 2px solid #90CAF9;">
    <h3 class="text-base sm:text-lg font-semibold mb-4" style="color: #0A192F;">{{ title }}</h3>
    <div class="relative" style="height: 250px; min-height: 250px;">
      <canvas ref="chartCanvas"></canvas>
      <div v-if="!data || data.length === 0" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-90 z-10 rounded">
        <div class="text-center">
          <i class="fas fa-chart-line text-gray-400 text-4xl mb-2"></i>
          <p class="text-xs text-gray-500 font-medium">Nenhum dado disponível</p>
          <p class="text-[10px] text-gray-400 mt-1">Dados serão exibidos após adicionar contas e lançamentos</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, onBeforeUnmount } from 'vue'
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  LineController,
  Title,
  Tooltip,
  Legend,
  Filler
} from 'chart.js'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  LineController,
  Title,
  Tooltip,
  Legend,
  Filler
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
    labels: props.data.map(item => item.mes_nome),
    datasets: [
      {
        label: 'Saldo',
        data: props.data.map(item => item.saldo),
        borderColor: '#2196F3',
        backgroundColor: 'rgba(33, 150, 243, 0.1)',
        borderWidth: 2,
        fill: true,
        tension: 0.4,
        pointRadius: 4,
        pointHoverRadius: 6,
        pointBackgroundColor: '#2196F3',
        pointBorderColor: '#ffffff',
        pointBorderWidth: 2,
      },
    ],
  }

  const config = {
    type: 'line',
    data: chartData,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false,
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return `Saldo: R$ ${context.parsed.y.toFixed(2)}`
            },
          },
        },
      },
      scales: {
        y: {
          beginAtZero: false,
          ticks: {
            callback: function(value) {
              return 'R$ ' + value.toFixed(2)
            },
          },
          grid: {
            color: 'rgba(0, 0, 0, 0.05)',
          },
        },
        x: {
          grid: {
            display: false,
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

