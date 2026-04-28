<template>
  <div class="space-y-6">
    <div class="flex justify-between"><h1 class="text-2xl font-bold">Logbook PKL</h1><button @click="openModal" class="bg-green-600 text-white px-4 py-2 rounded-lg">+ Buat Logbook</button></div>
    <div class="space-y-4"><div v-for="log in logbooks" :key="log.id" class="bg-white rounded-xl p-4 shadow-sm"><div class="flex justify-between"><div><h3 class="font-semibold">{{ log.activity }}</h3><p class="text-sm text-gray-500">{{ log.date }}</p><p class="mt-2">{{ log.description }}</p></div><div class="text-right"><span :class="log.status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'" class="px-2 py-1 rounded-full text-xs">{{ log.status }}</span><div v-if="log.grade" class="mt-2 text-xl font-bold text-yellow-600">{{ log.grade }}</div></div></div><div v-if="log.feedback" class="mt-3 p-3 bg-gray-50 rounded-lg"><p class="text-sm font-semibold">Feedback:</p><p class="text-sm">{{ log.feedback }}</p></div></div></div>
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"><div class="bg-white p-6 rounded-xl w-96"><h3 class="text-xl font-bold mb-4">Buat Logbook</h3><form @submit.prevent="submitLogbook"><input v-model="form.date" type="date" class="w-full p-2 border rounded mb-2"><input v-model="form.activity" placeholder="Aktivitas" class="w-full p-2 border rounded mb-2"><textarea v-model="form.description" placeholder="Deskripsi" rows="3" class="w-full p-2 border rounded mb-2"></textarea><input type="file" @change="handleFile" class="w-full mb-4"><div class="flex justify-end"><button type="button" @click="closeModal" class="px-4 py-2 border rounded mr-2">Batal</button><button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Simpan</button></div></form></div></div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()
const logbooks = ref([])
const showModal = ref(false)
const form = ref({ date: new Date().toISOString().split('T')[0], activity: '', description: '', attachment: null })

const load = async () => { try { const res = await axios.get('/logbooks'); logbooks.value = res.data } catch(e) {} }
const handleFile = (e) => { form.value.attachment = e.target.files[0] }
const submitLogbook = async () => { try { const fd = new FormData(); Object.keys(form.value).forEach(k => form.value[k] && fd.append(k, form.value[k])); await axios.post('/logbooks', fd); toast.success('Berhasil'); closeModal(); load() } catch(e) { toast.error('Gagal') } }
const openModal = () => { showModal.value = true }
const closeModal = () => { showModal.value = false }
onMounted(() => load())
</script>