<template>
  <PieChart 
    :data="despesas" 
    :title="'Distribuição de Despesas por Categoria'"
  />
</template>

<script setup>
import PieChart from './PieChart.vue'

defineProps({
  despesas: {
    type: Array,
    default: () => []
  },
  loading: {
    type: Boolean,
    default: false
  }
})
</script>
