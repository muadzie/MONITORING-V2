<template>
  <div class="space-y-6">
    <!-- Welcome Banner with Date & Time -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 to-cyan-600 p-8 text-white">
      <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
      <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24"></div>
      <div class="relative z-10">
        <div class="flex justify-between items-start">
          <div>
            <h1 class="text-2xl font-bold">Selamat Datang, {{ authStore.user?.name }}!</h1>
            <p class="mt-2 text-blue-100">Berikut ringkasan bimbingan PKL Anda hari ini</p>
            <div class="flex gap-4 mt-4">
              <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                <p class="text-xs opacity-75">Total Siswa Bimbingan</p>
                <p class="font-bold text-xl">{{ stats.students || 0 }}</p>
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
            <p class="text-sm text-blue-200">{{ currentDay }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Siswa Bimbingan</p>
            <p class="text-3xl font-bold text-blue-600">{{ stats.students || 0 }}</p>
            <p class="text-xs text-green-600 mt-1">Total aktif</p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
            <UserGroupIcon class="w-6 h-6 text-blue-600" />
          </div>
        </div>
        <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
          <div class="w-full h-full bg-blue-600 rounded-full"></div>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Hadir Hari Ini</p>
            <p class="text-3xl font-bold text-green-600">{{ stats.present_today || 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">Dari {{ stats.students || 0 }} siswa</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
            <CheckCircleIcon class="w-6 h-6 text-green-600" />
          </div>
        </div>
        <div class="mt-3 h-1 bg-gray-100 rounded-full overflow-hidden">
          <div class="h-full bg-green-600 rounded-full" :style="{ width: attendancePercentage + '%' }"></div>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Logbook Perlu Review</p>
            <p class="text-3xl font-bold text-yellow-600">{{ stats.pending_logbooks || 0 }}</p>
            <p class="text-xs text-yellow-600 mt-1">Menunggu penilaian</p>
          </div>
          <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
            <BookOpenIcon class="w-6 h-6 text-yellow-600" />
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Izin Menunggu</p>
            <p class="text-3xl font-bold text-orange-600">{{ stats.pending_permissions || 0 }}</p>
            <p class="text-xs text-orange-600 mt-1">Perlu persetujuan</p>
          </div>
          <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
            <DocumentTextIcon class="w-6 h-6 text-orange-600" />
          </div>
        </div>
      </div>
    </div>

    <!-- Performance Chart -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <div class="bg-white rounded-2xl p-6 shadow-sm">
        <div class="flex justify-between items-center mb-4">
          <div>
            <h3 class="font-semibold text-gray-800">Statistik Kehadiran Siswa</h3>
            <p class="text-sm text-gray-500">7 hari terakhir</p>
          </div>
          <select class="text-sm border rounded-lg px-3 py-1.5 bg-gray-50">
            <option>Minggu Ini</option>
            <option>Bulan Ini</option>
          </select>
        </div>
        <div ref="attendanceChart" class="h-64"></div>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-sm">
        <div class="flex justify-between items-center mb-4">
          <div>
            <h3 class="font-semibold text-gray-800">Top 5 Siswa Aktif</h3>
            <p class="text-sm text-gray-500">Berdasarkan jumlah logbook</p>
          </div>
        </div>
        <div class="space-y-3">
          <div v-for="(student, idx) in topStudents" :key="student.id" class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold" :class="getTopStudentColor(idx)">
              {{ idx + 1 }}
            </div>
            <div class="flex-1">
              <p class="font-medium text-gray-800">{{ student.name }}</p>
              <p class="text-xs text-gray-500">{{ student.company_name || 'Belum ditempatkan' }}</p>
            </div>
            <div class="text-right">
              <p class="font-bold text-indigo-600">{{ student.logbook_count }}</p>
              <p class="text-xs text-gray-500">Logbook</p>
            </div>
          </div>
          <div v-if="!topStudents.length" class="text-center py-8 text-gray-500">
            <UserGroupIcon class="w-12 h-12 mx-auto text-gray-300 mb-2" />
            <p>Belum ada data siswa</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Logbook Perlu Review -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
      <div class="p-4 border-b flex justify-between items-center">
        <div>
          <h3 class="font-semibold text-gray-800">Logbook Perlu Review</h3>
          <p class="text-sm text-gray-500">Logbook yang belum dinilai</p>
        </div>
        <router-link to="/guru/logbook-review" class="text-blue-600 text-sm hover:underline">Lihat semua →</router-link>
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
            </div>
            <router-link :to="`/guru/logbook-review/${log.id}`" class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition">
              Review
            </router-link>
          </div>
        </div>
        <div v-if="!pendingLogbooks.length" class="p-8 text-center text-gray-500">
          <CheckCircleIcon class="w-12 h-12 mx-auto text-green-300 mb-2" />
          <p>Semua logbook sudah direview</p>
        </div>
      </div>
    </div>

    <!-- Izin Menunggu Persetujuan -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
      <div class="p-4 border-b flex justify-between items-center">
        <div>
          <h3 class="font-semibold text-gray-800">Izin Menunggu Persetujuan</h3>
          <p class="text-sm text-gray-500">Pengajuan izin/sakit yang belum diproses</p>
        </div>
        <router-link to="/guru/permissions" class="text-blue-600 text-sm hover:underline">Lihat semua →</router-link>
      </div>
      <div class="divide-y">
        <div v-for="perm in pendingPermissions.slice(0, 5)" :key="perm.id" class="p-4 hover:bg-gray-50 transition">
          <div class="flex justify-between items-start">
            <div>
              <div class="flex items-center gap-2">
                <p class="font-medium text-gray-800">{{ perm.user_name }}</p>
                <span :class="perm.type === 'sick' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'" class="text-xs px-2 py-0.5 rounded-full">
                  {{ perm.type === 'sick' ? 'Sakit' : 'Izin' }}
                </span>
              </div>
              <p class="text-sm text-gray-500 mt-1">{{ perm.date }} - {{ perm.reason }}</p>
            </div>
            <div class="flex gap-2">
              <button @click="approvePermission(perm.id)" class="px-3 py-1 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 transition">
                Setujui
              </button>
              <button @click="rejectPermission(perm.id)" class="px-3 py-1 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700 transition">
                Tolak
              </button>
            </div>
          </div>
        </div>
        <div v-if="!pendingPermissions.length" class="p-8 text-center text-gray-500">
          <CheckCircleIcon class="w-12 h-12 mx-auto text-green-300 mb-2" />
          <p>Semua izin sudah diproses</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'
import { 
  UserGroupIcon, CheckCircleIcon, BookOpenIcon, DocumentTextIcon,
  ChartBarIcon, AcademicCapIcon
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

// Chart refs
const attendanceChart = ref(null)
let attendanceChartInstance = null

// Computed
const attendancePercentage = computed(() => {
  if (!stats.value.students || stats.value.students === 0) return 0
  return Math.round((stats.value.present_today / stats.value.students) * 100)
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
  const colors = ['bg-yellow-500 text-white', 'bg-gray-400 text-white', 'bg-orange-500 text-white', 'bg-blue-500 text-white', 'bg-green-500 text-white']
  return colors[index] || 'bg-gray-300 text-gray-700'
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
    pendingLogbooks.value = res.data
  } catch (error) {
    console.error('Failed to fetch pending logbooks:', error)
  }
}

const fetchPendingPermissions = async () => {
  try {
    const res = await axios.get('/guru/permissions/pending')
    pendingPermissions.value = res.data
  } catch (error) {
    console.error('Failed to fetch pending permissions:', error)
  }
}

const fetchTopStudents = async () => {
  try {
    const res = await axios.get('/guru/top-students')
    topStudents.value = res.data
  } catch (error) {
    console.error('Failed to fetch top students:', error)
    // Mock data for demo
    topStudents.value = [
      { id: 1, name: 'Ahmad Fauzi', company_name: 'PT. Teknologi Nusantara', logbook_count: 25 },
      { id: 2, name: 'Siti Nurhaliza', company_name: 'PT. Teknologi Nusantara', logbook_count: 23 },
      { id: 3, name: 'Rizki Ramadhan', company_name: 'CV. Karya Mandiri', logbook_count: 20 }
    ]
  }
}

// Actions
const approvePermission = async (id) => {
  try {
    await axios.put(`/guru/permissions/${id}/approve`)
    toast.success('Izin disetujui')
    fetchPendingPermissions()
    fetchDashboardData()
  } catch (error) {
    toast.error('Gagal menyetujui izin')
  }
}

const rejectPermission = async (id) => {
  try {
    await axios.put(`/guru/permissions/${id}/reject`)
    toast.success('Izin ditolak')
    fetchPendingPermissions()
    fetchDashboardData()
  } catch (error) {
    toast.error('Gagal menolak izin')
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
      yAxis: { type: 'value', name: 'Jumlah Siswa', nameLocation: 'middle', nameGap: 40 },
      series: [
        { name: 'Hadir', type: 'bar', data: [12, 15, 14, 16, 18, 10, 8], itemStyle: { borderRadius: [4, 4, 0, 0], color: '#10b981' } },
        { name: 'Terlambat', type: 'bar', data: [2, 1, 3, 2, 1, 0, 1], itemStyle: { borderRadius: [4, 4, 0, 0], color: '#f59e0b' } }
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
  fetchDashboardData()
  fetchPendingLogbooks()
  fetchPendingPermissions()
  fetchTopStudents()
  setTimeout(initAttendanceChart, 100)
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
  attendanceChartInstance?.dispose()
})
</script>