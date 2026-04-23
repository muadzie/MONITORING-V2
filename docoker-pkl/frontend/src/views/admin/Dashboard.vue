<template>
  <div class="space-y-6">
    <!-- Welcome Banner with Date & Time -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-8 text-white">
      <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
      <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24"></div>
      <div class="relative z-10 flex justify-between items-start">
        <div>
          <h1 class="text-2xl md:text-3xl font-bold">Selamat Datang, {{ authStore.user?.name }}!</h1>
          <p class="mt-2 text-indigo-100">Semoga harimu menyenangkan. Berikut ringkasan aktivitas PKL hari ini.</p>
          <div class="flex gap-4 mt-4">
            <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
              <p class="text-xs opacity-75">Periode PKL</p>
              <p class="font-semibold">Januari - Maret 2026</p>
            </div>
            <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
              <p class="text-xs opacity-75">Minggu ke-</p>
              <p class="font-semibold">12</p>
            </div>
            <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
              <p class="text-xs opacity-75">Hari ini</p>
              <p class="font-semibold">{{ currentDate }}</p>
            </div>
          </div>
        </div>
        <div class="text-right hidden md:block">
          <p class="text-3xl font-bold">{{ currentTime }}</p>
          <p class="text-sm text-indigo-200">{{ currentDay }}</p>
        </div>
      </div>
    </div>

    <!-- Stats Cards with Icons & Trends -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Total Siswa</p>
            <p class="text-3xl font-bold text-gray-800">{{ stats.total_students || 0 }}</p>
            <p class="text-xs text-green-600 mt-1 flex items-center gap-1">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
              +12% dari bulan lalu
            </p>
          </div>
          <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
            <AcademicCapIcon class="w-6 h-6 text-indigo-600" />
          </div>
        </div>
        <div class="mt-4 h-1 bg-gray-100 rounded-full overflow-hidden">
          <div class="w-3/4 h-full bg-indigo-600 rounded-full"></div>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Total Guru</p>
            <p class="text-3xl font-bold text-gray-800">{{ stats.total_teachers || 0 }}</p>
            <p class="text-xs text-blue-600 mt-1">Pembimbing PKL</p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
            <UserGroupIcon class="w-6 h-6 text-blue-600" />
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Perusahaan</p>
            <p class="text-3xl font-bold text-gray-800">{{ stats.total_companies || 0 }}</p>
            <p class="text-xs text-purple-600 mt-1">Mitra PKL</p>
          </div>
          <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
            <BuildingOffice2Icon class="w-6 h-6 text-purple-600" />
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-xl transition-all duration-300 group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Total Logbook</p>
            <p class="text-3xl font-bold text-gray-800">{{ stats.total_logbooks || 0 }}</p>
            <p class="text-xs text-yellow-600 mt-1">Perlu review: {{ stats.pending_logbooks || 0 }}</p>
          </div>
          <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
            <BookOpenIcon class="w-6 h-6 text-yellow-600" />
          </div>
        </div>
      </div>
    </div>

    <!-- Today's Attendance Stats -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
      <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Hadir</p>
        <p class="text-3xl font-bold">{{ attendanceStats.present || 0 }}</p>
        <p class="text-xs opacity-80 mt-1">Siswa</p>
      </div>
      <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Terlambat</p>
        <p class="text-3xl font-bold">{{ attendanceStats.late || 0 }}</p>
        <p class="text-xs opacity-80 mt-1">Siswa</p>
      </div>
      <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Alpha</p>
        <p class="text-3xl font-bold">{{ attendanceStats.absent || 0 }}</p>
        <p class="text-xs opacity-80 mt-1">Siswa</p>
      </div>
      <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Sakit</p>
        <p class="text-3xl font-bold">{{ attendanceStats.sick || 0 }}</p>
        <p class="text-xs opacity-80 mt-1">Siswa</p>
      </div>
      <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Izin</p>
        <p class="text-3xl font-bold">{{ attendanceStats.permit || 0 }}</p>
        <p class="text-xs opacity-80 mt-1">Siswa</p>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Attendance Chart -->
      <div class="bg-white rounded-2xl p-6 shadow-sm">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h3 class="text-lg font-semibold text-gray-800">Statistik Kehadiran</h3>
            <p class="text-sm text-gray-500">Data kehadiran siswa PKL</p>
          </div>
          <select class="text-sm border rounded-lg px-3 py-1.5 bg-gray-50">
            <option>Bulan Ini</option>
            <option>Bulan Lalu</option>
            <option>Tahun Ini</option>
          </select>
        </div>
        <div ref="attendanceChart" class="h-80"></div>
      </div>

      <!-- Logbook Chart -->
      <div class="bg-white rounded-2xl p-6 shadow-sm">
        <div class="flex justify-between items-center mb-6">
          <div>
            <h3 class="text-lg font-semibold text-gray-800">Statistik Logbook</h3>
            <p class="text-sm text-gray-500">Jumlah logbook per minggu</p>
          </div>
          <select class="text-sm border rounded-lg px-3 py-1.5 bg-gray-50">
            <option>Per Minggu</option>
            <option>Per Bulan</option>
          </select>
        </div>
        <div ref="logbookChart" class="h-80"></div>
      </div>
    </div>

    <!-- Second Row Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Company Distribution -->
      <div class="bg-white rounded-2xl p-6 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Distribusi Siswa per Perusahaan</h3>
        <div ref="companyChart" class="h-64"></div>
      </div>

      <!-- Recent Activities -->
      <div class="bg-white rounded-2xl p-6 shadow-sm lg:col-span-2">
        <div class="flex justify-between items-center mb-4">
          <div>
            <h3 class="text-lg font-semibold text-gray-800">Aktivitas Terbaru</h3>
            <p class="text-sm text-gray-500">Aktivitas siswa dalam 7 hari terakhir</p>
          </div>
          <button class="text-indigo-600 text-sm hover:underline">Lihat semua</button>
        </div>
        <div class="space-y-3">
          <div v-for="activity in recentActivities" :key="activity.id" class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition">
            <div class="w-10 h-10 rounded-full flex items-center justify-center" :class="activity.bgColor">
              <component :is="activity.icon" class="w-5 h-5" :class="activity.iconColor" />
            </div>
            <div class="flex-1">
              <p class="text-sm font-medium text-gray-800">{{ activity.description }}</p>
              <p class="text-xs text-gray-500">{{ activity.user }} • {{ activity.time }}</p>
            </div>
            <span class="text-xs px-2 py-1 rounded-full" :class="activity.statusClass">{{ activity.status }}</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Top Students Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
      <div class="p-6 border-b">
        <h3 class="text-lg font-semibold text-gray-800">Siswa dengan Logbook Terbanyak</h3>
        <p class="text-sm text-gray-500">10 siswa dengan aktivitas logbook tertinggi</p>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Rank</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Nama Siswa</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Perusahaan</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Total Logbook</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Rata-rata Nilai</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500">Progress</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="(student, idx) in topStudents" :key="student.id" class="hover:bg-gray-50">
              <td class="px-6 py-4">
                <div class="w-8 h-8 rounded-full flex items-center justify-center" :class="idx === 0 ? 'bg-yellow-100 text-yellow-700' : idx === 1 ? 'bg-gray-200 text-gray-600' : idx === 2 ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-500'">
                  {{ idx + 1 }}
                </div>
              </td>
              <td class="px-6 py-4 font-medium">{{ student.name }}</td>
              <td class="px-6 py-4 text-sm text-gray-500">{{ student.company }}</td>
              <td class="px-6 py-4 text-sm font-semibold text-indigo-600">{{ student.logbook_count }}</td>
              <td class="px-6 py-4 text-sm">{{ student.avg_grade || '-' }}</td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-indigo-600 rounded-full" :style="{ width: student.progress + '%' }"></div>
                  </div>
                  <span class="text-xs text-gray-500">{{ student.progress }}%</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <router-link to="/admin/students" class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition group">
        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
          <AcademicCapIcon class="w-5 h-5 text-indigo-600" />
        </div>
        <div>
          <p class="font-semibold text-gray-800">Tambah Siswa</p>
          <p class="text-xs text-gray-500">Registrasi siswa baru</p>
        </div>
      </router-link>
      <router-link to="/admin/companies" class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition group">
        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
          <BuildingOffice2Icon class="w-5 h-5 text-purple-600" />
        </div>
        <div>
          <p class="font-semibold text-gray-800">Tambah Perusahaan</p>
          <p class="text-xs text-gray-500">Tambah mitra baru</p>
        </div>
      </router-link>
      <router-link to="/admin/reports" class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition group">
        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
          <DocumentChartBarIcon class="w-5 h-5 text-green-600" />
        </div>
        <div>
          <p class="font-semibold text-gray-800">Export Laporan</p>
          <p class="text-xs text-gray-500">Download data PKL</p>
        </div>
      </router-link>
      <router-link to="/admin/monitoring" class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition group">
        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
          <ViewfinderCircleIcon class="w-5 h-5 text-orange-600" />
        </div>
        <div>
          <p class="font-semibold text-gray-800">Monitoring</p>
          <p class="text-xs text-gray-500">Pantau aktivitas PKL</p>
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
  AcademicCapIcon, UserGroupIcon, BuildingOffice2Icon, BookOpenIcon,
  DocumentChartBarIcon, ViewfinderCircleIcon, CheckCircleIcon,
  ClockIcon, XCircleIcon, HeartIcon, CalendarIcon
} from '@heroicons/vue/24/outline'
import * as echarts from 'echarts'

const authStore = useAuthStore()
const currentDate = ref('')
const currentTime = ref('')
const currentDay = ref('')
const stats = ref({})
const attendanceStats = ref({})
const recentActivities = ref([])
const topStudents = ref([])

const attendanceChart = ref(null)
const logbookChart = ref(null)
const companyChart = ref(null)

let attendanceChartInstance = null
let logbookChartInstance = null
let companyChartInstance = null

// Update date and time
const updateDateTime = () => {
  const now = new Date()
  currentDate.value = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })
  currentTime.value = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
  currentDay.value = now.toLocaleDateString('id-ID', { weekday: 'long' })
}

// Load dashboard data
const loadDashboardData = async () => {
  try {
    const res = await axios.get('/admin/dashboard/stats')
    stats.value = res.data
  } catch (error) {
    console.error('Failed to load dashboard stats:', error)
  }
}

// Load attendance stats
const loadAttendanceStats = async () => {
  try {
    const res = await axios.get('/admin/attendance/stats')
    attendanceStats.value = res.data
  } catch (error) {
    console.error('Failed to load attendance stats:', error)
  }
}

// Load recent activities
const loadRecentActivities = async () => {
  try {
    const res = await axios.get('/admin/recent-activities')
    recentActivities.value = res.data.map(activity => ({
      ...activity,
      icon: getActivityIcon(activity.type),
      bgColor: getActivityBgColor(activity.type),
      iconColor: getActivityIconColor(activity.type),
      statusClass: getStatusClass(activity.status)
    }))
  } catch (error) {
    console.error('Failed to load recent activities:', error)
    // Mock data for demo
    recentActivities.value = [
      { id: 1, description: 'Membuat logbook baru', user: 'Ahmad Fauzi', time: '5 menit lalu', type: 'logbook', status: 'pending', icon: BookOpenIcon, bgColor: 'bg-blue-100', iconColor: 'text-blue-600', statusClass: 'bg-yellow-100 text-yellow-800' },
      { id: 2, description: 'Check in PKL', user: 'Siti Nurhaliza', time: '1 jam lalu', type: 'attendance', status: 'success', icon: CheckCircleIcon, bgColor: 'bg-green-100', iconColor: 'text-green-600', statusClass: 'bg-green-100 text-green-800' },
      { id: 3, description: 'Mengajukan izin sakit', user: 'Rizki Ramadhan', time: '2 jam lalu', type: 'permission', status: 'pending', icon: HeartIcon, bgColor: 'bg-red-100', iconColor: 'text-red-600', statusClass: 'bg-yellow-100 text-yellow-800' },
      { id: 4, description: 'Terlambat absen', user: 'Dewi Anggraeni', time: '3 jam lalu', type: 'attendance', status: 'late', icon: ClockIcon, bgColor: 'bg-yellow-100', iconColor: 'text-yellow-600', statusClass: 'bg-yellow-100 text-yellow-800' }
    ]
  }
}

// Load top students
const loadTopStudents = async () => {
  try {
    const res = await axios.get('/admin/top-students')
    topStudents.value = res.data
  } catch (error) {
    console.error('Failed to load top students:', error)
    // Mock data for demo
    topStudents.value = [
      { id: 1, name: 'Ahmad Fauzi', company: 'PT. Teknologi Nusantara', logbook_count: 25, avg_grade: 88, progress: 83 },
      { id: 2, name: 'Siti Nurhaliza', company: 'PT. Teknologi Nusantara', logbook_count: 23, avg_grade: 85, progress: 77 },
      { id: 3, name: 'Rizki Ramadhan', company: 'CV. Karya Mandiri', logbook_count: 20, avg_grade: 82, progress: 67 },
      { id: 4, name: 'Dewi Anggraeni', company: 'CV. Karya Mandiri', logbook_count: 18, avg_grade: 80, progress: 60 }
    ]
  }
}

// Helper functions
const getActivityIcon = (type) => {
  const icons = { logbook: BookOpenIcon, attendance: CheckCircleIcon, permission: HeartIcon }
  return icons[type] || CalendarIcon
}

const getActivityBgColor = (type) => {
  const colors = { logbook: 'bg-blue-100', attendance: 'bg-green-100', permission: 'bg-red-100' }
  return colors[type] || 'bg-gray-100'
}

const getActivityIconColor = (type) => {
  const colors = { logbook: 'text-blue-600', attendance: 'text-green-600', permission: 'text-red-600' }
  return colors[type] || 'text-gray-600'
}

const getStatusClass = (status) => {
  const classes = { success: 'bg-green-100 text-green-800', pending: 'bg-yellow-100 text-yellow-800', late: 'bg-yellow-100 text-yellow-800' }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

// Initialize charts
const initAttendanceChart = () => {
  if (attendanceChart.value) {
    attendanceChartInstance = echarts.init(attendanceChart.value)
    attendanceChartInstance.setOption({
      tooltip: { trigger: 'axis', axisPointer: { type: 'shadow' } },
      grid: { left: '3%', right: '4%', bottom: '3%', containLabel: true },
      xAxis: { type: 'category', data: ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'], axisLabel: { fontSize: 12 } },
      yAxis: { type: 'value', name: 'Jumlah Siswa', nameLocation: 'middle', nameGap: 40 },
      series: [
        { name: 'Hadir', type: 'bar', data: [65, 70, 68, 72, 75, 60, 55], itemStyle: { borderRadius: [4, 4, 0, 0], color: '#10b981' } },
        { name: 'Terlambat', type: 'bar', data: [5, 3, 4, 2, 3, 1, 2], itemStyle: { borderRadius: [4, 4, 0, 0], color: '#f59e0b' } }
      ]
    })
  }
}

const initLogbookChart = () => {
  if (logbookChart.value) {
    logbookChartInstance = echarts.init(logbookChart.value)
    logbookChartInstance.setOption({
      tooltip: { trigger: 'axis' },
      xAxis: { type: 'category', data: ['Minggu 1', 'Minggu 2', 'Minggu 3', 'Minggu 4'] },
      yAxis: { type: 'value', name: 'Jumlah Logbook' },
      series: [{ type: 'line', data: [120, 150, 180, 210], smooth: true, lineStyle: { color: '#8b5cf6', width: 3 }, areaStyle: { opacity: 0.1, color: '#8b5cf6' }, symbol: 'circle', symbolSize: 8 }]
    })
  }
}

const initCompanyChart = () => {
  if (companyChart.value) {
    companyChartInstance = echarts.init(companyChart.value)
    companyChartInstance.setOption({
      tooltip: { trigger: 'item' },
      legend: { orient: 'vertical', left: 'left' },
      series: [{ type: 'pie', radius: '55%', data: [
        { name: 'PT. Teknologi Nusantara', value: 15, itemStyle: { color: '#4f46e5' } },
        { name: 'CV. Karya Mandiri', value: 12, itemStyle: { color: '#8b5cf6' } },
        { name: 'PT. Subang Jaya Abadi', value: 8, itemStyle: { color: '#ec4899' } }
      ], emphasis: { scale: true }, label: { show: true, formatter: '{b}: {d}%' } }]
    })
  }
}

// Handle window resize
const handleResize = () => {
  attendanceChartInstance?.resize()
  logbookChartInstance?.resize()
  companyChartInstance?.resize()
}

onMounted(() => {
  updateDateTime()
  setInterval(updateDateTime, 1000)
  loadDashboardData()
  loadAttendanceStats()
  loadRecentActivities()
  loadTopStudents()
  initAttendanceChart()
  initLogbookChart()
  initCompanyChart()
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
  attendanceChartInstance?.dispose()
  logbookChartInstance?.dispose()
  companyChartInstance?.dispose()
})
</script>