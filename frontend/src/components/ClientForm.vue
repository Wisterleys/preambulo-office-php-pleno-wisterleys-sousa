<template>
  <form @submit.prevent="submitForm">
    <div class="form-group">
      <label for="name">Nome</label>
      <input type="text" id="name" v-model="clientData.name" required />
    </div>

    <div class="form-group">
      <label for="email">E-mail</label>
      <input type="email" id="email" v-model="clientData.email" required />
    </div>

    <div class="form-group">
      <label for="cpf">CPF</label>
      <input type="text" id="cpf" v-model="clientData.cpf" required />
    </div>

    <div class="form-group">
      <label for="phone">Telefone</label>
      <input type="text" id="phone" v-model="clientData.phone" required />
    </div>

    <div class="form-group">
      <label for="address">Endereço</label>
      <input type="text" id="address" v-model="clientData.address" />
    </div>

    <div class="form-group">
      <label for="client_type_id">Tipo de Cliente</label>
      <select id="client_type_id" v-model="clientData.client_type_id">
        <option :value="null">Selecione um tipo</option>
        <option v-for="type in clientTypes" :key="type.id" :value="type.id">
          {{ type.name }}
        </option>
      </select>
    </div>

    <div class="form-group" v-if="!isEdit">
      <label for="password">Senha</label>
      <input type="password" id="password" v-model="clientData.password" required />
    </div>

    <div class="form-group" v-if="isEdit">
      <label for="password">Nova Senha (opcional)</label>
      <input type="password" id="password" v-model="clientData.password" />
    </div>

    <button type="submit" class="btn-primary">{{ isEdit ? 'Salvar Alterações' : 'Cadastrar Cliente' }}</button>
    <button type="button" class="btn-secondary" @click="$emit('cancel')">Cancelar</button>
  </form>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue'
import api from '../services/api'

const props = defineProps({
  client: {
    type: Object,
    default: () => ({
      name: '',
      email: '',
      cpf: '',
      phone: '',
      address: '',
      password: '',
      client_type_id: null,
    }),
  },
  isEdit: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['submit', 'cancel'])

const clientData = ref({ ...props.client })
const clientTypes = ref([])

// Observa a prop 'client' para atualizar o estado interno quando ela mudar (útil para edição)
watch(() => props.client, (newClient) => {
  clientData.value = { ...newClient }
}, { deep: true })

const fetchClientTypes = async () => {
  try {
    const response = await api.get('/clients/types')
    clientTypes.value = response.data
  } catch (error) {
    console.error('Erro ao buscar tipos de cliente:', error)
    // Tratar erro de forma mais amigável ao usuário
  }
}

const submitForm = () => {
  // Remove a senha se estiver vazia na edição
  if (props.isEdit && !clientData.value.password) {
    delete clientData.value.password
  }
  
  emit('submit', clientData.value)
}

onMounted(fetchClientTypes)
</script>

<style scoped>
.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
  font-weight: bold;
}

.form-group input[type="text"],
.form-group input[type="email"],
.form-group input[type="password"],
.form-group select {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

.btn-primary {
  background-color: #007bff;
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  margin-right: 10px;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
</style>
