<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold">Monitoring Logbook</h1>
      <button @click="exportData" class="bg-green-600 text-white px-4 py-2 rounded-lg">Export Excel</button>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input v-model="filters.start_date" type="date" class="px-4 py-2 border rounded-lg">
        <input v-model="filters.end_date" type="date" class="px-4 py-2 border rounded-lg">
        <select v-model="filters.status" class="px-4 py-2 border rounded-lg">
          <option value="">Semua Status</option>
          <option value="pending">Menunggu</option>
          <option value="approved">Disetujui</option>
          <option value="rejected">Ditolak</option>
        </select>
      </div>
      <div class="mt-4 flex justify-end">
        <button @click="fetchData" class="bg-indigo-600 text-white px-4 py-2 rounded-lg">Filter</button>
      </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3">Tanggal</th><th class="px-6 py-3">Siswa</th>
            <th class="px-6 py-3">Aktivitas</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Nilai</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in data" :key="item.id" class="border-t">
            <td class="px-6 py-4">{{ formatDate(item.date) }}</td>
            <td class="px-6 py-4">{{ item.user?.name }}</td>
            <td class="px-6 py-4">{{ item.activity }}</td>
            <td class="px-6 py-4">{{ getStatusText(item.status) }}</td>
            <td class="px-6 py-4">{{ item.grade || '-' }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../../plugins/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()
const data = ref([])
const filters = ref({ start_date: '', end_date: '', status: '' })

const getStatusText = (s) => ({ pending: 'Menunggu', approved: 'Disetujui', rejected: 'Ditolak' }[s] || s)
const formatDate = (date) => new Date(date).toLocaleDateString('id-ID')

const fetchData = async () => {
  try { const res = await axios.get('/admin/monitoring/logbook', { params: filters.value }); data.value = res.data }
  catch(e) { toast.error('Gagal memuat data') }
}

const exportData = async () => {
  try {
    const res = await axios.get('/admin/monitoring/logbook/export', { params: filters.value, responseType: 'blob' })
    const url = window.URL.createObjectURL(new Blob([res.data]))
    const link = document.createElement('a'); link.href = url; link.download = `logbook_${new Date().toISOString()}.csv`
    link.click(); toast.success('Export berhasil')
  } catch(e) { toast.error('Gagal export') }
}

onMounted(() => fetchData())
</script>