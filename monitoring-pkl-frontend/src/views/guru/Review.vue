<template>
  <div class="space-y-6"><h1 class="text-2xl font-bold">Review Logbook Siswa</h1>
    <div class="space-y-4"><div v-for="log in logbooks" :key="log.id" class="bg-white rounded-xl p-4 shadow-sm"><div class="flex justify-between"><div><h3 class="font-semibold">{{ log.activity }}</h3><p class="text-sm text-gray-500">{{ log.user_name }} - {{ log.date }}</p><p class="mt-2">{{ log.description }}</p></div><div class="w-64"><input v-model="log.grade" type="number" placeholder="Nilai" class="w-full p-2 border rounded mb-2"><textarea v-model="log.feedback" placeholder="Feedback" rows="2" class="w-full p-2 border rounded mb-2"></textarea><button @click="submitReview(log)" class="w-full bg-blue-600 text-white py-2 rounded">Simpan</button></div></div></div></div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()
const logbooks = ref([])

const load = async () => { try { const res = await axios.get('/guru/logbooks-pending'); logbooks.value = res.data } catch(e) {} }
const submitReview = async (log) => { try { await axios.put(`/logbooks/${log.id}/review`, { grade: log.grade, feedback: log.feedback }); toast.success('Berhasil'); load() } catch(e) { toast.error('Gagal') } }
onMounted(() => load())
</script>