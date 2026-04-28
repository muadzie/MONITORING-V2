<template>
  <div class="space-y-6"><div class="flex justify-between"><h1 class="text-2xl font-bold">Pengajuan Izin/Sakit</h1><button @click="openModal" class="bg-yellow-600 text-white px-4 py-2 rounded-lg">+ Ajukan</button></div>
    <div class="space-y-4"><div v-for="p in permissions" :key="p.id" class="bg-white rounded-xl p-4 shadow-sm"><div class="flex justify-between"><div><div class="flex items-center"><span class="text-2xl mr-2">{{ p.type === 'sick' ? '🤒' : '📝' }}</span><h3 class="font-semibold">{{ p.type === 'sick' ? 'Sakit' : 'Izin' }}</h3></div><p class="text-sm text-gray-500">{{ p.date }}</p><p class="mt-2">{{ p.reason }}</p></div><div><span :class="p.status === 'approved' ? 'bg-green-100 text-green-800' : p.status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'" class="px-2 py-1 rounded-full text-xs">{{ p.status }}</span></div></div></div></div>
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center"><div class="bg-white p-6 rounded-xl w-96"><h3 class="text-xl font-bold mb-4">Ajukan Izin/Sakit</h3><form @submit.prevent="submit"><input v-model="form.date" type="date" class="w-full p-2 border rounded mb-2"><select v-model="form.type" class="w-full p-2 border rounded mb-2"><option value="permit">Izin</option><option value="sick">Sakit</option></select><textarea v-model="form.reason" placeholder="Alasan" rows="3" class="w-full p-2 border rounded mb-2"></textarea><input type="file" @change="handleFile" class="w-full mb-4"><div class="flex justify-end"><button type="button" @click="closeModal" class="px-4 py-2 border rounded mr-2">Batal</button><button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded">Ajukan</button></div></form></div></div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()
const permissions = ref([])
const showModal = ref(false)
const form = ref({ date: new Date().toISOString().split('T')[0], type: 'permit', reason: '', attachment: null })

const load = async () => { try { const res = await axios.get('/permissions'); permissions.value = res.data } catch(e) {} }
const handleFile = (e) => { form.value.attachment = e.target.files[0] }
const submit = async () => { try { const fd = new FormData(); Object.keys(form.value).forEach(k => form.value[k] && fd.append(k, form.value[k])); await axios.post('/permissions', fd); toast.success('Berhasil'); closeModal(); load() } catch(e) { toast.error('Gagal') } }
const openModal = () => { showModal.value = true }
const closeModal = () => { showModal.value = false }
onMounted(() => load())
</script>