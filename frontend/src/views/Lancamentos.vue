<template>
  <div class="px-4 sm:px-6 lg:px-8">
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-3xl font-semibold" style="color: #0A192F;">Lançamentos</h1>
        <p class="mt-2 text-sm" style="color: #0A192F; opacity: 0.7;">Registre suas entradas e saídas financeiras</p>
      </div>
      <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
        <button
          @click="openModal"
          class="block rounded-md px-3 py-2 text-center text-sm font-semibold text-white shadow-sm hover:opacity-90 transition-colors"
          style="background-color: #2196F3;"
          @mouseenter="$event.target.style.backgroundColor = '#1976D2'"
          @mouseleave="$event.target.style.backgroundColor = '#2196F3'"
        >
          Novo Lançamento
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
                <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Data</th>
                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Descrição</th>
                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Conta</th>
                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Tipo</th>
                <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Valor</th>
                <th class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                  <span class="sr-only">Ações</span>
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="lancamento in lancamentos" :key="lancamento.id">
                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm text-gray-500 sm:pl-0">
                  {{ formatDate(lancamento.data) }}
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900">
                  {{ lancamento.descricao }}
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                  {{ lancamento.conta?.nome }}
                </td>
                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                  <span
                    :class="[
                      'inline-flex rounded-full px-2 text-xs font-semibold leading-5',
                      lancamento.tipo === 'entrada'
                        ? 'bg-green-100 text-green-800'
                        : 'bg-red-100 text-red-800'
                    ]"
                  >
                    {{ lancamento.tipo === 'entrada' ? 'Entrada' : 'Saída' }}
                  </span>
                </td>
                <td
                  :class="[
                    'whitespace-nowrap px-3 py-4 text-sm font-semibold',
                    lancamento.tipo === 'entrada' ? 'text-green-600' : 'text-red-600'
                  ]"
                >
                  {{ lancamento.tipo === 'entrada' ? '+' : '-' }}
                  R$ {{ formatCurrency(lancamento.valor) }}
                </td>
                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                  <button
                    @click="editLancamento(lancamento)"
                    class="mr-4 transition-colors hover:opacity-80"
                    style="color: #2196F3;"
                    @mouseenter="$event.target.style.color = '#1976D2'"
                    @mouseleave="$event.target.style.color = '#2196F3'"
                  >
                    Editar
                  </button>
                  <button
                    @click="deleteLancamento(lancamento.id)"
                    class="text-red-600 hover:text-red-900"
                  >
                    Excluir
                  </button>
                </td>
              </tr>
              <tr v-if="lancamentos.length === 0">
                <td colspan="6" class="text-center py-4 text-gray-500">
                  Nenhum lançamento cadastrado
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
            {{ editingLancamento ? 'Editar Lançamento' : 'Novo Lançamento' }}
          </h3>
          
          <div v-if="error" class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
            {{ error }}
          </div>

          <form @submit.prevent="saveLancamento" class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1" style="color: #0A192F;">Conta</label>
              <select
                v-model="form.conta_id"
                required
                class="input-custom w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2"
              >
                <option value="">Selecione uma conta</option>
                <option v-for="conta in contas" :key="conta.id" :value="conta.id">
                  {{ conta.nome }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium mb-1" style="color: #0A192F;">Tipo</label>
              <select
                v-model="form.tipo"
                required
                class="input-custom w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2"
              >
                <option value="">Selecione</option>
                <option value="entrada">Entrada</option>
                <option value="saida">Saída</option>
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium mb-1" style="color: #0A192F;">Descrição</label>
              <input
                v-model="form.descricao"
                type="text"
                required
                class="input-custom w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2"
                placeholder="Ex: Salário, Compra, etc."
              />
            </div>

            <div>
              <label class="block text-sm font-medium mb-1" style="color: #0A192F;">Valor</label>
              <input
                v-model="form.valor"
                type="number"
                step="0.01"
                min="0.01"
                required
                class="input-custom w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2"
                placeholder="0.00"
              />
            </div>

            <div>
              <label class="block text-sm font-medium mb-1" style="color: #0A192F;">Data</label>
              <input
                v-model="form.data"
                type="date"
                required
                class="input-custom w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2"
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

const lancamentos = ref([])
const contas = ref([])
const loading = ref(true)
const showModal = ref(false)
const editingLancamento = ref(null)
const error = ref('')

const form = ref({
  conta_id: '',
  tipo: '',
  descricao: '',
  valor: '',
  data: new Date().toISOString().split('T')[0]
})

function formatCurrency(value) {
  return new Intl.NumberFormat('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value)
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('pt-BR')
}

async function loadLancamentos() {
  try {
    loading.value = true
    const response = await api.get('/lancamentos')
    lancamentos.value = response.data
  } catch (error) {
    console.error('Erro ao carregar lançamentos:', error)
  } finally {
    loading.value = false
  }
}

async function loadContas() {
  try {
    const response = await api.get('/contas')
    contas.value = response.data
  } catch (error) {
    console.error('Erro ao carregar contas:', error)
  }
}

function openModal() {
  editingLancamento.value = null
  form.value = {
    conta_id: '',
    tipo: '',
    descricao: '',
    valor: '',
    data: new Date().toISOString().split('T')[0]
  }
  error.value = ''
  showModal.value = true
}

function editLancamento(lancamento) {
  editingLancamento.value = lancamento
  form.value = {
    conta_id: lancamento.conta_id,
    tipo: lancamento.tipo,
    descricao: lancamento.descricao,
    valor: lancamento.valor,
    data: lancamento.data.split('T')[0]
  }
  error.value = ''
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  editingLancamento.value = null
  form.value = {
    conta_id: '',
    tipo: '',
    descricao: '',
    valor: '',
    data: new Date().toISOString().split('T')[0]
  }
  error.value = ''
}

async function saveLancamento() {
  try {
    loading.value = true
    error.value = ''

    const data = {
      ...form.value,
      valor: parseFloat(form.value.valor)
    }

    if (editingLancamento.value) {
      await api.put(`/lancamentos/${editingLancamento.value.id}`, data)
      toastStore.success('Lançamento atualizado com sucesso!')
    } else {
      await api.post('/lancamentos', data)
      toastStore.success('Lançamento criado com sucesso!')
    }

    await loadLancamentos()
    closeModal()
  } catch (err) {
    if (err.response?.data?.errors) {
      const firstError = Object.values(err.response.data.errors)[0]
      error.value = Array.isArray(firstError) ? firstError[0] : firstError
    } else {
      error.value = err.response?.data?.message || 'Erro ao salvar lançamento.'
    }
  } finally {
    loading.value = false
  }
}

async function deleteLancamento(id) {
  if (!confirm('Tem certeza que deseja excluir este lançamento?')) {
    return
  }

  try {
    await api.delete(`/lancamentos/${id}`)
    toastStore.success('Lançamento excluído com sucesso!')
    await loadLancamentos()
  } catch (error) {
    console.error('Erro ao excluir lançamento:', error)
    toastStore.error('Erro ao excluir lançamento.')
  }
}

onMounted(() => {
  loadLancamentos()
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

