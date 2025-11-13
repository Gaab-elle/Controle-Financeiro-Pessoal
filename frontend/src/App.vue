<template>
  <div id="app" class="min-h-screen" style="background-color: #F4F6FA;">
    <router-view />
    <Toast />
  </div>
</template>

<script setup>
import Toast from './components/Toast.vue'
</script>

