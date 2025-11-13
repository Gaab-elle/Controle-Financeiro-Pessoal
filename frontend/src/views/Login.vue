<template>
  <div class="min-h-screen bg-white">
    <!-- Logo no canto superior esquerdo -->
    <div class="absolute top-6 left-6">
      <img 
        src="/images/logo2.png" 
        alt="TôNoAzul Logo" 
        class="header-logo"
        @error="showLogoFallback = true"
        v-show="!showLogoFallback"
      />
      <span v-show="showLogoFallback" class="header-logo-fallback">💙</span>
    </div>

    <!-- Conteúdo centralizado -->
    <div class="min-h-screen flex items-center justify-center px-4">
      <div class="max-w-md w-full">
        <h2 class="text-4xl font-semibold text-center mb-8" style="color: #1F2937;">Login</h2>
        
        <div v-if="error" class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-md text-sm">
          {{ error }}
        </div>

        <form @submit.prevent="handleLogin" class="space-y-5">
          <div>
            <label class="block text-sm font-medium mb-2" style="color: #6B7280;">E-mail</label>
            <input
              v-model="form.email"
              type="email"
              required
              class="input-custom w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:border-blue-500 transition-colors bg-white"
              placeholder="Digite seu e-mail"
            />
          </div>

          <div>
            <label class="block text-sm font-medium mb-2" style="color: #6B7280;">Senha</label>
            <input
              v-model="form.password"
              type="password"
              required
              class="input-custom w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:border-blue-500 transition-colors bg-white"
              placeholder="Digite sua senha"
            />
          </div>

          <div class="flex items-center justify-between text-sm">
            <label class="flex items-center">
              <input
                v-model="form.remember"
                type="checkbox"
                class="mr-2 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
              />
              <span style="color: #6B7280;">Lembrar-me</span>
            </label>
            <a href="#" class="text-blue-600 hover:text-blue-700 transition-colors">Esqueceu a senha?</a>
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full text-white py-3 px-4 rounded-md hover:opacity-90 focus:outline-none focus:ring-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors font-medium"
            style="background-color: #10B981;"
            @mouseenter="$event.target.style.backgroundColor = '#059669'"
            @mouseleave="$event.target.style.backgroundColor = '#10B981'"
          >
            {{ loading ? 'Entrando...' : 'Entrar' }}
          </button>
        </form>

        <p class="mt-8 text-center text-sm" style="color: #6B7280;">
          Não tem uma conta?
          <router-link to="/register" class="text-blue-600 hover:text-blue-700 font-medium transition-colors">
            Cadastre-se
          </router-link>
        </p>
      </div>
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

const showLogoFallback = ref(false)

const form = ref({
  email: '',
  password: '',
  remember: false
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
  border-color: #3B82F6 !important;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
  outline: none;
}

.header-logo {
  height: 40px;
  width: auto;
  object-fit: contain;
}

.header-logo-fallback {
  font-size: 32px;
  line-height: 1;
  display: inline-block;
}

@media (max-width: 640px) {
  .header-logo {
    height: 32px;
  }
  
  .header-logo-fallback {
    font-size: 28px;
  }
}
</style>

