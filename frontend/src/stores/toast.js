import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useToastStore = defineStore('toast', () => {
  const toasts = ref([])
  let toastId = 0

  function show(message, type = 'success', duration = 3000) {
    const id = toastId++
    const toast = {
      id,
      message,
      type, // 'success' ou 'error'
      duration
    }

    toasts.value.push(toast)

    // Remover automaticamente após a duração
    if (duration > 0) {
      setTimeout(() => {
        remove(id)
      }, duration)
    }

    return id
  }

  function success(message, duration = 3000) {
    return show(message, 'success', duration)
  }

  function error(message, duration = 4000) {
    return show(message, 'error', duration)
  }

  function remove(id) {
    const index = toasts.value.findIndex(toast => toast.id === id)
    if (index > -1) {
      toasts.value.splice(index, 1)
    }
  }

  function clear() {
    toasts.value = []
  }

  return {
    toasts,
    show,
    success,
    error,
    remove,
    clear
  }
})

