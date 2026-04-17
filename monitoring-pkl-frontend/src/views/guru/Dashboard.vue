<template>
  <div class="space-y-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 to-cyan-600 p-8 text-white">
      <h1 class="text-2xl font-bold">Selamat Datang, {{ authStore.user?.name }}!</h1>
      <p class="mt-2">Berikut ringkasan bimbingan PKL Anda</p>
      <div class="flex gap-4 mt-4"><div class="bg-white/20 rounded-xl px-4 py-2"><p class="text-xs">Total Siswa Bimbingan</p><p class="font-bold text-xl">{{ stats.students }}</p></div></div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="bg-white rounded-2xl p-6"><p class="text-gray-500">Siswa Bimbingan</p><p class="text-3xl font-bold text-blue-600">{{ stats.students }}</p></div>
      <div class="bg-white rounded-2xl p-6"><p class="text-gray-500">Hadir Hari Ini</p><p class="text-3xl font-bold text-green-600">{{ stats.present_today }}</p></div>
      <div class="bg-white rounded-2xl p-6"><p class="text-gray-500">Logbook Perlu Review</p><p class="text-3xl font-bold text-yellow-600">{{ stats.pending_logbooks }}</p></div>
      <div class="bg-white rounded-2xl p-6"><p class="text-gray-500">Izin Menunggu</p><p class="text-3xl font-bold text-orange-600">{{ stats.pending_permissions }}</p></div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white rounded-2xl p-6"><h3 class="font-semibold mb-4">Logbook Perlu Review</h3><div v-for="log in pendingLogbooks" :key="log.id" class="p-3 border-b"><p class="font-medium">{{ log.user_name }}</p><p class="text-sm text-gray-500">{{ log.activity }}</p><button class="text-blue-600 text-sm mt-1">Review →</button></div></div>
      <div class="bg-white rounded-2xl p-6"><h3 class="font-semibold mb-4">Izin Menunggu Persetujuan</h3><div v-for="perm in pendingPermissions" :key="perm.id" class="p-3 border-b"><p class="font-medium">{{ perm.user_name }}</p><p class="text-sm text-gray-500">{{ perm.type === 'sick' ? 'Sakit' : 'Izin' }} - {{ perm.date }}</p><button class="text-blue-600 text-sm mt-1">Proses →</button></div></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import axios from '../../plugins/axios'

const authStore = useAuthStore()
const stats = ref({ students: 0, present_today: 0, pending_logbooks: 0, pending_permissions: 0 })
const pendingLogbooks = ref([])
const pendingPermissions = ref([])

onMounted(async () => {
  try { const res = await axios.get('/guru/dashboard/stats'); stats.value = res.data
    const logRes = await axios.get('/guru/logbooks/pending'); pendingLogbooks.value = logRes.data
    const permRes = await axios.get('/guru/permissions/pending'); pendingPermissions.value = permRes.data
  } catch(e) { console.error(e) }
})
</script>