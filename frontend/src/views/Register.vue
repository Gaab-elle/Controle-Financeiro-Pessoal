<template>
  <div class="min-h-screen flex items-center justify-center px-4" style="background: linear-gradient(to bottom right, #64B5F6, #2196F3);">
    <div class="max-w-md w-full bg-white rounded-lg shadow-xl p-8">
      <h2 class="text-3xl font-bold text-center mb-8" style="color: #0A192F;">Cadastro</h2>
      
      <div v-if="error" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
        {{ error }}
      </div>

      <form @submit.prevent="handleRegister" class="space-y-6">
        <div>
          <label class="block text-sm font-medium mb-2" style="color: #0A192F;">Nome</label>
          <input
            v-model="form.name"
            type="text"
            required
            class="input-custom w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 transition-colors"
            placeholder="Seu nome"
          />
        </div>

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
            minlength="8"
            class="input-custom w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 transition-colors"
            placeholder="••••••••"
          />
        </div>

        <div>
          <label class="block text-sm font-medium mb-2" style="color: #0A192F;">Confirmar Senha</label>
          <input
            v-model="form.password_confirmation"
            type="password"
            required
            minlength="8"
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
          {{ loading ? 'Cadastrando...' : 'Cadastrar' }}
        </button>
      </form>

      <p class="mt-6 text-center text-sm" style="color: #0A192F;">
        Já tem uma conta?
        <router-link to="/login" class="font-medium transition-colors" style="color: #2196F3;" @mouseenter="$event.target.style.color = '#1976D2'" @mouseleave="$event.target.style.color = '#2196F3'">
          Faça login
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
  name: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const loading = ref(false)
const error = ref('')

async function handleRegister() {
  if (form.value.password !== form.value.password_confirmation) {
    const errorMessage = 'As senhas não coincidem.'
    error.value = errorMessage
    toastStore.error(errorMessage)
    return
  }

  loading.value = true
  error.value = ''

  try {
    await authStore.register(form.value)
    toastStore.success('Cadastro realizado com sucesso!')
    router.push('/')
  } catch (err) {
    console.error('Erro no registro:', err)
    if (err.errors && Object.keys(err.errors).length > 0) {
      const firstError = Object.values(err.errors)[0]
      const errorMessage = Array.isArray(firstError) ? firstError[0] : firstError
      error.value = errorMessage
      toastStore.error(errorMessage)
    } else if (err.message) {
      error.value = err.message
      toastStore.error(err.message)
    } else {
      const errorMessage = 'Erro ao cadastrar. Verifique se o backend está rodando e tente novamente.'
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

