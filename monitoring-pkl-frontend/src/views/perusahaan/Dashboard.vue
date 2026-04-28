<template>
  <div class="space-y-6">
    <!-- Welcome Banner with Date & Time -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-purple-600 to-pink-600 p-8 text-white">
      <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
      <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24"></div>
      <div class="relative z-10">
        <div class="flex justify-between items-start">
          <div>
            <h1 class="text-2xl font-bold">Selamat Datang, {{ authStore.user?.name }}!</h1>
            <p class="mt-2 text-purple-100">Berikut ringkasan monitoring siswa magang di perusahaan Anda</p>
            <div class="flex flex-wrap gap-4 mt-4">
              <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                <p class="text-xs opacity-75">Perusahaan</p>
                <p class="font-bold text-sm">{{ authStore.user?.company?.name || '-' }}</p>
              </div>
              <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                <p class="text-xs opacity-75">Periode PKL</p>
                <p class="font-bold text-sm">Januari - Maret 2026</p>
              </div>
              <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                <p class="text-xs opacity-75">Hari ini</p>
                <p class="font-bold text-sm">{{ currentDate }}</p>
              </div>
            </div>
          </div>
          <div class="text-right hidden md:block">
            <p class="text-3xl font-bold">{{ currentTime }}</p>
            <p class="text-sm text-purple-200">{{ currentDay }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Total Siswa Magang</p>
            <p class="text-3xl font-bold text-purple-600">{{ stats.students || 0 }}</p>
            <p class="text-xs text-green-600 mt-1">Siswa aktif</p>
          </div>
          <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
            <UserGroupIcon class="w-6 h-6 text-purple-600" />
          </div>
        </div>
        <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
          <div class="w-full h-full bg-purple-600 rounded-full"></div>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Total Logbook</p>
            <p class="text-3xl font-bold text-pink-600">{{ stats.logbooks || 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">Dari semua siswa</p>
          </div>
          <div class="w-12 h-12 bg-pink-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
            <BookOpenIcon class="w-6 h-6 text-pink-600" />
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Perlu Dinilai</p>
            <p class="text-3xl font-bold text-yellow-600">{{ stats.pending_assessment || 0 }}</p>
            <p class="text-xs text-yellow-600 mt-1">Menunggu penilaian</p>
          </div>
          <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
            <ClipboardDocumentListIcon class="w-6 h-6 text-yellow-600" />
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Rata-rata Nilai</p>
            <p class="text-3xl font-bold text-green-600">{{ stats.average_grade || 0 }}</p>
            <p class="text-xs text-green-600 mt-1">Nilai akhir</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
            <StarIcon class="w-6 h-6 text-green-600" />
          </div>
        </div>
      </div>
    </div>

    <!-- Chart & Top Students -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Logbook Activity Chart -->
      <div class="bg-white rounded-2xl p-6 shadow-sm">
        <div class="flex justify-between items-center mb-4">
          <div>
            <h3 class="font-semibold text-gray-800">Aktivitas Logbook</h3>
            <p class="text-sm text-gray-500">7 hari terakhir</p>
          </div>
          <select v-model="chartPeriod" class="text-sm border rounded-lg px-3 py-1.5 bg-gray-50">
            <option value="week">Minggu Ini</option>
            <option value="month">Bulan Ini</option>
          </select>
        </div>
        <div ref="logbookChart" class="h-64"></div>
      </div>

      <!-- Top Students -->
      <div class="bg-white rounded-2xl p-6 shadow-sm">
        <div class="flex justify-between items-center mb-4">
          <div>
            <h3 class="font-semibold text-gray-800">Top 5 Siswa Aktif</h3>
            <p class="text-sm text-gray-500">Berdasarkan jumlah logbook</p>
          </div>
          <router-link to="/perusahaan/progress" class="text-purple-600 text-sm hover:underline">Lihat semua →</router-link>
        </div>
        <div class="space-y-3">
          <div v-for="(student, idx) in topStudents" :key="student.id" class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold" :class="getTopStudentColor(idx)">
              {{ idx + 1 }}
            </div>
            <div class="flex-1">
              <p class="font-medium text-gray-800">{{ student.name }}</p>
              <p class="text-xs text-gray-500">{{ student.nisn || '-' }}</p>
            </div>
            <div class="text-right">
              <p class="font-bold text-purple-600">{{ student.logbook_count || 0 }}</p>
              <p class="text-xs text-gray-500">Logbook</p>
            </div>
            <router-link :to="`/perusahaan/assessment/${student.id}`" class="px-3 py-1 bg-purple-600 text-white rounded-lg text-sm hover:bg-purple-700 transition">
              Nilai
            </router-link>
          </div>
          <div v-if="!topStudents.length" class="text-center py-8 text-gray-500">
            <UserGroupIcon class="w-12 h-12 mx-auto text-gray-300 mb-2" />
            <p>Belum ada siswa magang</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Logbooks Need Assessment -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
      <div class="p-4 border-b flex justify-between items-center">
        <div>
          <h3 class="font-semibold text-gray-800">Logbook Perlu Dinilai</h3>
          <p class="text-sm text-gray-500">Logbook yang belum dinilai oleh perusahaan</p>
        </div>
        <router-link to="/perusahaan/assessment" class="text-purple-600 text-sm hover:underline">Lihat semua →</router-link>
      </div>
      <div class="divide-y">
        <div v-for="log in pendingLogbooks.slice(0, 5)" :key="log.id" class="p-4 hover:bg-gray-50 transition">
          <div class="flex justify-between items-start">
            <div class="flex-1">
              <div class="flex items-center gap-2">
                <p class="font-medium text-gray-800">{{ log.user_name }}</p>
                <span class="text-xs text-gray-400">{{ log.date }}</span>
              </div>
              <p class="text-sm text-gray-600 mt-1">{{ log.activity }}</p>
              <p class="text-xs text-gray-400 mt-1">Diajukan: {{ formatDate(log.created_at) }}</p>
            </div>
            <router-link :to="`/perusahaan/assessment/${log.user_id}`" class="px-3 py-1 bg-purple-600 text-white rounded-lg text-sm hover:bg-purple-700 transition">
              Nilai
            </router-link>
          </div>
        </div>
        <div v-if="!pendingLogbooks.length" class="p-8 text-center text-gray-500">
          <CheckCircleIcon class="w-12 h-12 mx-auto text-green-300 mb-2" />
          <p>Semua logbook sudah dinilai</p>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <router-link to="/perusahaan/assessment" class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition group">
        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
          <ClipboardDocumentListIcon class="w-5 h-5 text-purple-600" />
        </div>
        <div>
          <p class="font-semibold text-gray-800">Penilaian Logbook</p>
          <p class="text-xs text-gray-500">Nilai logbook siswa magang</p>
        </div>
      </router-link>
      <router-link to="/perusahaan/progress" class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition group">
        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
          <ChartBarIcon class="w-5 h-5 text-green-600" />
        </div>
        <div>
          <p class="font-semibold text-gray-800">Progress Siswa</p>
          <p class="text-xs text-gray-500">Lihat perkembangan siswa</p>
        </div>
      </router-link>
      <router-link to="/perusahaan/report" class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition group">
        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
          <DocumentChartBarIcon class="w-5 h-5 text-orange-600" />
        </div>
        <div>
          <p class="font-semibold text-gray-800">Laporan PKL</p>
          <p class="text-xs text-gray-500">Download laporan lengkap</p>
        </div>
      </router-link>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import axios from '../../plugins/axios'
import { 
  UserGroupIcon, BookOpenIcon, ClipboardDocumentListIcon, StarIcon,
  ChartBarIcon, DocumentChartBarIcon, CheckCircleIcon
} from '@heroicons/vue/24/outline'
import * as echarts from 'echarts'

const authStore = useAuthStore()

// State
const stats = ref({ students: 0, logbooks: 0, pending_assessment: 0, average_grade: 0 })
const topStudents = ref([])
const pendingLogbooks = ref([])
const chartPeriod = ref('week')
const currentTime = ref('')
const currentDate = ref('')
const currentDay = ref('')

// Chart refs
const logbookChart = ref(null)
let logbookChartInstance = null

// Update date and time
const updateDateTime = () => {
  const now = new Date()
  currentDate.value = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
  currentTime.value = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
  currentDay.value = now.toLocaleDateString('id-ID', { weekday: 'long' })
}

// Helper functions
const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID')
}

const getTopStudentColor = (index) => {
  const colors = ['bg-yellow-500 text-white', 'bg-gray-400 text-white', 'bg-orange-500 text-white', 'bg-blue-500 text-white', 'bg-green-500 text-white']
  return colors[index] || 'bg-gray-300 text-gray-700'
}

// Data fetching
const fetchDashboardStats = async () => {
  try {
    const res = await axios.get('/perusahaan/dashboard/stats')
    stats.value = res.data
  } catch (error) {
    console.error('Failed to fetch dashboard stats:', error)
  }
}

const fetchTopStudents = async () => {
  try {
    const res = await axios.get('/perusahaan/top-students')
    topStudents.value = res.data
  } catch (error) {
    console.error('Failed to fetch top students:', error)
  }
}

const fetchPendingLogbooks = async () => {
  try {
    const res = await axios.get('/perusahaan/logbooks/pending')
    pendingLogbooks.value = res.data
  } catch (error) {
    console.error('Failed to fetch pending logbooks:', error)
  }
}

// Initialize chart
const initLogbookChart = () => {
  if (logbookChart.value) {
    logbookChartInstance = echarts.init(logbookChart.value)
    logbookChartInstance.setOption({
      tooltip: { trigger: 'axis' },
      grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
      xAxis: { type: 'category', data: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'], axisLabel: { fontSize: 12 } },
      yAxis: { type: 'value', name: 'Jumlah Logbook' },
      series: [{
        type: 'line', data: [5, 8, 12, 9, 15, 6, 4], smooth: true,
        lineStyle: { color: '#a855f7', width: 3 },
        areaStyle: { opacity: 0.1, color: '#a855f7' },
        symbol: 'circle', symbolSize: 8,
        itemStyle: { color: '#a855f7' }
      }]
    })
  }
}

// Handle resize
const handleResize = () => {
  logbookChartInstance?.resize()
}

// Lifecycle
onMounted(() => {
  updateDateTime()
  setInterval(updateDateTime, 1000)
  fetchDashboardStats()
  fetchTopStudents()
  fetchPendingLogbooks()
  setTimeout(initLogbookChart, 100)
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
  logbookChartInstance?.dispose()
})
</script>