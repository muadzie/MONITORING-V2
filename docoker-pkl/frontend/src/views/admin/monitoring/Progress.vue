<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Progress Siswa PKL</h1>
        <p class="text-gray-500 mt-1">Pantau perkembangan logbook siswa</p>
      </div>
      <button @click="exportData" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
        Export Excel
      </button>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-2xl shadow-sm p-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <select v-model="filters.class_id" class="px-4 py-2 border rounded-lg">
          <option value="">Semua Kelas</option>
          <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
        </select>
        <select v-model="filters.company_id" class="px-4 py-2 border rounded-lg">
          <option value="">Semua Perusahaan</option>
          <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name }}</option>
        </select>
        <button @click="fetchData" class="bg-indigo-600 text-white px-4 py-2 rounded-lg">Filter</button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Total Siswa</p>
        <p class="text-2xl font-bold">{{ students.length }}</p>
      </div>
      <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Progress > 80%</p>
        <p class="text-2xl font-bold">{{ highProgress }}</p>
      </div>
      <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Progress 50-80%</p>
        <p class="text-2xl font-bold">{{ mediumProgress }}</p>
      </div>
      <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Progress < 50%</p>
        <p class="text-2xl font-bold">{{ lowProgress }}</p>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">No</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">NISN</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Nama Siswa</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Kelas</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Perusahaan</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Total Logbook</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Progress</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Rata-rata Nilai</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="(student, index) in students" :key="student.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 text-sm">{{ index + 1 }}</td>
              <td class="px-6 py-4 text-sm">{{ student.nisn || '-' }}</td>
              <td class="px-6 py-4 font-medium">{{ student.name }}</td>
              <td class="px-6 py-4 text-sm">{{ student.class?.name || '-' }}</td>
              <td class="px-6 py-4 text-sm">{{ student.company?.name || '-' }}</td>
              <td class="px-6 py-4 text-sm font-semibold text-indigo-600">{{ student.logbook_count || 0 }} / 30</td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full rounded-full" :class="getProgressColor(student.progress)" :style="{ width: (student.progress || 0) + '%' }"></div>
                  </div>
                  <span class="text-xs text-gray-500">{{ student.progress || 0 }}%</span>
                </div>
              </td>
              <td class="px-6 py-4 text-sm">{{ student.avg_grade || 0 }}</td>
              <td class="px-6 py-4">
                <span :class="getStatusClass(student.progress)" class="px-2 py-1 rounded-full text-xs">
                  {{ getStatusText(student.progress) }}
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
import { ref, computed, onMounted } from 'vue'
import axios from '../../../plugins/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()
const students = ref([])
const classes = ref([])
const companies = ref([])
const filters = ref({ class_id: '', company_id: '' })

const highProgress = computed(() => students.value.filter(s => s.progress > 80).length)
const mediumProgress = computed(() => students.value.filter(s => s.progress >= 50 && s.progress <= 80).length)
const lowProgress = computed(() => students.value.filter(s => s.progress < 50).length)

const getProgressColor = (progress) => {
  if (progress >= 80) return 'bg-green-600'
  if (progress >= 50) return 'bg-yellow-600'
  return 'bg-red-600'
}

const getStatusClass = (progress) => {
  if (progress >= 80) return 'bg-green-100 text-green-800'
  if (progress >= 50) return 'bg-yellow-100 text-yellow-800'
  return 'bg-red-100 text-red-800'
}

const getStatusText = (progress) => {
  if (progress >= 80) return 'Sangat Baik'
  if (progress >= 50) return 'Cukup'
  return 'Perlu Bimbingan'
}

const fetchData = async () => {
  try {
    const res = await axios.get('/admin/monitoring/progress', { params: filters.value })
    students.value = res.data
  } catch (error) {
    toast.error('Gagal memuat data')
  }
}

const fetchClasses = async () => {
  try {
    const res = await axios.get('/admin/classes')
    classes.value = res.data
  } catch (error) {}
}

const fetchCompanies = async () => {
  try {
    const res = await axios.get('/companies')
    companies.value = res.data
  } catch (error) {}
}

const exportData = async () => {
  try {
    const res = await axios.get('/admin/monitoring/progress/export', { params: filters.value, responseType: 'blob' })
    const url = window.URL.createObjectURL(new Blob([res.data]))
    const link = document.createElement('a')
    link.href = url
    link.download = `progress_siswa_${new Date().toISOString()}.csv`
    link.click()
    toast.success('Export berhasil')
  } catch (error) {
    toast.error('Gagal export')
  }
}

onMounted(() => {
  fetchData()
  fetchClasses()
  fetchCompanies()
})
</script>