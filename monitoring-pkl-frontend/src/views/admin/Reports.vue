<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-8 text-white">
      <h1 class="text-3xl font-bold">Laporan PKL</h1>
      <p class="mt-2 text-indigo-100">Download laporan absensi dan logbook dalam format Excel</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Laporan Absensi -->
      <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-gray-800">Laporan Absensi</h3>
        </div>
        <p class="text-gray-500 text-sm mb-4">Download rekapan absensi siswa dalam format Excel yang rapi</p>
        
        <form @submit.prevent="downloadAttendance" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
              <input type="date" v-model="attendanceFilters.start_date" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
              <input type="date" v-model="attendanceFilters.end_date" required class="w-full px-4 py-2 border rounded-lg">
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Filter Perusahaan (Opsional)</label>
            <select v-model="attendanceFilters.company_id" class="w-full px-4 py-2 border rounded-lg">
              <option value="">Semua Perusahaan</option>
              <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name }}</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Filter Kelas (Opsional)</label>
            <select v-model="attendanceFilters.class_id" class="w-full px-4 py-2 border rounded-lg">
              <option value="">Semua Kelas</option>
              <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
            </select>
          </div>
          <button type="submit" :disabled="attendanceLoading" class="w-full bg-green-600 text-white py-3 rounded-lg hover:bg-green-700 transition disabled:opacity-50 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            {{ attendanceLoading ? 'Memproses...' : 'Download Laporan Absensi' }}
          </button>
        </form>
      </div>

      <!-- Laporan Logbook -->
      <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
            </svg>
          </div>
          <h3 class="text-xl font-semibold text-gray-800">Laporan Logbook</h3>
        </div>
        <p class="text-gray-500 text-sm mb-4">Download rekapan logbook siswa dalam format Excel yang rapi</p>
        
        <form @submit.prevent="downloadLogbook" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
              <input type="date" v-model="logbookFilters.start_date" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
              <input type="date" v-model="logbookFilters.end_date" required class="w-full px-4 py-2 border rounded-lg">
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Filter Siswa (Opsional)</label>
            <select v-model="logbookFilters.student_id" class="w-full px-4 py-2 border rounded-lg">
              <option value="">Semua Siswa</option>
              <option v-for="student in students" :key="student.id" :value="student.id">{{ student.name }} ({{ student.nisn }})</option>
            </select>
          </div>
          <button type="submit" :disabled="logbookLoading" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition disabled:opacity-50 flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            {{ logbookLoading ? 'Memproses...' : 'Download Laporan Logbook' }}
          </button>
        </form>
      </div>
    </div>

    <!-- Rekap Siswa -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
          <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-800">Rekap Siswa</h3>
      </div>
      <p class="text-gray-500 text-sm mb-4">Download rekapan lengkap nilai dan kehadiran siswa per bulan</p>
      
      <form @submit.prevent="downloadSummary" class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
            <select v-model="summaryFilters.month" class="w-full px-4 py-2 border rounded-lg">
              <option value="1">Januari</option>
              <option value="2">Februari</option>
              <option value="3">Maret</option>
              <option value="4">April</option>
              <option value="5">Mei</option>
              <option value="6">Juni</option>
              <option value="7">Juli</option>
              <option value="8">Agustus</option>
              <option value="9">September</option>
              <option value="10">Oktober</option>
              <option value="11">November</option>
              <option value="12">Desember</option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
            <input type="number" v-model="summaryFilters.year" class="w-full px-4 py-2 border rounded-lg" min="2024" :max="new Date().getFullYear()">
          </div>
        </div>
        <button type="submit" :disabled="summaryLoading" class="w-full bg-purple-600 text-white py-3 rounded-lg hover:bg-purple-700 transition disabled:opacity-50 flex items-center justify-center gap-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
          </svg>
          {{ summaryLoading ? 'Memproses...' : 'Download Rekap Siswa' }}
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()
const attendanceLoading = ref(false)
const logbookLoading = ref(false)
const summaryLoading = ref(false)
const companies = ref([])
const classes = ref([])
const students = ref([])

const attendanceFilters = ref({
  start_date: new Date().toISOString().split('T')[0],
  end_date: new Date().toISOString().split('T')[0],
  company_id: '',
  class_id: ''
})

const logbookFilters = ref({
  start_date: new Date().toISOString().split('T')[0],
  end_date: new Date().toISOString().split('T')[0],
  student_id: ''
})

const summaryFilters = ref({
  month: new Date().getMonth() + 1,
  year: new Date().getFullYear()
})

const downloadAttendance = async () => {
  attendanceLoading.value = true
  try {
    // Gunakan endpoint /admin/reports/attendance
    const response = await axios.get('/admin/reports/attendance', {
      params: {
        start_date: attendanceFilters.value.start_date,
        end_date: attendanceFilters.value.end_date,
        company_id: attendanceFilters.value.company_id,
        class_id: attendanceFilters.value.class_id
      },
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `laporan_absensi_${new Date().toISOString()}.csv`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
    
    toast.success('Laporan absensi berhasil diunduh')
  } catch (error) {
    console.error('Download error:', error)
    toast.error('Gagal mengunduh laporan')
  } finally {
    attendanceLoading.value = false
  }
}

const downloadLogbook = async () => {
  logbookLoading.value = true
  try {
    const response = await axios.get('/admin/reports/logbook', {
      params: {
        start_date: logbookFilters.value.start_date,
        end_date: logbookFilters.value.end_date,
        student_id: logbookFilters.value.student_id
      },
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `laporan_logbook_${new Date().toISOString()}.csv`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
    
    toast.success('Laporan logbook berhasil diunduh')
  } catch (error) {
    toast.error('Gagal mengunduh laporan')
  } finally {
    logbookLoading.value = false
  }
}

const downloadSummary = async () => {
  summaryLoading.value = true
  try {
    const response = await axios.get('/admin/reports/summary', {
      params: {
        month: summaryFilters.value.month,
        year: summaryFilters.value.year
      },
      responseType: 'blob'
    })
    
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', `rekap_siswa_${summaryFilters.value.month}_${summaryFilters.value.year}.csv`)
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
    
    toast.success('Rekap siswa berhasil diunduh')
  } catch (error) {
    toast.error('Gagal mengunduh laporan')
  } finally {
    summaryLoading.value = false
  }
}

const loadData = async () => {
  try {
    const [companiesRes, classesRes, studentsRes] = await Promise.all([
      axios.get('/companies'),
      axios.get('/admin/classes'),
      axios.get('/admin/students')
    ])
    companies.value = companiesRes.data
    classes.value = classesRes.data
    students.value = studentsRes.data
  } catch (error) {
    console.error('Failed to load data:', error)
  }
}

onMounted(() => {
  loadData()
})
</script>