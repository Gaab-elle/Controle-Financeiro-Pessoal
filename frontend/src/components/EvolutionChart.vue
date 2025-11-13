<template>
  <LineChart 
    :data="evolucao" 
    :title="'Evolução do Saldo (Últimos 6 Meses)'"
  />
</template>

<script setup>
import LineChart from './LineChart.vue'

defineProps({
  evolucao: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  }
})
</script>
