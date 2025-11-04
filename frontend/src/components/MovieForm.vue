<template>
  <form @submit.prevent="submitForm" enctype="multipart/form-data">
    <div class="form-group">
      <label for="title">Título</label>
      <input type="text" id="title" v-model="movieData.title" required />
    </div>

    <div class="form-group">
      <label for="synopsis">Sinopse</label>
      <textarea id="synopsis" v-model="movieData.synopsis" required></textarea>
    </div>

    <div class="form-group-inline">
      <div class="form-group">
        <label for="year">Ano</label>
        <input type="number" id="year" v-model="movieData.year" required />
      </div>

      <div class="form-group">
        <label for="category">Categoria</label>
        <input type="text" id="category" v-model="movieData.category" required />
      </div>
    </div>

    <div class="form-group-inline">
      <div class="form-group">
        <label for="rental_price">Preço da Locação (R$)</label>
        <input type="number" id="rental_price" v-model="movieData.rental_price" step="0.01" required />
      </div>

      <div class="form-group">
        <label for="available_quantity">Quantidade Disponível</label>
        <input type="number" id="available_quantity" v-model="movieData.available_quantity" required />
      </div>
    </div>

    <!-- Upload de Imagem (Capa) -->
    <div class="form-group">
      <label for="image">Capa do Filme (Imagem)</label>
      <input type="file" id="image" @change="handleImageUpload" accept="image/*" />
      <p v-if="movieData.image_url && !imageFile">Imagem atual: <a :href="movieData.image_url" target="_blank">Visualizar</a></p>
    </div>

    <!-- Simulação de Crop de Imagem -->
    <div v-if="imagePreviewUrl" class="form-group crop-container">
      <label>Pré-visualização e Corte (400x600)</label>
      <div class="cropper-box">
        <!-- Aqui seria o componente de crop (ex: vue-advanced-cropper) -->
        <img :src="imagePreviewUrl" alt="Pré-visualização da Imagem" class="image-preview" />
        <div class="crop-overlay">
          <p>Simulação de Crop: Arraste e redimensione a área de corte (400x600)</p>
          <!-- Em um projeto real, o componente de crop forneceria os dados de corte -->
        </div>
      </div>
      <p class="crop-tip">A imagem será cortada para a proporção 2:3 (400x600) para padronização no catálogo.</p>
    </div>

    <!-- Upload de Vídeo -->
    <div class="form-group">
      <label for="video">Trailer/Vídeo do Filme</label>
      <input type="file" id="video" @change="handleVideoUpload" accept="video/*" />
      <p v-if="movieData.video_url && !videoFile">Vídeo atual: <a :href="movieData.video_url" target="_blank">Visualizar</a></p>
    </div>

    <button type="submit" class="btn-primary">{{ isEdit ? 'Salvar Alterações' : 'Cadastrar Filme' }}</button>
    <button type="button" class="btn-secondary" @click="$emit('cancel')">Cancelar</button>
  </form>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  movie: {
    type: Object,
    default: () => ({
      title: '',
      synopsis: '',
      year: new Date().getFullYear(),
      category: '',
      rental_price: 0.0,
      available_quantity: 0,
      image_url: null,
      video_url: null,
    }),
  },
  isEdit: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['submit', 'cancel'])

const movieData = ref({ ...props.movie })
const imageFile = ref(null)
const videoFile = ref(null)
const imagePreviewUrl = ref(props.movie.image_url)
const cropData = ref(null) // Simulação dos dados de crop

// Observa a prop 'movie' para atualizar o estado interno quando ela mudar
watch(() => props.movie, (newMovie) => {
  movieData.value = { ...newMovie }
  imagePreviewUrl.value = newMovie.image_url
  imageFile.value = null
  videoFile.value = null
}, { deep: true })

const handleImageUpload = (event) => {
  const file = event.target.files[0]
  if (file) {
    imageFile.value = file
    imagePreviewUrl.value = URL.createObjectURL(file)
    // Em um projeto real, aqui você inicializaria o componente de crop
    // e capturaria os dados de corte (cropData.value)
    cropData.value = JSON.stringify({ x: 0, y: 0, width: 400, height: 600 }) // Simulação
  } else {
    imageFile.value = null
    imagePreviewUrl.value = props.movie.image_url
    cropData.value = null
  }
}

const handleVideoUpload = (event) => {
  const file = event.target.files[0]
  videoFile.value = file || null
}

const submitForm = () => {
  const formData = new FormData()

  // Adiciona campos de texto
  for (const key in movieData.value) {
    if (key !== 'image_url' && key !== 'video_url' && movieData.value[key] !== null) {
      formData.append(key, movieData.value[key])
    }
  }

  // Adiciona arquivos
  if (imageFile.value) {
    formData.append('image', imageFile.value)
    formData.append('crop_data', cropData.value)
  }
  if (videoFile.value) {
    formData.append('video', videoFile.value)
  }

  // Se for edição e o Laravel não suportar PUT com FormData (que é comum),
  // adicionamos o método _method=PUT
  if (props.isEdit) {
    formData.append('_method', 'PUT')
  }

  emit('submit', formData)
}
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
.form-group input[type="number"],
.form-group textarea {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

.form-group-inline {
  display: flex;
  gap: 20px;
}

.form-group-inline .form-group {
  flex: 1;
}

.crop-container {
  border: 1px solid #ddd;
  padding: 15px;
  border-radius: 4px;
  background-color: #f9f9f9;
}

.cropper-box {
  position: relative;
  width: 400px; /* Largura padrão do crop */
  height: 600px; /* Altura padrão do crop */
  margin: 10px auto;
  overflow: hidden;
  border: 2px dashed #007bff;
}

.image-preview {
  width: 100%;
  height: 100%;
  object-fit: contain; /* Para mostrar a imagem inteira dentro da caixa */
}

.crop-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  color: white;
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
  font-size: 1.2em;
  padding: 20px;
}

.crop-tip {
  font-size: 0.8em;
  color: #6c757d;
  text-align: center;
  margin-top: 10px;
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
