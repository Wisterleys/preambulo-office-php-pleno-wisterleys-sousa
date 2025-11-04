<template>
  <div class="container dashboard">
    <div class="dashboard-header">
      <h1>Gerenciar Clientes</h1>
    </div>
    <div v-if="loading" class="loading">Carregando...</div>
    <div v-else-if="error" class="error">{{ error }}</div>
    <div v-else>
      <div class="action-bar">
        <button class="btn-primary" @click="createClient">Novo Cliente</button>
      </div>
      
      <div v-if="showForm" class="form-container">
        <h2>{{ formTitle }}</h2>
        <ClientForm 
          :client="currentClient" 
          :is-edit="isEdit" 
          @submit="submitForm" 
          @cancel="cancelForm" 
        />
      </div>

      <div v-else>
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>E-mail</th>
            <th>CPF</th>
            <th>Telefone</th>
            <th>Devoluções Pendentes</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="client in clients" :key="client.id">
            <td>{{ client.id }}</td>
            <td>{{ client.name }}</td>
            <td>{{ client.email }}</td>
            <td>{{ client.cpf }}</td>
            <td>{{ client.phone }}</td>
            <td>{{ client.has_pending_returns ? 'Sim' : 'Não' }}</td>
            <td>
              <button class="btn-sm btn-warning" @click="editClient(client)">Editar</button>
              <button class="btn-sm btn-danger" @click="confirmDelete(client)">Excluir</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    </div>

    <ConfirmModal
      :is-visible="showModal"
      title="Confirmar Exclusão"
      :message="`Tem certeza que deseja excluir o cliente ${clientToDelete?.name}? Esta ação é irreversível.`"
      confirm-text="Sim, eu desejo remover"
      @confirm="deleteClient"
      @close="showModal = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import ClientForm from '../../components/ClientForm.vue'
import ConfirmModal from '../../components/ConfirmModal.vue'
import api from '../../services/api'

const clients = ref([])
const loading = ref(true)
const error = ref('')
const showForm = ref(false)
const isEdit = ref(false)
const currentClient = ref(null)
const showModal = ref(false)
const clientToDelete = ref(null)

const formTitle = computed(() => isEdit.value ? 'Editar Cliente' : 'Novo Cliente')

const fetchClients = async () => {
  try {
    const response = await api.get('/clients')
    clients.value = response.data
  } catch (err) {
    error.value = 'Erro ao carregar clientes'
  } finally {
    loading.value = false
  }
}

onMounted(fetchClients)

const createClient = () => {
  isEdit.value = false
  currentClient.value = {
    name: '',
    email: '',
    cpf: '',
    phone: '',
    address: '',
    password: '',
    client_type_id: null,
  }
  showForm.value = true
}

const editClient = (client) => {
  isEdit.value = true
  currentClient.value = { ...client }
  showForm.value = true
}

const cancelForm = () => {
  showForm.value = false
  currentClient.value = null
}

const submitForm = async (clientData) => {
  try {
    if (isEdit.value) {
      await api.put(`/clients/${currentClient.value.id}`, clientData)
    } else {
      await api.post('/clients', clientData)
    }
    showForm.value = false
    await fetchClients()
  } catch (err) {
    error.value = 'Erro ao salvar cliente: ' + (err.response?.data?.message || err.message)
  }
}

const confirmDelete = (client) => {
  clientToDelete.value = client
  showModal.value = true
}

const deleteClient = async () => {
  try {
    await api.delete(`/clients/${clientToDelete.value.id}`)
    await fetchClients()
  } catch (err) {
    error.value = 'Erro ao excluir cliente: ' + (err.response?.data?.message || err.message)
  } finally {
    clientToDelete.value = null
  }
}
  try {
    const response = await api.get('/clients')
    clients.value = response.data
  } catch (err) {
    error.value = 'Erro ao carregar clientes'
  } finally {
    loading.value = false
  }
})
</script>
