<template>
  <div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-3xl font-semibold" style="color: #0A192F;">Contas</h1>
        <p class="mt-2 text-sm" style="color: #0A192F; opacity: 0.7;">Gerencie suas contas bancárias, carteiras e cartões</p>
      </div>
      <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
        <button
          @click="openModal"
          class="block rounded-md px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:opacity-90 transition-colors"
          style="background-color: #2196F3;"
          @mouseenter="$event.target.style.backgroundColor = '#1976D2'"
          @mouseleave="$event.target.style.backgroundColor = '#2196F3'"
        >
          Nova Conta
        </button>
      </div>
    </div>

    <div v-if="loading" class="mt-8 text-center">
      <p class="text-gray-500">Carregando...</p>
    </div>

    <div v-else class="mt-8 flow-root">
      <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
          <table class="min-w-full divide-y divide-gray-300">
            <thead>
              <tr>
                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Nome</th>
                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tipo</th>
                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Saldo Inicial</th>
                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Saldo Atual</th>
                <th class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                  <span class="sr-only">Ações</span>
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="conta in contas" :key="conta.id">
                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                  {{ conta.nome }}
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                  <span class="inline-flex rounded-full bg-green-100 px-2 text-xs font-semibold leading-5 text-green-800">
                    {{ conta.tipo }}
                  </span>
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                  R$ {{ formatCurrency(conta.saldo_inicial) }}
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm font-semibold text-gray-900">
                  R$ {{ formatCurrency(conta.saldo_atual) }}
                </td>
                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                  <button
                    @click="editConta(conta)"
                    class="mr-4 transition-colors hover:opacity-80"
                    style="color: #2196F3;"
                    @mouseenter="$event.target.style.color = '#1976D2'"
                    @mouseleave="$event.target.style.color = '#2196F3'"
                  >
                    Editar
                  </button>
                  <button
                    @click="deleteConta(conta.id)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Excluir
                  </button>
                </td>
              </tr>
              <tr v-if="contas.length === 0">
                <td colspan="5" class="text-center py-4 text-gray-500">
                  Nenhuma conta cadastrada
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium mb-4" style="color: #0A192F;">
            {{ editingConta ? 'Editar Conta' : 'Nova Conta' }}
          </h3>
          
          <div v-if="error" class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
            {{ error }}
          </div>

          <form @submit.prevent="saveConta" class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1" style="color: #0A192F;">Nome</label>
              <input
                v-model="form.nome"
                type="text"
                required
                class="input-custom w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2"
                placeholder="Ex: Banco do Brasil"
              />
            </div>

            <div>
              <label class="block text-sm font-medium mb-1" style="color: #0A192F;">Tipo</label>
              <select
                v-model="form.tipo"
                required
                class="input-custom w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2"
              >
                <option value="">Selecione</option>
                <option value="banco">Banco</option>
                <option value="carteira">Carteira</option>
                <option value="cartao">Cartão</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium mb-1" style="color: #0A192F;">Saldo Inicial</label>
              <input
                v-model.number="form.saldo_inicial"
                type="number"
                step="any"
                min="0"
                class="input-custom w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2"
                placeholder="0.00"
              />
            </div>

            <div class="flex justify-end space-x-3 pt-4">
              <button
                type="button"
                @click="closeModal"
                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400"
              >
                Cancelar
              </button>
              <button
                type="submit"
                :disabled="loading"
                class="px-4 py-2 text-white rounded-md hover:opacity-90 disabled:opacity-50 transition-colors"
                style="background-color: #2196F3;"
                @mouseenter="$event.target.style.backgroundColor = '#1976D2'"
                @mouseleave="$event.target.style.backgroundColor = '#2196F3'"
              >
                {{ loading ? 'Salvando...' : 'Salvar' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../services/api'
import { useToastStore } from '../stores/toast'

const toastStore = useToastStore()

const contas = ref([])
const loading = ref(true)
const showModal = ref(false)
const editingConta = ref(null)
const error = ref('')

const form = ref({
  nome: '',
  tipo: '',
  saldo_inicial: 0
})

function formatCurrency(value) {
  return new Intl.NumberFormat('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value)
}

async function loadContas() {
  try {
    loading.value = true
    const response = await api.get('/contas')
    contas.value = response.data
  } catch (error) {
    console.error('Erro ao carregar contas:', error)
  } finally {
    loading.value = false
  }
}

function openModal() {
  editingConta.value = null
  form.value = {
    nome: '',
    tipo: '',
    saldo_inicial: 0
  }
  error.value = ''
  showModal.value = true
}

function editConta(conta) {
  editingConta.value = conta
  form.value = {
    nome: conta.nome,
    tipo: conta.tipo,
    saldo_inicial: parseFloat(conta.saldo_inicial) || 0
  }
  error.value = ''
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  editingConta.value = null
  form.value = {
    nome: '',
    tipo: '',
    saldo_inicial: 0
  }
  error.value = ''
}

async function saveConta() {
  try {
    loading.value = true
    error.value = ''

    // Garantir que saldo_inicial seja um número válido
    const dataToSend = {
      ...form.value,
      saldo_inicial: parseFloat(form.value.saldo_inicial) || 0
    }

    if (editingConta.value) {
      await api.put(`/contas/${editingConta.value.id}`, dataToSend)
      toastStore.success('Conta atualizada com sucesso!')
    } else {
      await api.post('/contas', dataToSend)
      toastStore.success('Conta criada com sucesso!')
    }

    await loadContas()
    closeModal()
  } catch (err) {
    if (err.response?.data?.errors) {
      const firstError = Object.values(err.response.data.errors)[0]
      error.value = Array.isArray(firstError) ? firstError[0] : firstError
    } else {
      error.value = err.response?.data?.message || 'Erro ao salvar conta.'
    }
  } finally {
    loading.value = false
  }
}

async function deleteConta(id) {
  if (!confirm('Tem certeza que deseja excluir esta conta?')) {
    return
  }

  try {
    await api.delete(`/contas/${id}`)
    toastStore.success('Conta excluída com sucesso!')
    await loadContas()
  } catch (error) {
    console.error('Erro ao excluir conta:', error)
    toastStore.error('Erro ao excluir conta.')
  }
}

onMounted(() => {
  loadContas()
})
</script>

<style scoped>
.input-custom:focus {
  border-color: #2196F3 !important;
  box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.2) !important;
  outline: none;
}
</style>

