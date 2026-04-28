<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-8 text-white">
      <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
      <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24"></div>
      <div class="relative z-10">
        <div class="flex justify-between items-start">
          <div>
            <h1 class="text-3xl font-bold">Monitoring PKL</h1>
            <p class="mt-2 text-indigo-100">Pantau aktivitas dan progres siswa PKL secara real-time</p>
            <div class="flex gap-4 mt-4">
              <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                <p class="text-xs opacity-75">Periode PKL</p>
                <p class="font-semibold">Januari - Juni 2026</p>
              </div>
              <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                <p class="text-xs opacity-75">Update Terakhir</p>
                <p class="font-semibold">{{ currentDate }}</p>
              </div>
            </div>
          </div>
          <div class="flex gap-2">
            <button @click="refreshData" class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-xl hover:bg-white/30 transition flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              Refresh
            </button>
            <router-link to="/admin/reports/attendance" class="px-4 py-2 bg-white text-indigo-600 rounded-xl hover:shadow-lg transition flex items-center gap-2 font-medium">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              Export Laporan
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
      <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Total Siswa</p>
        <p class="text-3xl font-bold">{{ students.length }}</p>
      </div>
      <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Hadir Hari Ini</p>
        <p class="text-3xl font-bold">{{ attendanceStats.present || 0 }}</p>
        <p class="text-xs opacity-80 mt-1">Siswa</p>
      </div>
      <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Terlambat</p>
        <p class="text-3xl font-bold">{{ attendanceStats.late || 0 }}</p>
      </div>
      <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Alpha</p>
        <p class="text-3xl font-bold">{{ attendanceStats.absent || 0 }}</p>
      </div>
      <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Izin/Sakit</p>
        <p class="text-3xl font-bold">{{ (attendanceStats.permit || 0) + (attendanceStats.sick || 0) }}</p>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm p-4">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Cari Siswa</label>
          <div class="relative">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input v-model="searchQuery" type="text" placeholder="Nama atau NISN..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Perusahaan</label>
          <select v-model="filters.company_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            <option value="">Semua Perusahaan</option>
            <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Status Kehadiran</label>
          <select v-model="filters.status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            <option value="">Semua Status</option>
            <option value="present">Hadir</option>
            <option value="late">Terlambat</option>
            <option value="absent">Alpha</option>
            <option value="sick">Sakit</option>
            <option value="permit">Izin</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
          <select v-model="filters.class_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            <option value="">Semua Kelas</option>
            <option v-for="cls in classes" :key="cls.id" :value="cls.id">{{ cls.name }}</option>
          </select>
        </div>
      </div>
      <div class="mt-4 flex justify-end gap-2">
        <button @click="applyFilters" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Terapkan Filter</button>
        <button @click="resetFilters" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">Reset</button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
        <p class="mt-4 text-gray-500">Memuat data siswa...</p>
      </div>
    </div>

    <!-- Table -->
    <div v-else class="bg-white rounded-2xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NISN</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Siswa</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kelas</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Perusahaan</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status Hari Ini</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Logbook</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Progress</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nilai Rata-rata</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="(student, index) in filteredStudents" :key="student.id" class="hover:bg-gray-50 transition">
              <td class="px-6 py-4 text-sm text-gray-500">{{ index + 1 }}</td>
              <td class="px-6 py-4 text-sm font-mono">{{ student.nisn || '-' }}</td>
              <td class="px-6 py-4 font-medium text-gray-800">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-sm">
                    {{ student.name?.charAt(0) || 'S' }}
                  </div>
                  {{ student.name }}
                </div>
              </td>
              <td class="px-6 py-4 text-sm">{{ student.class?.name || '-' }}</td>
              <td class="px-6 py-4 text-sm">{{ student.company?.name || '-' }}</td>
              <td class="px-6 py-4">
                <span :class="getStatusClass(student.today_status)" class="px-2 py-1 rounded-full text-xs font-medium">
                  {{ getStatusText(student.today_status) }}
                </span>
              </td>
              <td class="px-6 py-4 text-sm font-semibold text-indigo-600">
                {{ student.logbook_count || 0 }}
                <span class="text-gray-400 text-xs">/30</span>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-indigo-600 rounded-full transition-all duration-500" :style="{ width: getProgress(student.logbook_count) + '%' }"></div>
                  </div>
                  <span class="text-xs text-gray-500">{{ getProgress(student.logbook_count) }}%</span>
                </div>
              </td>
              <td class="px-6 py-4 text-sm font-semibold text-purple-600">
                {{ student.avg_grade || 0 }}
              </td>
              <td class="px-6 py-4">
                <button @click="viewDetail(student)" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium flex items-center gap-1 transition">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                  Detail
                </button>
              </td>
            </tr>
            <tr v-if="filteredStudents.length === 0 && !loading">
              <td colspan="10" class="px-6 py-12 text-center text-gray-500">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <p>Tidak ada data siswa</p>
                <p class="text-sm mt-1">Coba ubah filter atau refresh halaman</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Detail Siswa -->
    <div v-if="selectedStudent" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50" @click.self="selectedStudent = null">
      <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto animate-fade-in-up">
        <div class="sticky top-0 bg-white p-5 border-b">
          <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-xl">
                {{ selectedStudent.name?.charAt(0) || 'S' }}
              </div>
              <div>
                <h3 class="text-xl font-bold text-gray-800">{{ selectedStudent.name }}</h3>
                <p class="text-sm text-gray-500">NISN: {{ selectedStudent.nisn || '-' }}</p>
              </div>
            </div>
            <button @click="selectedStudent = null" class="text-gray-400 hover:text-gray-600 transition">✕</button>
          </div>
        </div>
        <div class="p-6 space-y-6">
          <div class="grid grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded-xl p-3">
              <p class="text-xs text-gray-500">Kelas</p>
              <p class="font-semibold">{{ selectedStudent.class?.name || '-' }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-3">
              <p class="text-xs text-gray-500">Perusahaan</p>
              <p class="font-semibold">{{ selectedStudent.company?.name || '-' }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-3">
              <p class="text-xs text-gray-500">Status PKL</p>
              <p class="font-semibold text-green-600">Aktif</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-3">
              <p class="text-xs text-gray-500">Total Logbook</p>
              <p class="font-semibold text-indigo-600">{{ selectedStudent.logbook_count || 0 }} / 30</p>
            </div>
          </div>

          <div>
            <div class="flex justify-between text-sm mb-1">
              <span>Progress Logbook</span>
              <span>{{ getProgress(selectedStudent.logbook_count) }}%</span>
            </div>
            <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
              <div class="h-full bg-indigo-600 rounded-full" :style="{ width: getProgress(selectedStudent.logbook_count) + '%' }"></div>
            </div>
          </div>

          <div>
            <h4 class="font-semibold text-gray-800 mb-3">Logbook Terbaru</h4>
            <div class="space-y-3">
              <div v-for="log in selectedStudent.recent_logbooks" :key="log.id" class="border rounded-xl p-3">
                <div class="flex justify-between">
                  <div>
                    <p class="font-medium">{{ log.activity }}</p>
                    <p class="text-xs text-gray-500">{{ formatDate(log.date) }}</p>
                  </div>
                  <span :class="log.status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'" class="px-2 py-1 rounded-full text-xs">
                    {{ log.status === 'approved' ? 'Disetujui' : 'Menunggu' }}
                  </span>
                </div>
                <p class="text-sm text-gray-600 mt-2">{{ log.description }}</p>
              </div>
              <div v-if="!selectedStudent.recent_logbooks?.length" class="text-center text-gray-500 py-4">Belum ada logbook</div>
            </div>
          </div>
        </div>
        <div class="p-4 border-t bg-gray-50 flex justify-end">
          <router-link :to="`/admin/students/${selectedStudent.id}`" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Lihat Profil Lengkap</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()
const students = ref([])
const companies = ref([])
const classes = ref([])
const loading = ref(true)
const searchQuery = ref('')
const selectedStudent = ref(null)
const currentDate = ref(new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }))

const filters = ref({
  company_id: '',
  status: '',
  class_id: ''
})

const attendanceStats = ref({
  present: 0,
  late: 0,
  absent: 0,
  sick: 0,
  permit: 0
})

const getStatusText = (status) => {
  const map = { present: 'Hadir', late: 'Terlambat', absent: 'Alpha', sick: 'Sakit', permit: 'Izin' }
  return map[status] || status
}

const getStatusClass = (status) => {
  const map = {
    present: 'bg-green-100 text-green-800',
    late: 'bg-yellow-100 text-yellow-800',
    absent: 'bg-red-100 text-red-800',
    sick: 'bg-blue-100 text-blue-800',
    permit: 'bg-purple-100 text-purple-800'
  }
  return map[status] || 'bg-gray-100 text-gray-800'
}

const getProgress = (count) => {
  const target = 30
  const progress = (count / target) * 100
  return Math.min(Math.round(progress), 100)
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID')
}

const filteredStudents = computed(() => {
  let result = students.value
  
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(s => s.name?.toLowerCase().includes(query) || s.nisn?.toLowerCase().includes(query))
  }
  if (filters.value.company_id) result = result.filter(s => s.company_id === filters.value.company_id)
  if (filters.value.status) result = result.filter(s => s.today_status === filters.value.status)
  if (filters.value.class_id) result = result.filter(s => s.class_id === filters.value.class_id)
  return result
})

const fetchMonitoringData = async () => {
  loading.value = true
  try {
    const [studentsRes, companiesRes, classesRes] = await Promise.all([
      axios.get('/admin/monitoring'),
      axios.get('/companies'),
      axios.get('/admin/classes')
    ])
    
    students.value = studentsRes.data
    companies.value = companiesRes.data
    classes.value = classesRes.data
    
    const stats = { present: 0, late: 0, absent: 0, sick: 0, permit: 0 }
    students.value.forEach(s => { if (stats[s.today_status] !== undefined) stats[s.today_status]++ })
    attendanceStats.value = stats
  } catch (error) {
    toast.error('Gagal memuat data monitoring')
  } finally {
    loading.value = false
  }
}

const viewDetail = async (student) => {
  try {
    const res = await axios.get(`/admin/students/${student.id}`)
    selectedStudent.value = res.data
  } catch (error) {
    toast.error('Gagal memuat detail siswa')
  }
}

const applyFilters = () => { /* filters sudah reactive */ }
const refreshData = () => fetchMonitoringData()
const resetFilters = () => {
  searchQuery.value = ''
  filters.value = { company_id: '', status: '', class_id: '' }
}

onMounted(() => fetchMonitoringData())
</script>

<style scoped>
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fadeInUp 0.3s ease-out; }
</style>