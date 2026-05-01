<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-8 text-white">
      <div class="relative z-10 flex items-center gap-4">
        <img 
          src="https://c.top4top.io/p_3749c8ad71.png" 
          alt="Logo" 
          class="w-16 h-16 rounded-xl bg-white/10 p-2 shadow-lg"
        >
        <div>
          <h1 class="text-3xl font-bold">Laporan PKL</h1>
          <p class="mt-2 text-indigo-100">Download berbagai laporan PKL dalam format Excel & PDF</p>
        </div>
      </div>
    </div>

    <!-- Statistik Cepat -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-sm opacity-80">Total Siswa PKL</p>
            <p class="text-2xl font-bold">{{ totalStudents }}</p>
          </div>
          <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
          </div>
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-sm opacity-80">Total Logbook</p>
            <p class="text-2xl font-bold">{{ totalLogbooks }}</p>
          </div>
          <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-sm opacity-80">Rata-rata Nilai</p>
            <p class="text-2xl font-bold">{{ averageGrade }}</p>
          </div>
          <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
            </svg>
          </div>
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 text-white">
        <div class="flex justify-between items-start">
          <div>
            <p class="text-sm opacity-80">Tingkat Kehadiran</p>
            <p class="text-2xl font-bold">{{ attendanceRate }}%</p>
          </div>
          <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-2xl shadow-sm p-1 flex flex-wrap gap-1">
      <button 
        v-for="tab in tabs" 
        :key="tab.key"
        @click="activeTab = tab.key; loadDataByTab()"
        class="px-5 py-2.5 rounded-xl font-medium transition-all duration-200"
        :class="activeTab === tab.key 
          ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md' 
          : 'text-gray-600 hover:bg-gray-100'"
      >
        <span class="mr-2">{{ tab.icon }}</span> {{ tab.label }}
      </button>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-2xl shadow-lg p-5">
      <div v-if="activeTab === 'attendance'" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">📅 Tanggal Mulai</label>
          <input type="date" v-model="attendanceFilters.start_date" class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">📅 Tanggal Akhir</label>
          <input type="date" v-model="attendanceFilters.end_date" class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">🏢 Perusahaan</label>
          <select v-model="attendanceFilters.company_id" class="w-full px-3 py-2 border rounded-lg">
            <option value="">Semua Perusahaan</option>
            <option v-for="c in companies" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">🏫 Kelas</label>
          <select v-model="attendanceFilters.class_id" class="w-full px-3 py-2 border rounded-lg">
            <option value="">Semua Kelas</option>
            <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
          </select>
        </div>
        <div class="flex items-end">
          <button @click="loadDataByTab" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">🔍 Filter</button>
        </div>
      </div>

      <div v-if="activeTab === 'logbook'" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">📅 Tanggal Mulai</label>
          <input type="date" v-model="logbookFilters.start_date" class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">📅 Tanggal Akhir</label>
          <input type="date" v-model="logbookFilters.end_date" class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">👨‍🎓 Siswa</label>
          <select v-model="logbookFilters.student_id" class="w-full px-3 py-2 border rounded-lg">
            <option value="">Semua Siswa</option>
            <option v-for="s in students" :key="s.id" :value="s.id">{{ s.name }} ({{ s.nisn }})</option>
          </select>
        </div>
        <div class="flex items-end">
          <button @click="loadDataByTab" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">🔍 Filter</button>
        </div>
      </div>

      <div v-if="activeTab === 'grade'" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">📅 Tanggal Mulai</label>
          <input type="date" v-model="gradeFilters.start_date" class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">📅 Tanggal Akhir</label>
          <input type="date" v-model="gradeFilters.end_date" class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">⭐ Rentang Nilai</label>
          <select v-model="gradeFilters.range" class="w-full px-3 py-2 border rounded-lg">
            <option value="all">Semua Nilai</option>
            <option value="90-100">A (90-100)</option>
            <option value="80-89">B (80-89)</option>
            <option value="70-79">C (70-79)</option>
            <option value="0-69">D (0-69)</option>
          </select>
        </div>
        <div class="flex items-end">
          <button @click="loadDataByTab" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">🔍 Filter</button>
        </div>
      </div>

      <div v-if="activeTab === 'summary'" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">📅 Tanggal Mulai</label>
          <input type="date" v-model="summaryFilters.start_date" class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">📅 Tanggal Akhir</label>
          <input type="date" v-model="summaryFilters.end_date" class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">🏢 Perusahaan</label>
          <select v-model="summaryFilters.company_id" class="w-full px-3 py-2 border rounded-lg">
            <option value="">Semua Perusahaan</option>
            <option v-for="c in companies" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
        </div>
        <div class="flex items-end">
          <button @click="loadDataByTab" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">🔍 Filter</button>
        </div>
      </div>
    </div>

    <!-- Export Buttons & Preview Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
      <div class="bg-gradient-to-r from-gray-800 to-gray-900 p-4 flex justify-between items-center">
        <div>
          <h3 class="font-bold text-white">{{ currentTabTitle }}</h3>
          <p class="text-xs text-gray-400 mt-1">{{ formatDateRange }}</p>
        </div>
        <div class="flex gap-2">
          <button @click="exportToExcel" :disabled="loading" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition flex items-center gap-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            Export Excel
          </button>
          <button @click="exportToPDF" :disabled="loading" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition flex items-center gap-2 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            Export PDF
          </button>
        </div>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="p-12 text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
        <p class="mt-4 text-gray-500">Memuat data...</p>
      </div>

      <!-- Table Preview -->
      <div v-else class="overflow-x-auto p-4">
        <table class="min-w-full bg-white border-collapse">
          <thead>
            <tr class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
              <th v-for="col in tableColumns" :key="col" class="px-4 py-3 text-left text-xs font-semibold uppercase border">{{ col }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(row, idx) in paginatedData" :key="idx" class="hover:bg-gray-50 transition">
              <td v-for="col in tableColumns" :key="col" class="px-4 py-2 text-sm text-gray-700 border" :class="getCellClass(row, col)">
                {{ formatCellValue(row, col) }}
              </td>
            </tr>
            <tr v-if="tableData.length === 0 && !loading">
              <td :colspan="tableColumns.length" class="text-center py-8 text-gray-500">
                Belum ada data. Silakan filter terlebih dahulu atau pastikan ada data di database.
              </td>
            </tr>
          </tbody>
        </table>
        
        <!-- Pagination -->
        <div v-if="tableData.length > 0" class="flex justify-between items-center mt-4 pt-4 border-t">
          <div class="text-sm text-gray-500">
            Menampilkan {{ ((currentPage - 1) * itemsPerPage) + 1 }} - {{ Math.min(currentPage * itemsPerPage, tableData.length) }} dari {{ tableData.length }} data
          </div>
          <div class="flex gap-2">
            <button @click="prevPage" :disabled="currentPage === 1" class="px-3 py-1 border rounded-lg hover:bg-gray-50 disabled:opacity-50">Previous</button>
            <button @click="nextPage" :disabled="currentPage === totalPages" class="px-3 py-1 border rounded-lg hover:bg-gray-50 disabled:opacity-50">Next</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="bg-indigo-50 rounded-2xl p-5 border border-indigo-100">
      <div class="flex items-start gap-4">
        <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
          <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <p class="text-sm font-semibold text-indigo-800">Informasi Laporan</p>
          <p class="text-xs text-indigo-600">• Laporan dapat diexport ke Excel (.xlsx) dengan format profesional</p>
          <p class="text-xs text-indigo-600">• Laporan dapat diexport ke PDF dengan tampilan print yang rapi</p>
          <p class="text-xs text-indigo-600">• Semua laporan dapat difilter berdasarkan periode tanggal</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

// State
const activeTab = ref('attendance')
const loading = ref(false)
const tableData = ref([])
const currentPage = ref(1)
const itemsPerPage = ref(10)

// Tab configuration
const tabs = [
  { key: 'attendance', label: 'Rekap Absensi', icon: '📋', title: 'LAPORAN REKAP ABSENSI PKL' },
  { key: 'logbook', label: 'Rekap Logbook', icon: '📖', title: 'LAPORAN REKAP LOGBOOK PKL' },
  { key: 'grade', label: 'Rekap Nilai', icon: '🎯', title: 'LAPORAN NILAI AKHIR PKL' },
  { key: 'summary', label: 'Rekap Keseluruhan', icon: '📊', title: 'REKAP KESELURUHAN PKL' }
]

// Data options
const companies = ref([])
const classes = ref([])
const students = ref([])

// Statistik
const totalStudents = ref(0)
const totalLogbooks = ref(0)
const averageGrade = ref(0)
const attendanceRate = ref(0)

// Filter values
const getDefaultStartDate = () => {
  const date = new Date()
  date.setDate(date.getDate() - 30)
  return date.toISOString().split('T')[0]
}

const getDefaultEndDate = () => {
  return new Date().toISOString().split('T')[0]
}

// Filter Absensi
const attendanceFilters = ref({
  start_date: getDefaultStartDate(),
  end_date: getDefaultEndDate(),
  company_id: '',
  class_id: ''
})

// Filter Logbook
const logbookFilters = ref({
  start_date: getDefaultStartDate(),
  end_date: getDefaultEndDate(),
  student_id: ''
})

// Filter Grade
const gradeFilters = ref({
  start_date: getDefaultStartDate(),
  end_date: getDefaultEndDate(),
  range: 'all'
})

// Filter Summary
const summaryFilters = ref({
  start_date: getDefaultStartDate(),
  end_date: getDefaultEndDate(),
  company_id: ''
})

// Computed
const currentTabTitle = computed(() => tabs.find(t => t.key === activeTab.value)?.title || 'Laporan PKL')

const formatDateRange = computed(() => {
  let start = '', end = ''
  if (activeTab.value === 'attendance') {
    start = attendanceFilters.value.start_date
    end = attendanceFilters.value.end_date
  } else if (activeTab.value === 'logbook') {
    start = logbookFilters.value.start_date
    end = logbookFilters.value.end_date
  } else if (activeTab.value === 'grade') {
    start = gradeFilters.value.start_date
    end = gradeFilters.value.end_date
  } else if (activeTab.value === 'summary') {
    start = summaryFilters.value.start_date
    end = summaryFilters.value.end_date
  }
  return `Periode: ${start} s/d ${end}`
})

const tableColumns = computed(() => {
  if (tableData.value.length === 0) return []
  return Object.keys(tableData.value[0]).map(key => {
    const labels = {
      nisn: 'NISN', name: 'Nama Siswa', kelas: 'Kelas', perusahaan: 'Perusahaan',
      hadir: 'Hadir', terlambat: 'Terlambat', izin: 'Izin', sakit: 'Sakit', alpha: 'Alpha',
      persentase: 'Kehadiran (%)', total_logbook: 'Total Logbook', avg_grade: 'Rata-rata Nilai',
      pending: 'Menunggu', approved: 'Disetujui', rejected: 'Ditolak', final_grade: 'Nilai Akhir',
      grade_letter: 'Grade', grade_description: 'Keterangan', status: 'Status'
    }
    return labels[key] || key
  })
})

const totalPages = computed(() => Math.ceil(tableData.value.length / itemsPerPage.value))
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return tableData.value.slice(start, start + itemsPerPage.value)
})

// Helper functions
const formatCellValue = (row, col) => {
  const originalKey = {
    'NISN': 'nisn', 'Nama Siswa': 'name', 'Kelas': 'kelas', 'Perusahaan': 'perusahaan',
    'Hadir': 'hadir', 'Terlambat': 'terlambat', 'Izin': 'izin', 'Sakit': 'sakit', 'Alpha': 'alpha',
    'Kehadiran (%)': 'persentase', 'Total Logbook': 'total_logbook', 'Rata-rata Nilai': 'avg_grade',
    'Menunggu': 'pending', 'Disetujui': 'approved', 'Ditolak': 'rejected', 'Nilai Akhir': 'final_grade',
    'Grade': 'grade_letter', 'Keterangan': 'grade_description', 'Status': 'status'
  }
  const key = originalKey[col] || col
  const value = row[key]
  if (value === null || value === undefined) return '-'
  if (col === 'Kehadiran (%)') return `${value}%`
  if (typeof value === 'number') return value.toFixed(2)
  return value
}

const getCellClass = (row, col) => {
  if (col === 'Kehadiran (%)') {
    const val = row.persentase
    if (val >= 90) return 'bg-green-100 text-green-800 font-semibold'
    if (val >= 75) return 'bg-yellow-100 text-yellow-800 font-semibold'
    return 'bg-red-100 text-red-800 font-semibold'
  }
  if (col === 'Status') {
    if (row.status === 'LULUS') return 'text-green-600 font-bold'
    return 'text-red-600 font-bold'
  }
  return ''
}

// Load data functions
const loadAttendanceData = async () => {
  loading.value = true
  try {
    const params = {
      start_date: attendanceFilters.value.start_date,
      end_date: attendanceFilters.value.end_date,
      company_id: attendanceFilters.value.company_id || undefined,
      class_id: attendanceFilters.value.class_id || undefined
    }
    const response = await axios.get('/admin/reports/attendance/json', { params })
    tableData.value = response.data.data || []
    currentPage.value = 1
    console.log('Attendance data loaded:', tableData.value.length)
  } catch (error) {
    console.error('Load attendance error:', error)
    toast.error('Gagal memuat data absensi')
    tableData.value = []
  } finally {
    loading.value = false
  }
}

const loadLogbookData = async () => {
  loading.value = true
  try {
    const params = {
      start_date: logbookFilters.value.start_date,
      end_date: logbookFilters.value.end_date,
      student_id: logbookFilters.value.student_id || undefined
    }
    const response = await axios.get('/admin/reports/logbook/json', { params })
    tableData.value = response.data.data || []
    currentPage.value = 1
    console.log('Logbook data loaded:', tableData.value.length)
  } catch (error) {
    console.error('Load logbook error:', error)
    toast.error('Gagal memuat data logbook')
    tableData.value = []
  } finally {
    loading.value = false
  }
}

const loadGradeData = async () => {
  loading.value = true
  try {
    const params = {
      start_date: gradeFilters.value.start_date,
      end_date: gradeFilters.value.end_date
    }
    const response = await axios.get('/admin/reports/grade/json', { params })
    let data = response.data.data || []
    
    if (gradeFilters.value.range !== 'all') {
      const [min, max] = gradeFilters.value.range.split('-').map(Number)
      data = data.filter(item => {
        const grade = item.final_grade || 0
        return grade >= min && grade <= max
      })
    }
    
    tableData.value = data
    currentPage.value = 1
    console.log('Grade data loaded:', tableData.value.length)
  } catch (error) {
    console.error('Load grade error:', error)
    toast.error('Gagal memuat data nilai')
    tableData.value = []
  } finally {
    loading.value = false
  }
}

const loadSummaryData = async () => {
  loading.value = true
  try {
    const params = {
      start_date: summaryFilters.value.start_date,
      end_date: summaryFilters.value.end_date,
      company_id: summaryFilters.value.company_id || undefined
    }
    const response = await axios.get('/admin/reports/summary/json', { params })
    tableData.value = response.data.data || []
    currentPage.value = 1
    console.log('Summary data loaded:', tableData.value.length)
  } catch (error) {
    console.error('Load summary error:', error)
    toast.error('Gagal memuat data rekap')
    tableData.value = []
  } finally {
    loading.value = false
  }
}

const loadDataByTab = () => {
  switch (activeTab.value) {
    case 'attendance': loadAttendanceData(); break
    case 'logbook': loadLogbookData(); break
    case 'grade': loadGradeData(); break
    case 'summary': loadSummaryData(); break
  }
}

// Export functions
const exportToExcel = () => {
  if (tableData.value.length === 0) {
    toast.warning('Tidak ada data untuk diexport')
    return
  }
  
  const currentDate = new Date()
  const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']
  
  // Fungsi untuk mendapatkan warna background berdasarkan nilai
  const getBgColor = (col, value) => {
    if (col === 'Kehadiran (%)' || col === 'persentase') {
      const persen = parseFloat(value)
      if (persen >= 90) return 'bg-green'
      if (persen >= 75) return 'bg-yellow'
      return 'bg-red'
    }
    if (col === 'Status') {
      return value === 'LULUS' ? 'bg-green' : 'bg-red'
    }
    if (col === 'Hadir') return 'bg-green'
    if (col === 'Terlambat') return 'bg-yellow'
    if (col === 'Alpha') return 'bg-red'
    if (col === 'Sakit') return 'bg-blue'
    if (col === 'Izin') return 'bg-purple'
    return ''
  }
  
  let htmlContent = `<!DOCTYPE html>
  <html>
  <head>
    <meta charset="UTF-8">
    <title>${currentTabTitle.value}</title>
    <style>
      * { margin: 0; padding: 0; box-sizing: border-box; }
      body { font-family: 'Segoe UI', Arial, sans-serif; margin: 40px 30px; background: #fff; }
      .header { text-align: center; margin-bottom: 30px; }
      .logo { width: 70px; height: 70px; object-fit: contain; margin-bottom: 10px; }
      h1 { color: #1e3a5f; font-size: 24px; margin-bottom: 5px; }
      h2 { color: #2563eb; font-size: 18px; margin-bottom: 5px; }
      .subtitle { color: #6b7280; font-size: 13px; margin-bottom: 20px; }
      table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
      th { background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; padding: 12px 8px; border: 1px solid #3730a3; font-weight: bold; }
      td { border: 1px solid #e5e7eb; padding: 8px; }
      .text-center { text-align: center; }
      .bg-green { background-color: #dcfce7; }
      .bg-yellow { background-color: #fef9c3; }
      .bg-red { background-color: #fee2e2; }
      .bg-blue { background-color: #dbeafe; }
      .bg-purple { background-color: #f3e8ff; }
      .font-bold { font-weight: bold; }
      .footer { margin-top: 30px; text-align: center; color: #9ca3af; font-size: 10px; border-top: 1px solid #e5e7eb; padding-top: 20px; }
    </style>
  </head>
  <body>
    <div class="header">
      <h1>${currentTabTitle.value}</h1>
      <h2>SMKN 1 SUBANG</h2>
      <div class="subtitle">${formatDateRange.value} | Dicetak: ${currentDate.toLocaleDateString('id-ID')}</div>
    </div>
    <table>
      <thead>
        <tr>
          <th>No</th>`
  
  // Looping untuk header kolom
  tableColumns.value.forEach(col => {
    htmlContent += `<th>${col}</th>`
  })
  
  htmlContent += `</tr>
      </thead>
      <tbody>`
  
  // Looping untuk data
  tableData.value.forEach((row, idx) => {
    htmlContent += `<tr>`
    htmlContent += `<td class="text-center">${idx + 1}</td>`
    
    tableColumns.value.forEach(col => {
      let value = formatCellValue(row, col)
      const bgClass = getBgColor(col, value)
      htmlContent += `<td class="text-center ${bgClass}">${value}</td>`
    })
    
    htmlContent += `</tr>`
  })
  
  htmlContent += `
      </tbody>
    </table>
    <div class="footer">
      <p>Total Data: ${tableData.value.length} Siswa | Dicetak dari Sistem Monitoring PKL SMKN 1 Subang</p>
    </div>
  </body>
  </html>`
  
  // Download sebagai file .xls
  const blob = new Blob([htmlContent], { type: 'application/vnd.ms-excel' })
  const link = document.createElement('a')
  const url = URL.createObjectURL(blob)
  link.setAttribute('href', url)
  link.setAttribute('download', `${currentTabTitle.value}_${new Date().toISOString().split('T')[0]}.xls`)
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
  URL.revokeObjectURL(url)
  toast.success('Export Excel berhasil')
}

const exportToPDF = () => {
  if (tableData.value.length === 0) {
    toast.warning('Tidak ada data untuk diexport ke PDF')
    return
  }
  
  const printWindow = window.open('', '_blank')
  const currentDate = new Date()
  
  let htmlContent = `<!DOCTYPE html>
  <html>
  <head>
    <meta charset="UTF-8">
    <title>${currentTabTitle.value}</title>
    <style>
      * { margin: 0; padding: 0; box-sizing: border-box; }
      body { font-family: 'Segoe UI', Arial, sans-serif; margin: 30px 20px; background: #fff; }
      .header { text-align: center; margin-bottom: 25px; }
      .logo { width: 60px; height: 60px; object-fit: contain; margin-bottom: 8px; }
      h1 { color: #1e3a5f; font-size: 22px; margin-bottom: 4px; }
      h2 { color: #2563eb; font-size: 16px; margin-bottom: 4px; }
      .subtitle { color: #6b7280; font-size: 12px; margin-bottom: 15px; }
      table { width: 100%; border-collapse: collapse; font-size: 11px; }
      th { background: linear-gradient(135deg, #4f46e5, #7c3aed); color: white; padding: 8px 6px; border: 1px solid #3730a3; }
      td { border: 1px solid #e5e7eb; padding: 6px; }
      .text-center { text-align: center; }
      .bg-green { background: #dcfce7; }
      .bg-yellow { background: #fef9c3; }
      .bg-red { background: #fee2e2; }
      .bg-blue { background: #dbeafe; }
      .bg-purple { background: #f3e8ff; }
      .footer { margin-top: 25px; text-align: center; color: #9ca3af; font-size: 9px; border-top: 1px solid #e5e7eb; padding-top: 15px; }
      @media print { body { margin: 0; padding: 15px; } .no-print { display: none; } }
    </style>
  </head>
  <body>
    <div class="header">
      <img src="https://c.top4top.io/p_3749c8ad71.png" class="logo">
      <h1>${currentTabTitle.value}</h1>
      <h2>SMKN 1 SUBANG</h2>
      <div class="subtitle">${formatDateRange.value} | Dicetak: ${currentDate.toLocaleDateString('id-ID')}</div>
    </div>
    <table>
      <thead><tr>${tableColumns.value.map(col => `<th>${col}</th>`).join('')}</tr></thead>
      <tbody>`
  
  tableData.value.forEach((row, idx) => {
    htmlContent += `<tr><td class="text-center">${idx + 1}</td>`
    tableColumns.value.forEach(col => {
      htmlContent += `<td class="text-center">${formatCellValue(row, col)}</td>`
    })
    htmlContent += `</tr>`
  })
  
  htmlContent += `
      </tbody>
    </table>
    <div class="footer">
      <p>Total Data: ${tableData.value.length} Siswa | Dicetak dari Sistem Monitoring PKL SMKN 1 Subang</p>
    </div>
    <div class="no-print" style="text-align:center; margin-top:20px;">
      <button onclick="window.print()" style="padding:8px 20px; background:#4f46e5; color:white; border:none; border-radius:8px; cursor:pointer;">🖨️ Cetak / Simpan PDF</button>
    </div>
  </body>
  </html>`
  
  printWindow.document.write(htmlContent)
  printWindow.document.close()
  toast.success('PDF siap dicetak/disimpan')
}

// Pagination
const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }

// Load initial options
const loadInitialData = async () => {
  try {
    const [companiesRes, classesRes, studentsRes, statsRes] = await Promise.all([
      axios.get('/companies').catch(() => ({ data: [] })),
      axios.get('/admin/classes').catch(() => ({ data: [] })),
      axios.get('/admin/students').catch(() => ({ data: [] })),
      axios.get('/admin/dashboard/stats').catch(() => ({ data: {} }))
    ])
    companies.value = companiesRes.data?.data || companiesRes.data || []
    classes.value = classesRes.data?.data || classesRes.data || []
    students.value = studentsRes.data?.data || studentsRes.data || []
    
    const stats = statsRes.data?.data || statsRes.data || {}
    totalStudents.value = stats.total_students || 0
    totalLogbooks.value = stats.total_logbooks || 0
    averageGrade.value = stats.average_grade || 0
    attendanceRate.value = stats.attendance_rate || 0
    
    await loadDataByTab()
  } catch (error) {
    console.error('Failed to load initial data:', error)
  }
}

watch(activeTab, () => loadDataByTab())

onMounted(() => {
  loadInitialData()
})
</script>