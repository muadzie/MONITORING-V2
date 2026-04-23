<template>
  <div class="space-y-6"><h1 class="text-2xl font-bold">Progress Siswa</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6"><div v-for="s in students" :key="s.id" class="bg-white rounded-xl p-4 shadow-sm"><div class="flex items-center"><div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 font-bold text-lg">{{ s.name.charAt(0) }}</div><div class="ml-3"><h3 class="font-semibold">{{ s.name }}</h3><p class="text-sm text-gray-500">NISN: {{ s.nisn }}</p></div></div><div class="mt-4 grid grid-cols-2 gap-4"><div class="text-center p-3 bg-green-50 rounded-lg"><p class="text-2xl font-bold text-green-600">{{ s.logbook_count || 0 }}</p><p class="text-xs">Logbook</p></div><div class="text-center p-3 bg-blue-50 rounded-lg"><p class="text-2xl font-bold text-blue-600">{{ s.average_grade || 0 }}</p><p class="text-xs">Rata-rata</p></div></div><div class="mt-3"><div class="w-full bg-gray-200 rounded-full h-2"><div class="bg-purple-600 h-2 rounded-full" :style="{ width: ((s.logbook_count || 0) / 30 * 100) + '%' }"></div></div><p class="text-xs text-gray-500 mt-1">Target 30 logbook</p></div></div></div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../plugins/axios'

const students = ref([])
onMounted(async () => { try { const res = await axios.get('/perusahaan/progress'); students.value = res.data } catch(e) {} })
</script>