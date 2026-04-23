<template>
  <div class="space-y-6">
    <!-- Welcome Banner with Date & Time -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-green-600 to-teal-600 p-8 text-white">
      <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
      <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24"></div>
      <div class="relative z-10">
        <div class="flex justify-between items-start">
          <div>
            <h1 class="text-2xl font-bold">Selamat Datang, {{ authStore.user?.name }}!</h1>
            <p class="mt-2 text-green-100">Semangat menjalani PKL! Catat kegiatanmu setiap hari.</p>
            <div class="flex flex-wrap gap-4 mt-4">
              <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                <p class="text-xs opacity-75">Perusahaan PKL</p>
                <p class="font-semibold text-sm">{{ authStore.user?.company?.name || 'Belum ditempatkan' }}</p>
              </div>
              <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                <p class="text-xs opacity-75">Periode PKL</p>
                <p class="font-semibold text-sm">Januari - Maret 2026</p>
              </div>
              <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                <p class="text-xs opacity-75">Hari ini</p>
                <p class="font-semibold text-sm">{{ currentDate }}</p>
              </div>
            </div>
          </div>
          <div class="text-right hidden md:block">
            <p class="text-3xl font-bold">{{ currentTime }}</p>
            <p class="text-sm text-green-200">{{ currentDay }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Kehadiran</p>
            <p class="text-3xl font-bold text-green-600">{{ stats.attendance || 0 }}%</p>
            <p class="text-xs text-gray-500 mt-1">Target 100%</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
            <CheckCircleIcon class="w-6 h-6 text-green-600" />
          </div>
        </div>
        <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
          <div class="h-full bg-green-600 rounded-full" :style="{ width: (stats.attendance || 0) + '%' }"></div>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Total Logbook</p>
            <p class="text-3xl font-bold text-blue-600">{{ stats.logbooks || 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">Dari target 30</p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
            <BookOpenIcon class="w-6 h-6 text-blue-600" />
          </div>
        </div>
        <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
          <div class="h-full bg-blue-600 rounded-full" :style="{ width: ((stats.logbooks || 0) / 30 * 100) + '%' }"></div>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Rata-rata Nilai</p>
            <p class="text-3xl font-bold text-purple-600">{{ stats.grade || 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">Nilai akhir</p>
          </div>
          <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
            <StarIcon class="w-6 h-6 text-purple-600" />
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Logbook Disetujui</p>
            <p class="text-3xl font-bold text-yellow-600">{{ stats.approved_logbooks || 0 }}</p>
            <p class="text-xs text-green-600 mt-1" v-if="stats.pending_logbooks > 0">Menunggu: {{ stats.pending_logbooks }}</p>
            <p class="text-xs text-green-600 mt-1" v-else>Semua disetujui ✓</p>
          </div>
          <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
            <ClipboardDocumentCheckIcon class="w-6 h-6 text-yellow-600" />
          </div>
        </div>
      </div>
    </div>

    <!-- Chart & Info -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Attendance Chart -->
      <div class="bg-white rounded-2xl p-6 shadow-sm">
        <div class="flex justify-between items-center mb-4">
          <div>
            <h3 class="font-semibold text-gray-800">Statistik Kehadiran</h3>
            <p class="text-sm text-gray-500">7 hari terakhir</p>
          </div>
          <select v-model="chartPeriod" class="text-sm border rounded-lg px-3 py-1.5 bg-gray-50">
            <option value="week">Minggu Ini</option>
            <option value="month">Bulan Ini</option>
          </select>
        </div>
        <div ref="attendanceChart" class="h-64"></div>
      </div>

      <!-- Company Info Card -->
      <div class="bg-white rounded-2xl p-6 shadow-sm">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
            <BuildingOffice2Icon class="w-5 h-5 text-indigo-600" />
          </div>
          <h3 class="font-semibold text-gray-800">Informasi Perusahaan</h3>
        </div>
        
        <div v-if="authStore.user?.company" class="space-y-3">
          <div class="flex justify-between items-center pb-2 border-b">
            <span class="text-gray-500 text-sm">Nama Perusahaan</span>
            <span class="font-medium">{{ authStore.user.company.name }}</span>
          </div>
          <div class="flex justify-between items-center pb-2 border-b">
            <span class="text-gray-500 text-sm">Alamat</span>
            <span class="font-medium text-sm text-right">{{ authStore.user.company.address }}</span>
          </div>
          <div class="flex justify-between items-center pb-2 border-b">
            <span class="text-gray-500 text-sm">Radius Absensi</span>
            <span class="font-medium">{{ authStore.user.company.radius }} meter</span>
          </div>
          <div class="mt-3">
            <router-link to="/siswa/company" class="text-indigo-600 text-sm hover:underline">Lihat detail →</router-link>
          </div>
        </div>
        <div v-else class="text-center py-8 text-gray-500">
          <BuildingOffice2Icon class="w-12 h-12 mx-auto text-gray-300 mb-2" />
          <p>Belum ada informasi perusahaan</p>
          <p class="text-xs mt-1">Hubungi admin untuk penempatan PKL</p>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <router-link to="/siswa/attendance" class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition group">
        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
          <MapPinIcon class="w-5 h-5 text-green-600" />
        </div>
        <div>
          <p class="font-semibold text-gray-800">Absensi Hari Ini</p>
          <p class="text-xs text-gray-500">Lakukan check-in/out</p>
        </div>
      </router-link>
      <router-link to="/siswa/logbook" class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition group">
        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
          <BookOpenIcon class="w-5 h-5 text-blue-600" />
        </div>
        <div>
          <p class="font-semibold text-gray-800">Buat Logbook</p>
          <p class="text-xs text-gray-500">Catat kegiatan hari ini</p>
        </div>
      </router-link>
      <router-link to="/siswa/permission" class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition group">
        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
          <DocumentTextIcon class="w-5 h-5 text-yellow-600" />
        </div>
        <div>
          <p class="font-semibold text-gray-800">Ajukan Izin</p>
          <p class="text-xs text-gray-500">Izin sakit atau keperluan lain</p>
        </div>
      </router-link>
      <router-link to="/siswa/final-report" class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition group">
        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
          <DocumentArrowUpIcon class="w-5 h-5 text-purple-600" />
        </div>
        <div>
          <p class="font-semibold text-gray-800">Upload Laporan</p>
          <p class="text-xs text-gray-500">Laporan akhir PKL</p>
        </div>
      </router-link>
    </div>

    <!-- Recent Logbook -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
      <div class="p-4 border-b flex justify-between items-center">
        <div>
          <h3 class="text-lg font-semibold">Logbook Terbaru</h3>
          <p class="text-sm text-gray-500">5 logbook terakhir Anda</p>
        </div>
        <router-link to="/siswa/logbook" class="text-green-600 text-sm hover:underline">Lihat semua →</router-link>
      </div>
      <div class="divide-y">
        <div v-for="log in recentLogbooks" :key="log.id" class="p-4 hover:bg-gray-50 transition">
          <div class="flex justify-between items-start">
            <div class="flex-1">
              <div class="flex items-center gap-2">
                <p class="font-medium text-gray-800">{{ log.activity }}</p>
                <span :class="log.status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'" class="px-2 py-0.5 rounded-full text-xs">
                  {{ log.status === 'approved' ? 'Disetujui' : 'Menunggu' }}
                </span>
              </div>
              <p class="text-xs text-gray-500 mt-1">{{ formatDate(log.date) }}</p>
              <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ log.description }}</p>
            </div>
            <div v-if="log.grade" class="ml-4 text-center">
              <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <span class="text-lg font-bold text-yellow-600">{{ log.grade }}</span>
              </div>
              <p class="text-xs text-gray-500 mt-1">Nilai</p>
            </div>
          </div>
        </div>
        <div v-if="!recentLogbooks.length" class="p-12 text-center text-gray-500">
          <BookOpenIcon class="w-12 h-12 mx-auto text-gray-300 mb-3" />
          <p>Belum ada logbook</p>
          <router-link to="/siswa/logbook" class="text-green-600 text-sm mt-2 inline-block">Buat logbook pertama →</router-link>
        </div>
      </div>
    </div>

    <!-- Achievement Badges -->
    <div class="bg-white rounded-2xl p-6 shadow-sm">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
          <TrophyIcon class="w-5 h-5 text-yellow-600" />
        </div>
        <h3 class="font-semibold text-gray-800">Pencapaian Anda</h3>
      </div>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="text-center p-3 bg-gray-50 rounded-xl">
          <div class="text-2xl mb-1">📝</div>
          <p class="font-bold text-gray-800">{{ stats.logbooks || 0 }}</p>
          <p class="text-xs text-gray-500">Logbook</p>
        </div>
        <div class="text-center p-3 bg-gray-50 rounded-xl">
          <div class="text-2xl mb-1">✅</div>
          <p class="font-bold text-gray-800">{{ stats.approved_logbooks || 0 }}</p>
          <p class="text-xs text-gray-500">Disetujui</p>
        </div>
        <div class="text-center p-3 bg-gray-50 rounded-xl">
          <div class="text-2xl mb-1">⭐</div>
          <p class="font-bold text-gray-800">{{ stats.grade || 0 }}</p>
          <p class="text-xs text-gray-500">Rata-rata</p>
        </div>
        <div class="text-center p-3 bg-gray-50 rounded-xl">
          <div class="text-2xl mb-1">🎯</div>
          <p class="font-bold text-gray-800">{{ Math.round((stats.logbooks || 0) / 30 * 100) }}%</p>
          <p class="text-xs text-gray-500">Progress</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import axios from '../../plugins/axios'
import { 
  CheckCircleIcon, BookOpenIcon, StarIcon, ChartBarIcon, 
  MapPinIcon, DocumentTextIcon, BuildingOffice2Icon,
  ClipboardDocumentCheckIcon, DocumentArrowUpIcon, TrophyIcon
} from '@heroicons/vue/24/outline'
import * as echarts from 'echarts'

const authStore = useAuthStore()

// State
const stats = ref({ attendance: 0, logbooks: 0, grade: 0, approved_logbooks: 0, pending_logbooks: 0 })
const recentLogbooks = ref([])
const chartPeriod = ref('week')
const currentTime = ref('')
const currentDate = ref('')
const currentDay = ref('')

// Chart refs
const attendanceChart = ref(null)
let attendanceChartInstance = null

// Update date and time
const updateDateTime = () => {
  const now = new Date()
  currentDate.value = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
  currentTime.value = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
  currentDay.value = now.toLocaleDateString('id-ID', { weekday: 'long' })
}

// Helper functions
const formatDate = (date) => new Date(date).toLocaleDateString('id-ID')

// Data fetching
const fetchDashboardStats = async () => {
  try {
    const res = await axios.get('/siswa/dashboard/stats')
    stats.value = res.data
  } catch (error) {
    console.error('Failed to load dashboard stats:', error)
  }
}

const fetchRecentLogbooks = async () => {
  try {
    const res = await axios.get('/siswa/logbooks/recent')
    recentLogbooks.value = res.data
  } catch (error) {
    console.error('Failed to load recent logbooks:', error)
  }
}

// Initialize chart
const initAttendanceChart = () => {
  if (attendanceChart.value) {
    attendanceChartInstance = echarts.init(attendanceChart.value)
    attendanceChartInstance.setOption({
      tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
      grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
      xAxis: { type: 'category', data: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'], axisLabel: { fontSize: 12 } },
      yAxis: { type: 'value', name: 'Status', nameLocation: 'middle', nameGap: 40 },
      series: [
        { name: 'Hadir', type: 'bar', data: [5, 5, 4, 5, 5, 3, 2], itemStyle: { borderRadius: [4, 4, 0, 0], color: '#10b981' } },
        { name: 'Terlambat', type: 'bar', data: [0, 0, 1, 0, 0, 0, 0], itemStyle: { borderRadius: [4, 4, 0, 0], color: '#f59e0b' } }
      ]
    })
  }
}

// Handle resize
const handleResize = () => {
  attendanceChartInstance?.resize()
}

// Lifecycle
onMounted(() => {
  updateDateTime()
  setInterval(updateDateTime, 1000)
  fetchDashboardStats()
  fetchRecentLogbooks()
  setTimeout(initAttendanceChart, 100)
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
  attendanceChartInstance?.dispose()
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>