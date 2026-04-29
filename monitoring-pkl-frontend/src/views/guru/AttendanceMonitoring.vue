<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Monitoring Kehadiran Siswa</h1>
        <p class="text-gray-500 mt-1">Pantau dan rekam kehadiran siswa bimbingan PKL</p>
      </div>
      <div class="flex gap-2">
        <button 
          @click="exportToExcel" 
          class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:shadow-lg transition-all duration-300 flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
          </svg>
          Export Excel
        </button>
        <button 
          @click="exportToPDF" 
          class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:shadow-lg transition-all duration-300 flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
          </svg>
          Export PDF
        </button>
        <button 
          @click="refreshData" 
          class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-lg hover:shadow-lg transition-all duration-300 flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          Refresh
        </button>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-md p-5 border border-gray-100">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">📋 Pilih Siswa</label>
          <select v-model="filters.student_id" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            <option value="">📌 Semua Siswa</option>
            <option v-for="student in students" :key="student.id" :value="student.id">
              👨‍🎓 {{ student.name }} - {{ student.nisn }}
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">📅 Tanggal Mulai</label>
          <input type="date" v-model="filters.start_date" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">📅 Tanggal Selesai</label>
          <input type="date" v-model="filters.end_date" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">🏷️ Status</label>
          <select v-model="filters.status" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
            <option value="">🔘 Semua Status</option>
            <option value="present">✅ Hadir</option>
            <option value="late">⏰ Terlambat</option>
            <option value="absent">❌ Alpha</option>
            <option value="sick">🤒 Sakit</option>
            <option value="permit">📝 Izin</option>
          </select>
        </div>
      </div>
      <div class="flex justify-end mt-4">
        <button @click="resetFilters" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          Reset Filter
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
      <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl p-5 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm opacity-90">Total Kehadiran</p>
            <p class="text-3xl font-bold mt-1">{{ attendances.length }}</p>
          </div>
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
        </div>
      </div>
      <div class="bg-gradient-to-br from-green-500 to-green-700 rounded-xl p-5 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm opacity-90">Hadir</p>
            <p class="text-3xl font-bold mt-1">{{ presentCount }}</p>
          </div>
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
        </div>
      </div>
      <div class="bg-gradient-to-br from-yellow-500 to-yellow-700 rounded-xl p-5 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm opacity-90">Terlambat</p>
            <p class="text-3xl font-bold mt-1">{{ lateCount }}</p>
          </div>
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
        </div>
      </div>
      <div class="bg-gradient-to-br from-red-500 to-red-700 rounded-xl p-5 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm opacity-90">Alpha</p>
            <p class="text-3xl font-bold mt-1">{{ absentCount }}</p>
          </div>
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </div>
        </div>
      </div>
      <div class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl p-5 text-white shadow-lg">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm opacity-90">Izin/Sakit</p>
            <p class="text-3xl font-bold mt-1">{{ permitCount }}</p>
          </div>
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
      <p class="mt-4 text-gray-500">Memuat data...</p>
    </div>

    <!-- Table -->
    <div v-else-if="filteredAttendances.length > 0" class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
          <thead>
            <tr class="bg-gradient-to-r from-indigo-600 to-purple-600">
              <th class="px-5 py-4 text-left text-xs font-semibold text-white uppercase">No</th>
              <th class="px-5 py-4 text-left text-xs font-semibold text-white uppercase">NISN</th>
              <th class="px-5 py-4 text-left text-xs font-semibold text-white uppercase">Nama Siswa</th>
              <th class="px-5 py-4 text-left text-xs font-semibold text-white uppercase">Kelas</th>
              <th class="px-5 py-4 text-left text-xs font-semibold text-white uppercase">Perusahaan</th>
              <th class="px-5 py-4 text-left text-xs font-semibold text-white uppercase">Tanggal</th>
              <th class="px-5 py-4 text-left text-xs font-semibold text-white uppercase">Check In</th>
              <th class="px-5 py-4 text-left text-xs font-semibold text-white uppercase">Check Out</th>
              <th class="px-5 py-4 text-left text-xs font-semibold text-white uppercase">Status</th>
              <th class="px-5 py-4 text-left text-xs font-semibold text-white uppercase">Validasi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="(att, index) in filteredAttendances" :key="att.id" class="hover:bg-gray-50 transition">
              <td class="px-5 py-4 text-sm text-gray-500">{{ index + 1 }}</td>
              <td class="px-5 py-4 text-sm font-mono text-gray-600">{{ att.student?.nisn || '-' }}</td>
              <td class="px-5 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-sm shadow-sm">
                    {{ att.student?.name?.charAt(0) || 'S' }}
                  </div>
                  <span class="font-medium text-gray-800">{{ att.student?.name || '-' }}</span>
                </div>
              </td>
              <td class="px-5 py-4">
                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-medium">{{ att.student?.class?.name || '-' }}</span>
              </td>
              <td class="px-5 py-4 text-sm text-gray-600">{{ att.company?.name || '-' }}</td>
              <td class="px-5 py-4 text-sm text-gray-600">{{ formatDate(att.date) }}</td>
              <td class="px-5 py-4 text-sm font-mono text-green-600">{{ att.check_in || '-' }}</td>
              <td class="px-5 py-4 text-sm font-mono text-red-600">{{ att.check_out || '-' }}</td>
              <td class="px-5 py-4">
                <span :class="getStatusClass(att.status)" class="px-3 py-1.5 rounded-full text-xs font-semibold shadow-sm">
                  {{ getStatusText(att.status) }}
                </span>
              </td>
              <td class="px-5 py-4">
                <span v-if="att.is_valid_location" class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded-lg text-xs">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                  Valid
                </span>
                <span v-else class="inline-flex items-center gap-1 px-2 py-1 bg-red-100 text-red-700 rounded-lg text-xs">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                  Invalid
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!loading && filteredAttendances.length === 0" class="bg-white rounded-xl p-12 text-center">
      <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
      </svg>
      <p class="text-gray-500 text-lg">Belum ada data kehadiran</p>
      <p class="text-sm text-gray-400 mt-1">Silakan lakukan absensi terlebih dahulu</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

// State
const attendances = ref([])
const students = ref([])
const loading = ref(false)
const filters = ref({
  student_id: '',
  start_date: '',
  end_date: '',
  status: ''
})

// Computed
const presentCount = computed(() => attendances.value.filter(a => a.status === 'present').length)
const lateCount = computed(() => attendances.value.filter(a => a.status === 'late').length)
const absentCount = computed(() => attendances.value.filter(a => a.status === 'absent').length)
const permitCount = computed(() => attendances.value.filter(a => a.status === 'sick' || a.status === 'permit').length)

const filteredAttendances = computed(() => {
  let result = [...attendances.value]

  if (filters.value.student_id) {
    result = result.filter(a => a.student_id === parseInt(filters.value.student_id))
  }

  if (filters.value.start_date) {
    result = result.filter(a => a.date >= filters.value.start_date)
  }

  if (filters.value.end_date) {
    result = result.filter(a => a.date <= filters.value.end_date)
  }

  if (filters.value.status) {
    result = result.filter(a => a.status === filters.value.status)
  }

  return result.sort((a, b) => new Date(b.date) - new Date(a.date))
})

// Helper functions
const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
}

const getStatusText = (status) => {
  const map = { present: '✅ Hadir', late: '⏰ Terlambat', absent: '❌ Alpha', sick: '🤒 Sakit', permit: '📝 Izin' }
  return map[status] || status
}

const getStatusClass = (status) => {
  const map = {
    present: 'bg-green-100 text-green-700 border border-green-200',
    late: 'bg-yellow-100 text-yellow-700 border border-yellow-200',
    absent: 'bg-red-100 text-red-700 border border-red-200',
    sick: 'bg-blue-100 text-blue-700 border border-blue-200',
    permit: 'bg-purple-100 text-purple-700 border border-purple-200'
  }
  return map[status] || 'bg-gray-100 text-gray-700 border border-gray-200'
}

// Fetch data
const fetchStudents = async () => {
  try {
    const res = await axios.get('/guru/students')
    students.value = res.data.data || res.data || []
  } catch (error) {
    console.error('Fetch students error:', error)
  }
}

const fetchAttendances = async () => {
  loading.value = true
  try {
    const res = await axios.get('/guru/attendances')
    attendances.value = res.data.data || res.data || []
  } catch (error) {
    console.error('Fetch attendances error:', error)
    toast.error('Gagal memuat data kehadiran')
  } finally {
    loading.value = false
  }
}

const refreshData = () => {
  fetchAttendances()
  fetchStudents()
  toast.info('Data berhasil diperbarui')
}

const resetFilters = () => {
  filters.value = {
    student_id: '',
    start_date: '',
    end_date: '',
    status: ''
  }
}

// EXPORT TO EXCEL - RAPI DENGAN WARNA
const exportToExcel = () => {
  try {
    // Buat HTML table dengan styling untuk Excel
    let htmlContent = `
      <html>
        <head>
          <meta charset="UTF-8">
          <title>Rekap Absensi PKL</title>
          <style>
            body { font-family: 'Segoe UI', Arial, sans-serif; margin: 20px; }
            h2 { text-align: center; color: #1e40af; margin-bottom: 5px; font-size: 18px; }
            .subtitle { text-align: center; margin-bottom: 20px; color: #6b7280; font-size: 12px; }
            table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 11px; }
            th { background-color: #4f46e5; color: white; padding: 10px 8px; border: 1px solid #3730a3; text-align: center; font-weight: bold; }
            td { border: 1px solid #d1d5db; padding: 8px; }
            .text-center { text-align: center; }
            .bg-hadir { background-color: #dcfce7; color: #166534; font-weight: bold; }
            .bg-terlambat { background-color: #fef9c3; color: #854d0e; font-weight: bold; }
            .bg-alpha { background-color: #fee2e2; color: #991b1b; font-weight: bold; }
            .bg-sakit { background-color: #dbeafe; color: #1e40af; font-weight: bold; }
            .bg-izin { background-color: #f3e8ff; color: #6b21a5; font-weight: bold; }
            .bg-white { background-color: #ffffff; }
            .bg-gray { background-color: #f9fafb; }
            .footer { text-align: center; margin-top: 20px; font-size: 10px; color: #9ca3af; }
          </style>
        </head>
        <body>
          <h2>REKAP ABSENSI PRAKTEK KERJA LAPANGAN (PKL)</h2>
          <div class="subtitle">
            SMKN 1 Subang<br>
            Periode: ${filters.value.start_date || 'Awal'} - ${filters.value.end_date || 'Sekarang'}<br>
            Total Data: ${filteredAttendances.value.length} record
          </div>
          <table>
            <thead>
              <tr>
                <th>No</th><th>NISN</th><th>Nama Siswa</th><th>Kelas</th><th>Perusahaan</th>
                <th>Tanggal</th><th>Check In</th><th>Check Out</th><th>Status</th><th>Validasi</th>
              </tr>
            </thead>
            <tbody>
    `
    
    filteredAttendances.value.forEach((att, index) => {
      const rowBg = index % 2 === 0 ? 'bg-white' : 'bg-gray'
      let statusClass = ''
      let statusText = getStatusText(att.status).replace(/[✅⏰❌🤒📝]/g, '').trim()
      
      if (statusText === 'Hadir') statusClass = 'bg-hadir'
      else if (statusText === 'Terlambat') statusClass = 'bg-terlambat'
      else if (statusText === 'Alpha') statusClass = 'bg-alpha'
      else if (statusText === 'Sakit') statusClass = 'bg-sakit'
      else if (statusText === 'Izin') statusClass = 'bg-izin'
      
      htmlContent += `
        <tr class="${rowBg}">
          <td class="text-center">${index + 1}</td>
          <td>${att.student?.nisn || '-'}</td>
          <td>${att.student?.name || '-'}</td>
          <td>${att.student?.class?.name || '-'}</td>
          <td>${att.company?.name || '-'}</td>
          <td class="text-center">${formatDate(att.date)}</td>
          <td class="text-center">${att.check_in || '-'}</td>
          <td class="text-center">${att.check_out || '-'}</td>
          <td class="${statusClass} text-center">${statusText}</td>
          <td class="text-center">${att.is_valid_location ? '✓ Valid' : '✗ Invalid'}</td>
        </tr>
      `
    })
    
    htmlContent += `
            </tbody>
          </table>
          <div class="footer">
            Dicetak pada: ${new Date().toLocaleString('id-ID')}
          </div>
        </body>
      </html>
    `
    
    // Download sebagai file .xls
    const blob = new Blob([htmlContent], { type: 'application/vnd.ms-excel' })
    const link = document.createElement('a')
    const url = URL.createObjectURL(blob)
    link.setAttribute('href', url)
    link.setAttribute('download', `Rekap_Absensi_${new Date().toISOString().split('T')[0]}.xls`)
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)
    URL.revokeObjectURL(url)
    
    toast.success('File Excel berhasil diunduh')
  } catch (error) {
    console.error('Export Excel error:', error)
    toast.error('Gagal mengexport data')
  }
}

// Export to PDF
const exportToPDF = () => {
  const printWindow = window.open('', '_blank')
  
  printWindow.document.write(`
    <html>
      <head>
        <title>Rekap Absensi PKL</title>
        <meta charset="UTF-8">
        <style>
          body { font-family: 'Segoe UI', Arial, sans-serif; margin: 20px; }
          h2 { text-align: center; color: #1e40af; margin-bottom: 5px; font-size: 18px; }
          .subtitle { text-align: center; margin-bottom: 20px; color: #6b7280; font-size: 12px; }
          table { width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 11px; }
          th { background-color: #4f46e5; color: white; padding: 10px 8px; border: 1px solid #3730a3; text-align: center; }
          td { border: 1px solid #d1d5db; padding: 8px; }
          .text-center { text-align: center; }
          .bg-hadir { background-color: #dcfce7; color: #166534; font-weight: bold; }
          .bg-terlambat { background-color: #fef9c3; color: #854d0e; font-weight: bold; }
          .bg-alpha { background-color: #fee2e2; color: #991b1b; font-weight: bold; }
          .bg-sakit { background-color: #dbeafe; color: #1e40af; font-weight: bold; }
          .bg-izin { background-color: #f3e8ff; color: #6b21a5; font-weight: bold; }
          .footer { text-align: center; margin-top: 20px; font-size: 10px; color: #6b7280; }
        </style>
      </head>
      <body>
        <h2>REKAP ABSENSI PKL</h2>
        <div class="subtitle">SMKN 1 Subang<br>Periode: ${filters.value.start_date || 'Awal'} - ${filters.value.end_date || 'Sekarang'}</div>
        <table>
          <thead><tr><th>No</th><th>NISN</th><th>Nama Siswa</th><th>Kelas</th><th>Perusahaan</th><th>Tanggal</th><th>Check In</th><th>Check Out</th><th>Status</th><th>Validasi</th></tr></thead>
          <tbody>
            ${filteredAttendances.value.map((att, index) => {
              let statusClass = ''
              let statusText = getStatusText(att.status).replace(/[✅⏰❌🤒📝]/g, '').trim()
              if (statusText === 'Hadir') statusClass = 'bg-hadir'
              else if (statusText === 'Terlambat') statusClass = 'bg-terlambat'
              else if (statusText === 'Alpha') statusClass = 'bg-alpha'
              else if (statusText === 'Sakit') statusClass = 'bg-sakit'
              else if (statusText === 'Izin') statusClass = 'bg-izin'
              return `<tr><td class="text-center">${index+1}</td><td>${att.student?.nisn||'-'}</td><td>${att.student?.name||'-'}</td><td>${att.student?.class?.name||'-'}</td><td>${att.company?.name||'-'}</td><td class="text-center">${formatDate(att.date)}</td><td class="text-center">${att.check_in||'-'}</td><td class="text-center">${att.check_out||'-'}</td><td class="${statusClass} text-center">${statusText}</td><td class="text-center">${att.is_valid_location ? '✓ Valid' : '✗ Invalid'}</td></tr>`
            }).join('')}
          </tbody>
        </table>
        <div class="footer">Dicetak: ${new Date().toLocaleString('id-ID')}</div>
      </body>
    </html>
  `)
  
  printWindow.document.close()
  printWindow.print()
  toast.success('File PDF siap dicetak')
}

onMounted(() => {
  fetchStudents()
  fetchAttendances()
})
</script>