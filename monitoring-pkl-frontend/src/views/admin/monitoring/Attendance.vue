<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Monitoring Absensi</h1>
        <p class="text-gray-500 mt-1">Pantau kehadiran siswa PKL</p>
      </div>
      <button @click="exportData" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
        Export Excel
      </button>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-2xl shadow-sm p-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <input v-model="filters.start_date" type="date" class="px-4 py-2 border rounded-lg">
        <input v-model="filters.end_date" type="date" class="px-4 py-2 border rounded-lg">
        <select v-model="filters.status" class="px-4 py-2 border rounded-lg">
          <option value="">Semua Status</option>
          <option value="present">Hadir</option>
          <option value="late">Terlambat</option>
          <option value="absent">Alpha</option>
          <option value="sick">Sakit</option>
          <option value="permit">Izin</option>
        </select>
      </div>
      <div class="mt-4 flex justify-end">
        <button @click="fetchData" class="bg-indigo-600 text-white px-4 py-2 rounded-lg">Filter</button>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3">Tanggal</th>
              <th class="px-6 py-3">NISN</th>
              <th class="px-6 py-3">Nama Siswa</th>
              <th class="px-6 py-3">Perusahaan</th>
              <th class="px-6 py-3">Check In</th>
              <th class="px-6 py-3">Check Out</th>
              <th class="px-6 py-3">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in data" :key="item.id" class="border-t hover:bg-gray-50">
              <td class="px-6 py-4">{{ formatDate(item.date) }}</td>
              <td class="px-6 py-4">{{ item.user?.nisn || '-' }}</td>
              <td class="px-6 py-4">{{ item.user?.name }}</td>
              <td class="px-6 py-4">{{ item.company?.name || '-' }}</td>
              <td class="px-6 py-4">{{ item.check_in || '-' }}</td>
              <td class="px-6 py-4">{{ item.check_out || '-' }}</td>
              <td class="px-6 py-4">
                <span :class="getStatusClass(item.status)" class="px-2 py-1 rounded-full text-xs">
                  {{ getStatusText(item.status) }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../../plugins/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()
const data = ref([])
const filters = ref({
  start_date: new Date().toISOString().split('T')[0],
  end_date: new Date().toISOString().split('T')[0],
  status: ''
})

const getStatusText = (s) => ({ present: 'Hadir', late: 'Terlambat', absent: 'Alpha', sick: 'Sakit', permit: 'Izin' }[s] || s)
const getStatusClass = (s) => ({
  present: 'bg-green-100 text-green-800', late: 'bg-yellow-100 text-yellow-800',
  absent: 'bg-red-100 text-red-800', sick: 'bg-blue-100 text-blue-800', permit: 'bg-purple-100 text-purple-800'
}[s] || 'bg-gray-100')
const formatDate = (date) => new Date(date).toLocaleDateString('id-ID')

const fetchData = async () => {
  try {
    const res = await axios.get('/admin/monitoring/attendance', { params: filters.value })
    data.value = res.data
  } catch (error) {
    toast.error('Gagal memuat data')
  }
}

const exportData = async () => {
  try {
    const res = await axios.get('/admin/monitoring/attendance/export', { params: filters.value, responseType: 'blob' })
    const url = window.URL.createObjectURL(new Blob([res.data]))
    const link = document.createElement('a')
    link.href = url
    link.download = `absensi_${new Date().toISOString()}.csv`
    link.click()
    toast.success('Export berhasil')
  } catch (error) {
    toast.error('Gagal export')
  }
}

onMounted(() => fetchData())
</script>