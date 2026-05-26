<template>
  <div class="space-y-6">
    <!-- Header with Stats -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">📊 Monitoring Absensi PKL</h1>
        <p class="text-gray-500 mt-1">Pantau dan analisis kehadiran siswa PKL secara real-time</p>
      </div>
      <div class="flex gap-3">
        <button 
          @click="refreshData" 
          class="bg-indigo-50 text-indigo-600 px-4 py-2 rounded-xl hover:bg-indigo-100 transition flex items-center gap-2"
          :disabled="loading"
        >
          <svg class="w-5 h-5" :class="{'animate-spin': loading}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          Refresh
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
      <div class="bg-gradient-to-br from-green-500 to-green-700 rounded-2xl p-5 text-white shadow-lg hover:shadow-xl transition-all duration-300 cursor-pointer" @click="filterByStatus('present')">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm opacity-90">Hadir</p>
            <p class="text-3xl font-bold mt-1">{{ stats.present }}</p>
          </div>
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
        </div>
        <div class="mt-2 text-xs opacity-75">
          {{ stats.presentPercentage }}% dari total
        </div>
        <div class="mt-1 text-xs opacity-60">
          Hari ini: {{ stats.todayPresent }}
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-yellow-500 to-yellow-700 rounded-2xl p-5 text-white shadow-lg hover:shadow-xl transition-all duration-300 cursor-pointer" @click="filterByStatus('late')">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm opacity-90">Terlambat</p>
            <p class="text-3xl font-bold mt-1">{{ stats.late }}</p>
          </div>
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
        </div>
        <div class="mt-2 text-xs opacity-75">
          {{ stats.latePercentage }}% dari total
        </div>
        <div class="mt-1 text-xs opacity-60">
          Hari ini: {{ stats.todayLate }}
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-red-500 to-red-700 rounded-2xl p-5 text-white shadow-lg hover:shadow-xl transition-all duration-300 cursor-pointer" @click="filterByStatus('absent')">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm opacity-90">Alpha</p>
            <p class="text-3xl font-bold mt-1">{{ stats.absent }}</p>
          </div>
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </div>
        </div>
        <div class="mt-2 text-xs opacity-75">
          {{ stats.absentPercentage }}% dari total
        </div>
        <div class="mt-1 text-xs opacity-60">
          Hari ini: {{ stats.todayAbsent }}
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl p-5 text-white shadow-lg hover:shadow-xl transition-all duration-300 cursor-pointer" @click="filterByStatus('sick')">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm opacity-90">Sakit</p>
            <p class="text-3xl font-bold mt-1">{{ stats.sick }}</p>
          </div>
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
          </div>
        </div>
        <div class="mt-2 text-xs opacity-75">
          {{ stats.sickPercentage }}% dari total
        </div>
        <div class="mt-1 text-xs opacity-60">
          Hari ini: {{ stats.todaySick }}
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-2xl p-5 text-white shadow-lg hover:shadow-xl transition-all duration-300 cursor-pointer" @click="filterByStatus('permit')">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm opacity-90">Izin</p>
            <p class="text-3xl font-bold mt-1">{{ stats.permit }}</p>
          </div>
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
        </div>
        <div class="mt-2 text-xs opacity-75">
          {{ stats.permitPercentage }}% dari total
        </div>
        <div class="mt-1 text-xs opacity-60">
          Hari ini: {{ stats.todayPermit }}
        </div>
      </div>
    </div>

    <!-- ============ FILTER SECTION - INI YANG HILANG ============ -->
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-800">🔍 Filter Data Absensi</h3>
        <button @click="resetFilters" class="text-sm text-gray-500 hover:text-indigo-600 transition flex items-center gap-1">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          Reset Filter
        </button>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">📅 Tanggal Mulai</label>
          <input 
            v-model="filters.start_date" 
            type="date" 
            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">📅 Tanggal Akhir</label>
          <input 
            v-model="filters.end_date" 
            type="date" 
            class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">🏢 Perusahaan</label>
          <select v-model="filters.company_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 bg-white">
            <option value="">Semua Perusahaan</option>
            <option v-for="c in companies" :key="c.id" :value="c.id">🏢 {{ c.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">👨‍🎓 Status</label>
          <select v-model="filters.status" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 bg-white">
            <option value="">Semua Status</option>
            <option value="present">✅ Hadir</option>
            <option value="late">⏰ Terlambat</option>
            <option value="absent">❌ Alpha</option>
            <option value="sick">🤒 Sakit</option>
            <option value="permit">📝 Izin</option>
          </select>
        </div>
      </div>
      
      <div class="mt-4 flex justify-end gap-3">
        <button 
          @click="fetchData" 
          class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-2.5 rounded-xl hover:shadow-lg transition-all duration-300 flex items-center gap-2"
          :disabled="loading"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
          Terapkan Filter
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white rounded-2xl shadow-sm p-12 text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
      <p class="mt-4 text-gray-500">Memuat data absensi...</p>
    </div>

    <!-- Table -->
    <div v-else-if="filteredData.length > 0" class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
      <div class="overflow-x-auto">
        <table class="min-w-[1200px] w-full">
          <thead>
            <tr class="bg-gradient-to-r from-indigo-600 to-purple-600">
              <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">No</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Tanggal</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">NISN</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Nama Siswa</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Kelas</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Perusahaan</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Check In</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Check Out</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Status</th>
              <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase">Foto</th>
              <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase">Aksi</th>
             </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="(item, index) in paginatedData" :key="item.id" class="hover:bg-gray-50 transition duration-200">
              <td class="px-6 py-4 text-sm text-gray-500">{{ index + 1 + (currentPage - 1) * itemsPerPage }}</td>
              <td class="px-6 py-4 text-sm text-gray-600 whitespace-nowrap">{{ formatDate(item.date) }}</td>
              <td class="px-6 py-4 text-sm font-mono text-gray-600">{{ item.user?.nisn || '-' }}</td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-sm">
                    {{ item.user?.name?.charAt(0) || 'S' }}
                  </div>
                  <span class="font-medium text-gray-800">{{ item.user?.name }}</span>
                </div>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ item.user?.kelas || '-' }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ item.company?.name || '-' }}</td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1 text-sm">
                  <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                  </svg>
                  {{ item.check_in || '-' }}
                </span>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1 text-sm">
                  <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                  </svg>
                  {{ item.check_out || '-' }}
                </span>
              </td>
              <td class="px-6 py-4">
                <span :class="getStatusClass(item.status)" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium">
                  <span class="w-1.5 h-1.5 rounded-full" :class="getStatusDotClass(item.status)"></span>
                  {{ getStatusText(item.status) }}
                </span>
              </td>
              <td class="px-6 py-4 text-center">
                <button v-if="item.photo" @click="previewPhoto(item.photo)" class="text-emerald-600 hover:text-emerald-800 text-xs underline" title="Lihat Foto">
                  Lihat
                </button>
                <span v-else class="text-gray-400 text-xs">-</span>
              </td>
              <td class="px-6 py-4 text-center">
                <button 
                  @click="viewDetail(item)" 
                  class="text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 p-2 rounded-lg transition"
                  title="Lihat Detail"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="px-6 py-4 border-t border-gray-100 flex justify-between items-center">
        <div class="text-sm text-gray-500">
          Menampilkan {{ ((currentPage - 1) * itemsPerPage) + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredData.length) }} dari {{ filteredData.length }} data
        </div>
        <div class="flex gap-2">
          <button 
            @click="prevPage" 
            :disabled="currentPage === 1"
            class="px-3 py-1 border rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition"
          >
            Previous
          </button>
          <button 
            @click="nextPage" 
            :disabled="currentPage === totalPages"
            class="px-3 py-1 border rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!loading && filteredData.length === 0" class="bg-white rounded-2xl shadow-sm p-12 text-center">
      <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
      </svg>
      <p class="text-gray-500 text-lg">Belum ada data absensi</p>
      <p class="text-sm text-gray-400 mt-1">Belum ada aktivitas absensi dalam periode yang dipilih</p>
    </div>

    <!-- Detail Modal -->
    <div v-if="showDetailModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50" @click.self="showDetailModal = false">
      <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto animate-fade-in-up shadow-2xl">
        <div class="sticky top-0 bg-white p-5 border-b rounded-t-2xl">
          <div class="flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-800">Detail Absensi</h3>
            <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
        <div class="p-6 space-y-4" v-if="selectedItem">
          <div class="flex items-center gap-4 pb-4 border-b">
            <div class="w-16 h-16 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center text-2xl font-bold text-indigo-600">
              {{ selectedItem.user?.name?.charAt(0) || 'S' }}
            </div>
            <div>
              <p class="font-bold text-gray-800">{{ selectedItem.user?.name }}</p>
              <p class="text-sm text-gray-500">NISN: {{ selectedItem.user?.nisn || '-' }}</p>
              <p class="text-sm text-gray-500">Kelas: {{ selectedItem.user?.kelas || '-' }}</p>
            </div>
          </div>
          
          <div class="grid grid-cols-2 gap-3">
            <div class="bg-gray-50 rounded-xl p-3">
              <p class="text-xs text-gray-500">Tanggal</p>
              <p class="font-medium">{{ formatDate(selectedItem.date) }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-3">
              <p class="text-xs text-gray-500">Status</p>
              <span :class="getStatusClass(selectedItem.status)" class="inline-flex px-2 py-1 rounded-full text-xs">
                {{ getStatusText(selectedItem.status) }}
              </span>
            </div>
            <div class="bg-gray-50 rounded-xl p-3">
              <p class="text-xs text-gray-500">Check In</p>
              <p class="font-medium">{{ selectedItem.check_in || '-' }}</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-3">
              <p class="text-xs text-gray-500">Check Out</p>
              <p class="font-medium">{{ selectedItem.check_out || '-' }}</p>
            </div>
          </div>
          
          <div class="bg-gray-50 rounded-xl p-3" v-if="selectedItem.notes">
            <p class="text-xs text-gray-500">Keterangan</p>
            <p class="text-sm mt-1">{{ selectedItem.notes }}</p>
          </div>
          
          <div class="bg-gray-50 rounded-xl p-3" v-if="selectedItem.photo">
            <p class="text-xs text-gray-500 mb-2">Foto Absensi</p>
            <img :src="getPhotoUrl(selectedItem.photo)" class="w-full rounded-lg object-cover max-h-48 cursor-pointer" @click="previewPhoto(selectedItem.photo)" />
          </div>
          
          <div class="bg-gray-50 rounded-xl p-3">
            <p class="text-xs text-gray-500">Perusahaan</p>
            <p class="text-sm mt-1">{{ selectedItem.company?.name || '-' }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ selectedItem.company?.address || '-' }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Photo Preview Modal -->
    <div v-if="showPhotoPreview" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50" @click.self="showPhotoPreview = false">
      <div class="max-w-lg w-full mx-4">
        <img :src="previewPhotoUrl" class="w-full rounded-xl shadow-2xl" />
        <button @click="showPhotoPreview = false" class="mt-4 w-full bg-white/20 text-white py-2 rounded-xl hover:bg-white/30 transition">Tutup</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from '../../../plugins/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()
const loading = ref(false)
const data = ref([])
const companies = ref([])
const showDetailModal = ref(false)
const selectedItem = ref(null)
const showPhotoPreview = ref(false)
const previewPhotoUrl = ref('')

const previewPhoto = (photo) => {
  previewPhotoUrl.value = getPhotoUrl(photo)
  showPhotoPreview.value = true
}

const getPhotoUrl = (photo) => {
  if (!photo) return ''
  if (photo.startsWith('http')) return photo
  if (photo.startsWith('/storage')) return photo
  return `/storage/${photo}`
}
const currentPage = ref(1)
const itemsPerPage = ref(15)

const filters = ref({
  start_date: new Date().toISOString().split('T')[0],
  end_date: new Date().toISOString().split('T')[0],
  company_id: '',
  status: ''
})

const stats = ref({
  present: 0,
  late: 0,
  absent: 0,
  sick: 0,
  permit: 0,
  total: 0,
  presentPercentage: 0,
  latePercentage: 0,
  absentPercentage: 0,
  sickPercentage: 0,
  permitPercentage: 0,
  todayPresent: 0,
  todayLate: 0,
  todayAbsent: 0,
  todaySick: 0,
  todayPermit: 0
})

const filterByStatus = (status) => {
  if (filters.value.status === status) {
    filters.value.status = ''
  } else {
    filters.value.status = status
  }
  fetchData()
}

const calculateStats = () => {
  const today = new Date().toISOString().split('T')[0]
  const filteredDataForStats = data.value
  
  const present = filteredDataForStats.filter(item => item.status === 'present').length
  const late = filteredDataForStats.filter(item => item.status === 'late').length
  const absent = filteredDataForStats.filter(item => item.status === 'absent').length
  const sick = filteredDataForStats.filter(item => item.status === 'sick').length
  const permit = filteredDataForStats.filter(item => item.status === 'permit').length
  const total = filteredDataForStats.length
  
  const todayData = filteredDataForStats.filter(item => item.date === today)
  
  stats.value = {
    present,
    late,
    absent,
    sick,
    permit,
    total,
    presentPercentage: total > 0 ? ((present / total) * 100).toFixed(1) : 0,
    latePercentage: total > 0 ? ((late / total) * 100).toFixed(1) : 0,
    absentPercentage: total > 0 ? ((absent / total) * 100).toFixed(1) : 0,
    sickPercentage: total > 0 ? ((sick / total) * 100).toFixed(1) : 0,
    permitPercentage: total > 0 ? ((permit / total) * 100).toFixed(1) : 0,
    todayPresent: todayData.filter(item => item.status === 'present').length,
    todayLate: todayData.filter(item => item.status === 'late').length,
    todayAbsent: todayData.filter(item => item.status === 'absent').length,
    todaySick: todayData.filter(item => item.status === 'sick').length,
    todayPermit: todayData.filter(item => item.status === 'permit').length
  }
}

const getStatusText = (s) => {
  const statusMap = {
    present: '✅ Hadir',
    late: '⏰ Terlambat',
    absent: '❌ Alpha',
    sick: '🤒 Sakit',
    permit: '📝 Izin'
  }
  return statusMap[s] || s
}

const getStatusClass = (s) => {
  const classMap = {
    present: 'bg-green-100 text-green-800',
    late: 'bg-yellow-100 text-yellow-800',
    absent: 'bg-red-100 text-red-800',
    sick: 'bg-blue-100 text-blue-800',
    permit: 'bg-purple-100 text-purple-800'
  }
  return classMap[s] || 'bg-gray-100 text-gray-800'
}

const getStatusDotClass = (s) => {
  const dotMap = {
    present: 'bg-green-500',
    late: 'bg-yellow-500',
    absent: 'bg-red-500',
    sick: 'bg-blue-500',
    permit: 'bg-purple-500'
  }
  return dotMap[s] || 'bg-gray-500'
}

const formatDate = (date) => {
  if (!date) return '-'
  const d = new Date(date)
  return d.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

const fetchData = async () => {
  loading.value = true
  try {
    const params = {
      start_date: filters.value.start_date,
      end_date: filters.value.end_date
    }
    
    const [attendanceRes, companiesRes] = await Promise.all([
      axios.get('/admin/monitoring/attendance', { params }),
      axios.get('/companies')
    ])
    
    let attendanceData = attendanceRes.data.data || attendanceRes.data || []
    if (attendanceRes.data && attendanceRes.data.data) {
      attendanceData = attendanceRes.data.data
    }
    
    data.value = attendanceData
    companies.value = companiesRes.data.data || companiesRes.data || []
    
    calculateStats()
    currentPage.value = 1
  } catch (error) {
    console.error('Fetch error:', error)
    toast.error('Gagal memuat data absensi')
  } finally {
    loading.value = false
  }
}

const refreshData = () => {
  fetchData()
}

const resetFilters = () => {
  filters.value = {
    start_date: new Date().toISOString().split('T')[0],
    end_date: new Date().toISOString().split('T')[0],
    company_id: '',
    status: ''
  }
  fetchData()
}

const exportData = async () => {
  try {
    const params = {
      start_date: filters.value.start_date,
      end_date: filters.value.end_date,
      company_id: filters.value.company_id,
      status: filters.value.status
    }
    
    const res = await axios.get('/admin/monitoring/attendance/export', { 
      params, 
      responseType: 'blob' 
    })
    
    const url = window.URL.createObjectURL(new Blob([res.data]))
    const link = document.createElement('a')
    link.href = url
    const filename = `absensi_${filters.value.start_date}_to_${filters.value.end_date}.xlsx`
    link.download = filename
    link.click()
    window.URL.revokeObjectURL(url)
    
    toast.success('Export data berhasil')
  } catch (error) {
    console.error('Export error:', error)
    toast.error('Gagal mengexport data')
  }
}

const viewDetail = (item) => {
  selectedItem.value = item
  showDetailModal.value = true
}

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
  }
}

const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++
  }
}

const filteredData = computed(() => {
  let result = [...data.value]
  
  if (filters.value.status) {
    result = result.filter(item => item.status === filters.value.status)
  }
  
  if (filters.value.company_id) {
    result = result.filter(item => item.company_id === parseInt(filters.value.company_id))
  }
  
  return result
})

const totalPages = computed(() => Math.ceil(filteredData.value.length / itemsPerPage.value))
const paginatedData = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  const end = start + itemsPerPage.value
  return filteredData.value.slice(start, end)
})

watch([() => filters.value.status, () => filters.value.company_id], () => {
  currentPage.value = 1
})

onMounted(() => {
  fetchData()
})
</script>

<style scoped>
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fadeInUp 0.3s ease-out;
}

.cursor-pointer {
  cursor: pointer;
}
</style>