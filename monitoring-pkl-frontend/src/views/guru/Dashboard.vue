<template>
  <div class="space-y-6">
    <!-- Welcome Banner with Date & Time -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-8 text-white shadow-xl">
      <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32 animate-pulse"></div>
      <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24 animate-pulse"></div>
      <div class="relative z-10">
        <div class="flex justify-between items-start flex-wrap gap-4">
          <div>
            <div class="flex items-center gap-2 mb-2">
              <span class="text-4xl">👋</span>
              <h1 class="text-2xl font-bold">Selamat Datang, {{ authStore.user?.name }}!</h1>
            </div>
            <p class="text-indigo-100">Berikut ringkasan bimbingan PKL Anda hari ini</p>
            <div class="flex flex-wrap gap-3 mt-4">
              <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2 hover:bg-white/30 transition">
                <p class="text-xs opacity-75">Total Siswa Bimbingan</p>
                <p class="font-bold text-2xl">{{ stats.students || 0 }}</p>
              </div>
              <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2 hover:bg-white/30 transition">
                <p class="text-xs opacity-75">Periode PKL Aktif</p>
                <p class="font-bold text-sm">{{ activePeriod }}</p>
              </div>
              <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2 hover:bg-white/30 transition">
                <p class="text-xs opacity-75">Hari ini</p>
                <p class="font-bold text-sm">{{ currentDate }}</p>
              </div>
            </div>
          </div>
          <div class="text-right hidden md:block bg-white/10 rounded-2xl px-6 py-3 backdrop-blur-sm">
            <p class="text-3xl font-bold">{{ currentTime }}</p>
            <p class="text-sm text-indigo-200">{{ currentDay }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 group border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Siswa Bimbingan</p>
            <p class="text-4xl font-bold text-indigo-600 mt-1">{{ stats.students || 0 }}</p>
            <p class="text-xs text-green-600 mt-2 flex items-center gap-1">
              <span class="inline-block w-2 h-2 bg-green-500 rounded-full"></span>
              Aktif semua
            </p>
          </div>
          <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
            <UserGroupIcon class="w-7 h-7 text-indigo-600" />
          </div>
        </div>
        <div class="mt-4 h-1.5 bg-gray-100 rounded-full overflow-hidden">
          <div class="w-full h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full"></div>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 group border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Hadir Hari Ini</p>
            <p class="text-4xl font-bold text-green-600 mt-1">{{ stats.present_today || 0 }}</p>
            <p class="text-xs text-gray-500 mt-2">Dari {{ stats.students || 0 }} siswa</p>
          </div>
          <div class="w-14 h-14 bg-green-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
            <CheckCircleIcon class="w-7 h-7 text-green-600" />
          </div>
        </div>
        <div class="mt-4 h-1.5 bg-gray-100 rounded-full overflow-hidden">
          <div class="h-full bg-gradient-to-r from-green-500 to-emerald-500 rounded-full" :style="{ width: attendancePercentage + '%' }"></div>
        </div>
        <p class="text-xs text-gray-500 mt-2">{{ attendancePercentage }}% tingkat kehadiran</p>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 group border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Logbook Perlu Review</p>
            <p class="text-4xl font-bold text-yellow-600 mt-1">{{ stats.pending_logbooks || 0 }}</p>
            <p class="text-xs text-yellow-600 mt-2 flex items-center gap-1">
              <span class="inline-block w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></span>
              Menunggu penilaian
            </p>
          </div>
          <div class="w-14 h-14 bg-yellow-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
            <BookOpenIcon class="w-7 h-7 text-yellow-600" />
          </div>
        </div>
        <div class="mt-4 h-1.5 bg-gray-100 rounded-full overflow-hidden">
          <div class="h-full bg-gradient-to-r from-yellow-500 to-orange-500 rounded-full" :style="{ width: pendingLogbookPercentage + '%' }"></div>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-all duration-300 group border border-gray-100">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm font-medium">Izin Menunggu</p>
            <p class="text-4xl font-bold text-orange-600 mt-1">{{ stats.pending_permissions || 0 }}</p>
            <p class="text-xs text-orange-600 mt-2 flex items-center gap-1">
              <span class="inline-block w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
              Perlu persetujuan
            </p>
          </div>
          <div class="w-14 h-14 bg-orange-100 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform">
            <DocumentTextIcon class="w-7 h-7 text-orange-600" />
          </div>
        </div>
      </div>
    </div>

    <!-- Charts & Top Students -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Performance Chart -->
      <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
        <div class="flex justify-between items-center mb-4 flex-wrap gap-3">
          <div>
            <h3 class="font-semibold text-gray-800 text-lg">Statistik Kehadiran Siswa</h3>
            <p class="text-sm text-gray-500">7 hari terakhir</p>
          </div>
          <div class="flex gap-2">
            <button @click="chartPeriod = 'week'" :class="chartPeriod === 'week' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600'" class="px-3 py-1 rounded-lg text-sm transition">
              Minggu
            </button>
            <button @click="chartPeriod = 'month'" :class="chartPeriod === 'month' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-600'" class="px-3 py-1 rounded-lg text-sm transition">
              Bulan
            </button>
          </div>
        </div>
        <div ref="attendanceChart" class="h-72 w-full"></div>
      </div>

      <!-- Top Students -->
      <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100">
        <div class="flex justify-between items-center mb-4">
          <div>
            <h3 class="font-semibold text-gray-800 text-lg">🏆 Top 5 Siswa Aktif</h3>
            <p class="text-sm text-gray-500">Berdasarkan jumlah logbook terbanyak</p>
          </div>
          <AcademicCapIcon class="w-8 h-8 text-indigo-400" />
        </div>
        <div class="space-y-3">
          <div v-for="(student, idx) in topStudents" :key="student.id" class="flex items-center gap-3 p-3 bg-gradient-to-r from-gray-50 to-white rounded-xl hover:shadow-md transition">
            <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-white shadow-md" :class="getTopStudentColor(idx)">
              {{ idx + 1 }}
            </div>
            <div class="flex-1">
              <p class="font-semibold text-gray-800">{{ student.name }}</p>
              <p class="text-xs text-gray-500">{{ student.company_name || 'Belum ditempatkan' }}</p>
            </div>
            <div class="text-right bg-indigo-50 px-4 py-2 rounded-xl">
              <p class="font-bold text-indigo-600 text-xl">{{ student.logbook_count }}</p>
              <p class="text-xs text-gray-500">Logbook</p>
            </div>
          </div>
          <div v-if="!topStudents.length" class="text-center py-12 text-gray-500">
            <UserGroupIcon class="w-16 h-16 mx-auto text-gray-300 mb-3" />
            <p>Belum ada data siswa</p>
            <p class="text-sm mt-1">Siswa akan muncul setelah memiliki logbook</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Pending Logbooks -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
      <div class="p-5 border-b bg-gradient-to-r from-gray-50 to-white">
        <div class="flex justify-between items-center flex-wrap gap-3">
          <div>
            <h3 class="font-semibold text-gray-800 text-lg">📝 Logbook Perlu Review</h3>
            <p class="text-sm text-gray-500">Logbook yang belum dinilai</p>
          </div>
          <router-link to="/guru/logbook-review" class="text-indigo-600 text-sm hover:underline flex items-center gap-1">
            Lihat semua 
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </router-link>
        </div>
      </div>
      <div class="divide-y">
        <div v-for="log in pendingLogbooks.slice(0, 5)" :key="log.id" class="p-5 hover:bg-gray-50 transition-all duration-200">
          <div class="flex justify-between items-start gap-4">
            <div class="flex-1">
              <div class="flex items-center gap-3 flex-wrap">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center text-indigo-600 font-bold">
                  {{ log.user_name?.charAt(0) || 'S' }}
                </div>
                <div>
                  <p class="font-semibold text-gray-800">{{ log.user_name }}</p>
                  <p class="text-xs text-gray-400">{{ log.date }}</p>
                </div>
                <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full">Menunggu Review</span>
              </div>
              <p class="text-sm text-gray-600 mt-2 ml-12">{{ log.activity }}</p>
            </div>
            <router-link :to="`/guru/logbook-review`" class="px-4 py-2 bg-indigo-600 text-white rounded-xl text-sm hover:bg-indigo-700 transition shadow-sm whitespace-nowrap">
              Review
            </router-link>
          </div>
        </div>
        <div v-if="!pendingLogbooks.length" class="p-10 text-center text-gray-500">
          <CheckCircleIcon class="w-16 h-16 mx-auto text-green-300 mb-3" />
          <p class="text-lg">Semua logbook sudah direview</p>
          <p class="text-sm mt-1">Bagus! Tidak ada logbook yang tertunda</p>
        </div>
      </div>
    </div>

    <!-- Pending Permissions -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
      <div class="p-5 border-b bg-gradient-to-r from-gray-50 to-white">
        <div class="flex justify-between items-center flex-wrap gap-3">
          <div>
            <h3 class="font-semibold text-gray-800 text-lg">📋 Izin Menunggu Persetujuan</h3>
            <p class="text-sm text-gray-500">Pengajuan izin/sakit yang belum diproses</p>
          </div>
          <router-link to="/guru/permissions" class="text-indigo-600 text-sm hover:underline flex items-center gap-1">
            Lihat semua
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
          </router-link>
        </div>
      </div>
      <div class="divide-y">
        <div v-for="perm in pendingPermissions.slice(0, 5)" :key="perm.id" class="p-5 hover:bg-gray-50 transition-all duration-200">
          <div class="flex justify-between items-start gap-4">
            <div class="flex-1">
              <div class="flex items-center gap-3 flex-wrap">
                <div class="w-10 h-10 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center text-gray-600 font-bold">
                  {{ perm.user_name?.charAt(0) || 'S' }}
                </div>
                <div>
                  <p class="font-semibold text-gray-800">{{ perm.user_name }}</p>
                  <p class="text-xs text-gray-400">{{ perm.date }}</p>
                </div>
                <span :class="perm.type === 'sick' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'" class="text-xs px-2 py-1 rounded-full">
                  {{ perm.type === 'sick' ? '🤒 Sakit' : '📝 Izin' }}
                </span>
              </div>
              <p class="text-sm text-gray-600 mt-2 ml-12">{{ perm.reason }}</p>
            </div>
            <div class="flex gap-2">
              <button @click="approvePermission(perm.id)" class="px-4 py-2 bg-green-600 text-white rounded-xl text-sm hover:bg-green-700 transition shadow-sm flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Setujui
              </button>
              <button @click="rejectPermission(perm.id)" class="px-4 py-2 bg-red-600 text-white rounded-xl text-sm hover:bg-red-700 transition shadow-sm flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Tolak
              </button>
            </div>
          </div>
        </div>
        <div v-if="!pendingPermissions.length" class="p-10 text-center text-gray-500">
          <CheckCircleIcon class="w-16 h-16 mx-auto text-green-300 mb-3" />
          <p class="text-lg">Semua izin sudah diproses</p>
          <p class="text-sm mt-1">Tidak ada pengajuan izin yang tertunda</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useAuthStore } from '../../stores/auth'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'
import { 
  UserGroupIcon, CheckCircleIcon, BookOpenIcon, DocumentTextIcon,
  AcademicCapIcon
} from '@heroicons/vue/24/outline'
import * as echarts from 'echarts'

const toast = useToast()
const authStore = useAuthStore()

// State
const stats = ref({ students: 0, present_today: 0, pending_logbooks: 0, pending_permissions: 0 })
const pendingLogbooks = ref([])
const pendingPermissions = ref([])
const topStudents = ref([])
const currentTime = ref('')
const currentDate = ref('')
const currentDay = ref('')
const chartPeriod = ref('week')
const attendanceChartData = ref([])

// Chart refs
const attendanceChart = ref(null)
let attendanceChartInstance = null

// Computed
const attendancePercentage = computed(() => {
  if (!stats.value.students || stats.value.students === 0) return 0
  return Math.round((stats.value.present_today / stats.value.students) * 100)
})

const pendingLogbookPercentage = computed(() => {
  const total = stats.value.pending_logbooks || 0
  return total > 0 ? Math.min(100, (total / 10) * 100) : 0
})

const activePeriod = computed(() => {
  const now = new Date()
  const year = now.getFullYear()
  return `Januari - Juni ${year}`
})

// Update date and time
const updateDateTime = () => {
  const now = new Date()
  currentDate.value = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
  currentTime.value = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
  currentDay.value = now.toLocaleDateString('id-ID', { weekday: 'long' })
}

// Helper functions
const getTopStudentColor = (index) => {
  const colors = [
    'bg-gradient-to-r from-yellow-500 to-amber-500',
    'bg-gradient-to-r from-gray-400 to-gray-500', 
    'bg-gradient-to-r from-orange-500 to-orange-600',
    'bg-gradient-to-r from-blue-500 to-blue-600',
    'bg-gradient-to-r from-green-500 to-emerald-600'
  ]
  return colors[index] || 'bg-gradient-to-r from-gray-400 to-gray-500'
}

// Fetch chart data
const fetchChartData = async () => {
  try {
    const res = await axios.get(`/guru/attendance/chart?period=${chartPeriod.value}`)
    attendanceChartData.value = res.data || []
    updateChart()
  } catch (error) {
    console.error('Failed to fetch chart data:', error)
    // Mock data
    attendanceChartData.value = [
      { day: 'Sen', hadir: 12, terlambat: 2 },
      { day: 'Sel', hadir: 15, terlambat: 1 },
      { day: 'Rab', hadir: 14, terlambat: 3 },
      { day: 'Kam', hadir: 16, terlambat: 2 },
      { day: 'Jum', hadir: 18, terlambat: 1 },
      { day: 'Sab', hadir: 10, terlambat: 0 },
      { day: 'Min', hadir: 8, terlambat: 1 }
    ]
    updateChart()
  }
}

// Update chart
const updateChart = () => {
  if (!attendanceChartInstance) return
  
  const days = attendanceChartData.value.map(d => d.day)
  const hadirData = attendanceChartData.value.map(d => d.hadir)
  const terlambatData = attendanceChartData.value.map(d => d.terlambat)
  
  attendanceChartInstance.setOption({
    tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
    legend: { data: ['Hadir', 'Terlambat'], bottom: 0 },
    grid: { left: '3%', right: '4%', bottom: '10%', containLabel: true },
    xAxis: { type: 'category', data: days, axisLabel: { fontSize: 12 } },
    yAxis: { type: 'value', name: 'Jumlah Siswa', nameLocation: 'middle', nameGap: 40 },
    series: [
      { name: 'Hadir', type: 'bar', data: hadirData, itemStyle: { borderRadius: [4, 4, 0, 0], color: '#10b981' } },
      { name: 'Terlambat', type: 'bar', data: terlambatData, itemStyle: { borderRadius: [4, 4, 0, 0], color: '#f59e0b' } }
    ]
  })
}

// Data fetching
const fetchDashboardData = async () => {
  try {
    const res = await axios.get('/guru/dashboard/stats')
    stats.value = res.data
  } catch (error) {
    console.error('Failed to fetch dashboard stats:', error)
  }
}

const fetchPendingLogbooks = async () => {
  try {
    const res = await axios.get('/guru/logbooks/pending')
    pendingLogbooks.value = res.data.data || res.data || []
  } catch (error) {
    console.error('Failed to fetch pending logbooks:', error)
  }
}

const fetchPendingPermissions = async () => {
  try {
    const res = await axios.get('/guru/permissions/pending')
    pendingPermissions.value = res.data.data || res.data || []
  } catch (error) {
    console.error('Failed to fetch pending permissions:', error)
  }
}

const fetchTopStudents = async () => {
  try {
    const res = await axios.get('/guru/top-students')
    topStudents.value = res.data.data || res.data || []
  } catch (error) {
    console.error('Failed to fetch top students:', error)
  }
}

// Actions
const approvePermission = async (id) => {
  try {
    await axios.put(`/guru/permissions/${id}/approve`)
    toast.success('✅ Izin disetujui')
    fetchPendingPermissions()
    fetchDashboardData()
  } catch (error) {
    toast.error('Gagal menyetujui izin')
  }
}

const rejectPermission = async (id) => {
  if (!confirm('Tolak pengajuan izin ini?')) return
  try {
    await axios.put(`/guru/permissions/${id}/reject`)
    toast.success('❌ Izin ditolak')
    fetchPendingPermissions()
    fetchDashboardData()
  } catch (error) {
    toast.error('Gagal menolak izin')
  }
}

// Initialize chart
const initChart = () => {
  if (attendanceChart.value) {
    if (attendanceChartInstance) {
      attendanceChartInstance.dispose()
    }
    attendanceChartInstance = echarts.init(attendanceChart.value)
    updateChart()
  }
}

// Handle resize
const handleResize = () => {
  attendanceChartInstance?.resize()
}

// Watch period change
watch(chartPeriod, () => {
  fetchChartData()
})

// Lifecycle
onMounted(() => {
  updateDateTime()
  setInterval(updateDateTime, 1000)
  fetchDashboardData()
  fetchPendingLogbooks()
  fetchPendingPermissions()
  fetchTopStudents()
  fetchChartData()
  setTimeout(initChart, 200)
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
  attendanceChartInstance?.dispose()
})
</script>

<style scoped>
/* Custom animations */
@keyframes pulse {
  0%, 100% { opacity: 0.3; }
  50% { opacity: 0.6; }
}
.animate-pulse {
  animation: pulse 3s ease-in-out infinite;
}
</style>