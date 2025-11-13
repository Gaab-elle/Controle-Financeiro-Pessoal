import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '../services/api'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(localStorage.getItem('token') || null)

  const isAuthenticated = computed(() => !!token.value)

  if (token.value) {
    api.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
  }

  async function login(credentials) {
    try {
      const response = await api.post('/login', credentials)
      token.value = response.data.token
      user.value = response.data.user
      localStorage.setItem('token', token.value)
      api.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
      return response.data
    } catch (error) {
      if (error.response) {
        // Erro com resposta do servidor
        throw error.response.data
      } else if (error.request) {
        // Erro de rede (sem resposta)
        throw {
          message: 'Erro de conexão. Verifique se o backend está rodando em http://localhost:8000',
          errors: {}
        }
      } else {
        // Outro tipo de erro
        throw {
          message: error.message || 'Erro ao fazer login. Tente novamente.',
          errors: {}
        }
      }
    }
  }

  async function register(userData) {
    try {
      const response = await api.post('/register', userData)
      token.value = response.data.token
      user.value = response.data.user
      localStorage.setItem('token', token.value)
      api.defaults.headers.common['Authorization'] = `Bearer ${token.value}`
      return response.data
    } catch (error) {
      if (error.response) {
        // Erro com resposta do servidor
        throw error.response.data
      } else if (error.request) {
        // Erro de rede (sem resposta)
        throw {
          message: 'Erro de conexão. Verifique se o backend está rodando em http://localhost:8000',
          errors: {}
        }
      } else {
        // Outro tipo de erro
        throw {
          message: error.message || 'Erro ao cadastrar. Tente novamente.',
          errors: {}
        }
      }
    }
  }

  async function logout() {
    try {
      await api.post('/logout')
    } catch (error) {
      console.error('Erro ao fazer logout:', error)
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem('token')
      delete api.defaults.headers.common['Authorization']
    }
  }

  async function fetchUser() {
    try {
      const response = await api.get('/me')
      user.value = response.data
      return response.data
    } catch (error) {
      throw error.response?.data || error
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    login,
    register,
    logout,
    fetchUser
  }
})

