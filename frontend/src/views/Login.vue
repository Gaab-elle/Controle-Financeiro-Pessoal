<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-300 to-green-500 px-4">
    <div class="max-w-md w-full bg-white rounded-lg shadow-xl p-8">
      <h2 class="text-3xl font-bold text-center mb-8" style="color: #0A192F;">Login</h2>
      
      <div v-if="error" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        {{ error }}
      </div>

      <form @submit.prevent="handleLogin" class="space-y-6">
        <div>
          <label class="block text-sm font-medium mb-2" style="color: #0A192F;">Email</label>
          <input
            v-model="form.email"
            type="email"
            required
            class="input-custom w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 transition-colors"
            placeholder="seu@email.com"
          />
        </div>

        <div>
          <label class="block text-sm font-medium mb-2" style="color: #0A192F;">Senha</label>
          <input
            v-model="form.password"
            type="password"
            required
            class="input-custom w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 transition-colors"
            placeholder="••••••••"
          />
        </div>

        <button
          type="submit"
          :disabled="loading"
          class="w-full text-white py-2 px-4 rounded-md hover:opacity-90 focus:outline-none focus:ring-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          style="background-color: #2196F3;"
          @mouseenter="$event.target.style.backgroundColor = '#1976D2'"
          @mouseleave="$event.target.style.backgroundColor = '#2196F3'"
        >
          {{ loading ? 'Entrando...' : 'Entrar' }}
        </button>
      </form>

      <p class="mt-6 text-center text-sm" style="color: #0A192F;">
        Não tem uma conta?
        <router-link to="/register" class="font-medium transition-colors" style="color: #2196F3;" @mouseenter="$event.target.style.color = '#1976D2'" @mouseleave="$event.target.style.color = '#2196F3'">
          Cadastre-se
        </router-link>
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { useToastStore } from '../stores/toast'

const toastStore = useToastStore()

const router = useRouter()
const authStore = useAuthStore()

const form = ref({
  email: '',
  password: ''
})

const loading = ref(false)
const error = ref('')

async function handleLogin() {
  loading.value = true
  error.value = ''

  try {
    await authStore.login(form.value)
    toastStore.success('Login realizado com sucesso!')
    router.push('/')
  } catch (err) {
    console.error('Erro no login:', err)
    if (err.errors && Object.keys(err.errors).length > 0) {
      const firstError = Object.values(err.errors)[0]
      const errorMessage = Array.isArray(firstError) ? firstError[0] : firstError
      error.value = errorMessage
      toastStore.error(errorMessage)
    } else if (err.message) {
      error.value = err.message
      toastStore.error(err.message)
    } else {
      const errorMessage = 'Erro ao fazer login. Verifique se o backend está rodando e tente novamente.'
      error.value = errorMessage
      toastStore.error(errorMessage)
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.input-custom:focus {
  border-color: #2196F3 !important;
  box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.2) !important;
  outline: none;
}
</style>

