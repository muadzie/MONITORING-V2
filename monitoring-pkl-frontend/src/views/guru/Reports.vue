<template>
  <div class="space-y-6"><h1 class="text-2xl font-bold">Laporan</h1>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6"><div class="bg-white p-6 rounded-xl shadow-sm"><h3 class="font-semibold mb-4">Laporan Absensi</h3><button @click="exportReport('attendance')" class="bg-green-600 text-white px-4 py-2 rounded-lg">Download Excel</button></div><div class="bg-white p-6 rounded-xl shadow-sm"><h3 class="font-semibold mb-4">Laporan Logbook</h3><button @click="exportReport('logbook')" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Download Excel</button></div></div>
  </div>
</template>

<script setup>
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'
const toast = useToast()
const exportReport = async (type) => { try { const res = await axios.get(`/reports/${type}`, { responseType: 'blob' }); const url = window.URL.createObjectURL(new Blob([res.data])); const a = document.createElement('a'); a.href = url; a.download = `${type}_report.xlsx`; a.click(); toast.success('Download dimulai') } catch(e) { toast.error('Gagal') } }
</script>