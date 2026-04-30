<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Rekap Absensi PKL</h1>
        <p class="text-gray-500 mt-1">Rekap kehadiran siswa berdasarkan periode yang dipilih</p>
      </div>
      <div class="flex gap-2">
        <button 
          @click="exportToExcel" 
          :disabled="summaryData.length === 0 || loading"
          class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:shadow-lg transition-all duration-300 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
          </svg>
          Export Excel
        </button>
        <button 
          @click="exportToPDF" 
          :disabled="summaryData.length === 0 || loading"
          class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:shadow-lg transition-all duration-300 flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
          </svg>
          Export PDF
        </button>
        <button 
          @click="fetchSummary" 
          class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white rounded-lg hover:shadow-lg transition-all duration-300 flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          Refresh
        </button>
      </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-md p-5 border border-gray-100">
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">📅 Tanggal Mulai</label>
          <input type="date" v-model="startDate" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">📅 Tanggal Selesai</label>
          <input type="date" v-model="endDate" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">🎓 Angkatan</label>
          <select v-model="selectedAngkatan" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            <option value="">Semua Angkatan</option>
            <option v-for="a in angkatans" :key="a" :value="a">{{ a }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-semibold text-gray-700 mb-2">📆 Opsi Cepat</label>
          <select v-model="quickSelect" @change="applyQuickSelect" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            <option value="">Pilih Periode</option>
            <option value="this_month">Bulan Ini</option>
            <option value="last_month">Bulan Lalu</option>
            <option value="this_year">Tahun Ini</option>
            <option value="last_year">Tahun Lalu</option>
          </select>
        </div>
        <div class="flex items-end gap-2">
          <button @click="applyFilter" class="px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex-1">📊 Terapkan</button>
          <button @click="resetFilters" class="px-4 py-2.5 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">🔄 Reset</button>
        </div>
      </div>
    </div>

    <!-- Info Periode -->
    <div v-if="summaryData.length > 0" class="bg-blue-50 rounded-xl p-4 border border-blue-200">
      <div class="flex justify-between items-center flex-wrap gap-3">
        <div>
          <p class="text-sm font-semibold text-blue-800">Periode: {{ formatDateRange }}</p>
          <p class="text-xs text-blue-600">Total Siswa PKL: {{ summaryData.length }} | Total Hari Kerja: {{ totalWorkingDays }} hari</p>
        </div>
        <div class="flex gap-6 text-center">
          <div><p class="text-xs text-gray-500">Rata-rata Kehadiran</p><p class="text-xl font-bold text-green-600">{{ avgKehadiran }}%</p></div>
          <div><p class="text-xs text-gray-500">Rata-rata Nilai</p><p class="text-xl font-bold text-purple-600">{{ avgNilai }}</p></div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
      <p class="mt-4 text-gray-500">Memuat data...</p>
    </div>

    <!-- Table -->
    <div v-else-if="summaryData.length > 0" class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
      <div class="overflow-x-auto">
        <table class="min-w-[1300px] lg:min-w-full bg-white">
          <thead>
            <tr class="bg-gradient-to-r from-indigo-600 to-purple-600">
              <th class="px-3 py-3 text-center text-xs font-semibold text-white uppercase">No</th>
              <th class="px-3 py-3 text-center text-xs font-semibold text-white uppercase">NISN</th>
              <th class="px-3 py-3 text-center text-xs font-semibold text-white uppercase">Nama Siswa</th>
              <th class="px-3 py-3 text-center text-xs font-semibold text-white uppercase">Kelas</th>
              <th class="px-3 py-3 text-center text-xs font-semibold text-white uppercase">Perusahaan</th>
              <th class="px-3 py-3 text-center text-xs font-semibold text-white uppercase bg-green-700">Hadir</th>
              <th class="px-3 py-3 text-center text-xs font-semibold text-white uppercase bg-yellow-700">Terlambat</th>
              <th class="px-3 py-3 text-center text-xs font-semibold text-white uppercase bg-red-700">Alpha</th>
              <th class="px-3 py-3 text-center text-xs font-semibold text-white uppercase bg-blue-700">Sakit</th>
              <th class="px-3 py-3 text-center text-xs font-semibold text-white uppercase bg-purple-700">Izin</th>
              <th class="px-3 py-3 text-center text-xs font-semibold text-white uppercase">% Kehadiran</th>
              <th class="px-3 py-3 text-center text-xs font-semibold text-white uppercase">Rata-rata Nilai</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="(item, index) in summaryData" :key="index" class="hover:bg-gray-50 transition">
              <td class="px-3 py-3 text-sm text-gray-500 text-center">{{ index + 1 }}</td>
              <td class="px-3 py-3 text-sm font-mono text-gray-600 text-center">{{ item.nisn }}</td>
              <td class="px-3 py-3 font-medium text-gray-800">{{ item.nama_siswa }}</td>
              <td class="px-3 py-3 text-sm text-gray-600 text-center">{{ item.kelas }}</td>
              <td class="px-3 py-3 text-sm text-gray-600">{{ item.perusahaan }}</td>
              <td class="px-3 py-3 text-sm font-bold text-green-600 text-center">{{ item.hadir }}</td>
              <td class="px-3 py-3 text-sm font-bold text-yellow-600 text-center">{{ item.terlambat }}</td>
              <td class="px-3 py-3 text-sm font-bold text-red-600 text-center">{{ item.alpha }}</td>
              <td class="px-3 py-3 text-sm font-bold text-blue-600 text-center">{{ item.sakit }}</td>
              <td class="px-3 py-3 text-sm font-bold text-purple-600 text-center">{{ item.izin }}</td>
              <td class="px-3 py-3 text-center">
                <span :class="getPersentaseClass(item.persentase)" class="px-2 py-1 rounded-full text-xs font-semibold">{{ item.persentase }}%</span>
              </td>
              <td class="px-3 py-3 text-center font-bold" :class="getNilaiClass(item.rata_nilai)">{{ item.rata_nilai !== '-' ? item.rata_nilai : '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="px-4 py-3 bg-gray-50 border-t">
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 text-center text-sm">
          <div><span class="font-semibold">Total Hadir:</span><br class="md:hidden"> {{ totalHadir }}</div>
          <div><span class="font-semibold">Total Terlambat:</span><br class="md:hidden"> {{ totalTerlambat }}</div>
          <div><span class="font-semibold">Total Alpha:</span><br class="md:hidden"> {{ totalAlpha }}</div>
          <div><span class="font-semibold">Total Sakit:</span><br class="md:hidden"> {{ totalSakit }}</div>
          <div><span class="font-semibold">Total Izin:</span><br class="md:hidden"> {{ totalIzin }}</div>
          <div><span class="font-semibold">Total Siswa:</span><br class="md:hidden"> {{ summaryData.length }}</div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!loading && summaryData.length === 0" class="bg-white rounded-xl p-12 text-center">
      <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
      </svg>
      <p class="text-gray-500 text-lg">Tidak ada data rekap</p>
      <p class="text-sm text-gray-400 mt-1">Tidak ada siswa PKL pada periode <strong>{{ formatDateRange }}</strong></p>
      <p class="text-xs text-gray-400 mt-2">💡 Coba pilih periode atau angkatan yang berbeda</p>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

// State
const summaryData = ref([])
const loading = ref(false)
const startDate = ref('')
const endDate = ref('')
const selectedAngkatan = ref('')
const quickSelect = ref('')
const meta = ref({})

// Data options
const angkatans = [2024, 2025, 2026, 2027, 2028, 2029, 2030]

// Set default date range
const setDefaultDates = () => {
  const now = new Date()
  const firstDay = new Date(now.getFullYear(), now.getMonth(), 1)
  const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0)
  startDate.value = firstDay.toISOString().split('T')[0]
  endDate.value = lastDay.toISOString().split('T')[0]
}

// Quick select options
const applyQuickSelect = () => {
  const now = new Date()
  const year = now.getFullYear()
  const month = now.getMonth()
  
  switch (quickSelect.value) {
    case 'this_month':
      startDate.value = new Date(year, month, 1).toISOString().split('T')[0]
      endDate.value = new Date(year, month + 1, 0).toISOString().split('T')[0]
      break
    case 'last_month':
      startDate.value = new Date(year, month - 1, 1).toISOString().split('T')[0]
      endDate.value = new Date(year, month, 0).toISOString().split('T')[0]
      break
    case 'this_year':
      startDate.value = new Date(year, 0, 1).toISOString().split('T')[0]
      endDate.value = new Date(year, 11, 31).toISOString().split('T')[0]
      break
    case 'last_year':
      startDate.value = new Date(year - 1, 0, 1).toISOString().split('T')[0]
      endDate.value = new Date(year - 1, 11, 31).toISOString().split('T')[0]
      break
  }
  fetchSummary()
}

// Computed
const formatDateRange = computed(() => {
  if (!startDate.value || !endDate.value) return '-'
  const start = new Date(startDate.value).toLocaleDateString('id-ID')
  const end = new Date(endDate.value).toLocaleDateString('id-ID')
  return `${start} s/d ${end}`
})

const totalWorkingDays = computed(() => meta.value.total_hari_kerja || 0)
const totalHadir = computed(() => summaryData.value.reduce((sum, item) => sum + (item.hadir || 0), 0))
const totalTerlambat = computed(() => summaryData.value.reduce((sum, item) => sum + (item.terlambat || 0), 0))
const totalAlpha = computed(() => summaryData.value.reduce((sum, item) => sum + (item.alpha || 0), 0))
const totalSakit = computed(() => summaryData.value.reduce((sum, item) => sum + (item.sakit || 0), 0))
const totalIzin = computed(() => summaryData.value.reduce((sum, item) => sum + (item.izin || 0), 0))

const avgKehadiran = computed(() => {
  if (summaryData.value.length === 0) return 0
  const sum = summaryData.value.reduce((acc, item) => acc + (item.persentase || 0), 0)
  return Math.round(sum / summaryData.value.length)
})

const avgNilai = computed(() => {
  const withNilai = summaryData.value.filter(item => item.rata_nilai !== '-' && !isNaN(item.rata_nilai))
  if (withNilai.length === 0) return '-'
  const sum = withNilai.reduce((acc, item) => acc + Number(item.rata_nilai), 0)
  return Math.round(sum / withNilai.length)
})

// Helper functions
const getPersentaseClass = (p) => {
  if (p >= 90) return 'bg-green-100 text-green-700'
  if (p >= 75) return 'bg-blue-100 text-blue-700'
  if (p >= 60) return 'bg-yellow-100 text-yellow-700'
  return 'bg-red-100 text-red-700'
}

const getNilaiClass = (n) => {
  if (n === '-' || n === null) return 'text-gray-400'
  if (n >= 85) return 'text-green-600'
  if (n >= 70) return 'text-blue-600'
  if (n >= 60) return 'text-yellow-600'
  return 'text-red-600'
}

// Reset filters
const resetFilters = () => {
  setDefaultDates()
  selectedAngkatan.value = ''
  quickSelect.value = ''
  fetchSummary()
}

// Apply filter
const applyFilter = () => {
  fetchSummary()
}

// Fetch data
const fetchSummary = async () => {
  if (!startDate.value || !endDate.value) {
    toast.warning('Pilih tanggal mulai dan selesai terlebih dahulu')
    return
  }
  
  loading.value = true
  try {
    const params = { start_date: startDate.value, end_date: endDate.value }
    if (selectedAngkatan.value && selectedAngkatan.value !== '') params.angkatan = selectedAngkatan.value
    
    console.log('Fetching with params:', params)
    const res = await axios.get('/guru/attendance-summary', { params })
    summaryData.value = res.data.data || []
    meta.value = res.data.meta || {}
    
    if (summaryData.value.length === 0) {
      const angkatanText = selectedAngkatan.value ? ` dengan angkatan ${selectedAngkatan.value}` : ''
      toast.info(`Tidak ada siswa PKL pada periode ${formatDateRange.value}${angkatanText}`)
    }
  } catch (error) {
    console.error('Fetch error:', error)
    toast.error('Gagal memuat data')
    summaryData.value = []
  } finally {
    loading.value = false
  }
}

// Export to Excel
const exportToExcel = () => {
  if (summaryData.value.length === 0) {
    toast.warning('Tidak ada data untuk diexport')
    return
  }
  
  let htmlContent = `<html><head><meta charset="UTF-8"><title>Rekap Absensi PKL - ${formatDateRange.value}</title>
    <style>body{font-family:Arial;margin:20px}h2{text-align:center;color:#1e40af}table{width:100%;border-collapse:collapse;margin-top:15px}th{background:#4f46e5;color:#fff;padding:10px;border:1px solid #3730a3}td{border:1px solid #ddd;padding:8px}.text-center{text-align:center}.bg-hijau{background:#dcfce7}.bg-kuning{background:#fef9c3}.bg-merah{background:#fee2e2}.bg-biru{background:#dbeafe}.bg-ungu{background:#f3e8ff}</style>
    </head><body><h2>REKAP ABSENSI PKL</h2><p>SMKN 1 Subang - Periode: ${formatDateRange.value}</p>
    <table><thead><tr><th>No</th><th>NISN</th><th>Nama Siswa</th><th>Kelas</th><th>Perusahaan</th><th>Hadir</th><th>Terlambat</th><th>Alpha</th><th>Sakit</th><th>Izin</th><th>% Hadir</th><th>Nilai</th></tr></thead><tbody>`
  
  summaryData.value.forEach((item, idx) => {
    htmlContent += `<tr><td class="text-center">${idx+1}</td><td class="text-center">${item.nisn}</td><td>${item.nama_siswa}</td><td class="text-center">${item.kelas}</td><td>${item.perusahaan}</td>
      <td class="text-center bg-hijau">${item.hadir}</td><td class="text-center bg-kuning">${item.terlambat}</td><td class="text-center bg-merah">${item.alpha}</td>
      <td class="text-center bg-biru">${item.sakit}</td><td class="text-center bg-ungu">${item.izin}</td><td class="text-center">${item.persentase}%</td><td class="text-center">${item.rata_nilai}</td></tr>`
  })
  
  htmlContent += `</tbody></table><p>Dicetak: ${new Date().toLocaleString('id-ID')}</p></body></html>`
  
  const blob = new Blob([htmlContent], { type: 'application/vnd.ms-excel' })
  const link = document.createElement('a')
  const url = URL.createObjectURL(blob)
  link.setAttribute('href', url)
  link.setAttribute('download', `Rekap_Absensi_${formatDateRange.value.replace(/\//g, '-')}.xls`)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url)
  toast.success('File Excel berhasil diunduh')
}

// Export to PDF
const exportToPDF = () => {
  if (summaryData.value.length === 0) {
    toast.warning('Tidak ada data untuk diexport')
    return
  }
  
  const printWindow = window.open('', '_blank')
  printWindow.document.write(`<html><head><title>Rekap Absensi PKL - ${formatDateRange.value}</title>
    <style>body{font-family:Arial;margin:20px}h2{text-align:center;color:#1e40af}table{width:100%;border-collapse:collapse;margin-top:15px}th{background:#4f46e5;color:#fff;padding:10px;border:1px solid #3730a3}td{border:1px solid #ddd;padding:8px}.text-center{text-align:center}.bg-hijau{background:#dcfce7}.bg-kuning{background:#fef9c3}.bg-merah{background:#fee2e2}.bg-biru{background:#dbeafe}.bg-ungu{background:#f3e8ff}</style>
    </head><body><h2>REKAP ABSENSI PKL</h2><p>SMKN 1 Subang - Periode: ${formatDateRange.value}</p>
    <table><thead><tr><th>No</th><th>NISN</th><th>Nama Siswa</th><th>Kelas</th><th>Perusahaan</th><th>Hadir</th><th>Terlambat</th><th>Alpha</th><th>Sakit</th><th>Izin</th><th>% Hadir</th><th>Nilai</th></tr></thead><tbody>
    ${summaryData.value.map((item, idx) => `<tr><td class="text-center">${idx+1}</td><td class="text-center">${item.nisn}</td><td>${item.nama_siswa}</td><td class="text-center">${item.kelas}</td><td>${item.perusahaan}</td>
      <td class="text-center bg-hijau">${item.hadir}</td><td class="text-center bg-kuning">${item.terlambat}</td><td class="text-center bg-merah">${item.alpha}</td>
      <td class="text-center bg-biru">${item.sakit}</td><td class="text-center bg-ungu">${item.izin}</td><td class="text-center">${item.persentase}%</td><td class="text-center">${item.rata_nilai}</td></tr>`).join('')}
    </tbody></table><p>Dicetak: ${new Date().toLocaleString('id-ID')}</p></body></html>`)
  printWindow.document.close()
  printWindow.print()
}

onMounted(() => {
  setDefaultDates()
  fetchSummary()
})
</script>