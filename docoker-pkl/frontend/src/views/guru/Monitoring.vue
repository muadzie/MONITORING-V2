<template>
  <div class="space-y-6"><h1 class="text-2xl font-bold">Monitoring Siswa Bimbingan</h1>
    <div class="bg-white rounded-xl shadow-sm overflow-hidden"><table class="w-full"><thead class="bg-gray-50"><tr><th class="px-6 py-3">NISN</th><th class="px-6 py-3">Nama</th><th class="px-6 py-3">Perusahaan</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Logbook</th><th class="px-6 py-3">Aksi</th></tr></thead><tbody><tr v-for="s in students" :key="s.id" class="border-t"><td class="px-6 py-4">{{ s.nisn }}</td><td class="px-6 py-4">{{ s.name }}</td><td class="px-6 py-4">{{ s.company?.name || '-' }}</td><td class="px-6 py-4"><span :class="getStatusClass(s.today_status)" class="px-2 py-1 rounded-full text-xs">{{ getStatusText(s.today_status) }}</span></td><td class="px-6 py-4">{{ s.logbook_count || 0 }}</td><td class="px-6 py-4"><button @click="viewDetail(s)" class="text-blue-600">Detail</button></td></tr></tbody></table></div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../plugins/axios'

const students = ref([])
const getStatusText = (s) => ({ present: 'Hadir', late: 'Terlambat', absent: 'Alpha', sick: 'Sakit', permit: 'Izin' }[s] || s)
const getStatusClass = (s) => ({ present: 'bg-green-100 text-green-800', late: 'bg-yellow-100 text-yellow-800', absent: 'bg-red-100 text-red-800', sick: 'bg-blue-100 text-blue-800', permit: 'bg-purple-100 text-purple-800' }[s] || 'bg-gray-100')
const viewDetail = (s) => alert(`Detail ${s.name}\nNISN: ${s.nisn}\nPerusahaan: ${s.company?.name}\nLogbook: ${s.logbook_count}`)
onMounted(async () => { try { const res = await axios.get('/guru/monitoring'); students.value = res.data } catch(e) {} })
</script>