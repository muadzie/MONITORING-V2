<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Review Logbook Siswa</h1>
        <p class="text-gray-500 mt-1">Review dan penilaian logbook harian siswa PKL</p>
      </div>
      <div class="flex gap-2">
        <select 
          v-model="filterStatus" 
          class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
        >
          <option value="all">Semua Status</option>
          <option value="pending">Menunggu Review</option>
          <option value="disetujui">Disetujui</option>
          <option value="ditolak">Ditolak</option>
        </select>
        <button 
          @click="refreshData" 
          class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          Refresh
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Total Logbook</p>
        <p class="text-2xl font-bold">{{ logbooks.length }}</p>
      </div>
      <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Menunggu Review</p>
        <p class="text-2xl font-bold">{{ pendingCount }}</p>
      </div>
      <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Disetujui</p>
        <p class="text-2xl font-bold">{{ approvedCount }}</p>
      </div>
      <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Ditolak</p>
        <p class="text-2xl font-bold">{{ rejectedCount }}</p>
      </div>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-xl shadow-sm p-4">
      <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
          <input 
            v-model="search" 
            type="text" 
            placeholder="Cari berdasarkan nama siswa..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          >
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
        <p class="mt-4 text-gray-500">Memuat data logbook...</p>
      </div>
    </div>

    <!-- Table -->
    <div v-else-if="filteredLogbooks.length > 0" class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Siswa</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kegiatan</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="(logbook, index) in filteredLogbooks" :key="logbook.id" class="hover:bg-gray-50 transition">
              <td class="px-6 py-4 text-sm text-gray-500">{{ index + 1 }}</td>
              <td class="px-6 py-4 font-medium text-gray-800">{{ logbook.student?.name || '-' }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ formatDate(logbook.date) }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">
                <div class="max-w-xs truncate">{{ logbook.activity || '-' }}</div>
              </td>
              <td class="px-6 py-4">
                <span :class="getStatusClass(logbook.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                  {{ getStatusText(logbook.status) }}
                </span>
              </td>
              <td class="px-6 py-4">
                <button 
                  @click="openReviewModal(logbook)" 
                  class="px-3 py-1 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm"
                >
                  Review
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!loading && filteredLogbooks.length === 0" class="bg-white rounded-xl p-12 text-center">
      <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
      </svg>
      <p class="text-gray-500">Belum ada data logbook</p>
    </div>

    <!-- Modal Review -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50" @click.self="closeModal">
      <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white p-5 border-b rounded-t-2xl">
          <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-xl font-bold text-gray-800">Review Logbook</h2>
                <p class="text-sm text-gray-500">Review kegiatan harian siswa</p>
              </div>
            </div>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <div class="p-6 space-y-4">
          <!-- Informasi Siswa -->
          <div class="bg-gray-50 rounded-lg p-4">
            <p class="text-sm font-medium text-gray-700">Informasi Siswa</p>
            <p class="text-lg font-semibold text-gray-800">{{ selectedLogbook?.student?.name }}</p>
            <p class="text-sm text-gray-500">Tanggal: {{ formatDate(selectedLogbook?.date) }}</p>
          </div>

          <!-- Detail Logbook -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Kegiatan</label>
            <div class="bg-gray-50 rounded-lg p-3">
              <p class="text-gray-800">{{ selectedLogbook?.activity || '-' }}</p>
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <div class="bg-gray-50 rounded-lg p-3">
              <p class="text-gray-800">{{ selectedLogbook?.description || '-' }}</p>
            </div>
          </div>

          <!-- Catatan Review -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Review</label>
            <textarea 
              v-model="reviewNotes" 
              rows="3" 
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              placeholder="Masukkan catatan review (opsional)..."
            ></textarea>
          </div>

          <!-- Tombol Aksi -->
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="closeModal" class="px-5 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium">
              Batal
            </button>
            <button 
              @click="approveLogbook" 
              class="px-6 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium"
              :disabled="submitting"
            >
              {{ submitting ? 'Menyimpan...' : 'Setujui' }}
            </button>
            <button 
              @click="rejectLogbook" 
              class="px-6 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium"
              :disabled="submitting"
            >
              {{ submitting ? 'Menyimpan...' : 'Tolak' }}
            </button>
          </div>
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

// State
const logbooks = ref([])
const loading = ref(false)
const submitting = ref(false)
const showModal = ref(false)
const selectedLogbook = ref(null)
const reviewNotes = ref('')
const search = ref('')
const filterStatus = ref('all')

// Computed
const pendingCount = computed(() => logbooks.value.filter(l => l.status === 'pending').length)
const approvedCount = computed(() => logbooks.value.filter(l => l.status === 'approved').length)
const rejectedCount = computed(() => logbooks.value.filter(l => l.status === 'rejected').length)

const filteredLogbooks = computed(() => {
  let result = logbooks.value
  
  if (filterStatus.value !== 'all') {
    result = result.filter(l => l.status === filterStatus.value)
  }
  
  if (search.value) {
    const searchLower = search.value.toLowerCase()
    result = result.filter(l => 
      l.student?.name?.toLowerCase().includes(searchLower)
    )
  }
  
  return result
})

// Helper functions
const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID')
}

const getStatusText = (status) => {
  const map = { pending: 'Menunggu', approved: 'Disetujui', rejected: 'Ditolak' }
  return map[status] || status
}

const getStatusClass = (status) => {
  const map = {
    pending: 'bg-yellow-100 text-yellow-800',
    approved: 'bg-green-100 text-green-800',
    rejected: 'bg-red-100 text-red-800'
  }
  return map[status] || 'bg-gray-100 text-gray-800'
}

// Fetch data
const fetchLogbooks = async () => {
  loading.value = true
  try {
    const res = await axios.get('/guru/logbooks/pending')
    logbooks.value = res.data.data || res.data || []
  } catch (error) {
    console.error('Fetch logbooks error:', error)
    toast.error('Gagal memuat data logbook')
  } finally {
    loading.value = false
  }
}

const refreshData = () => {
  fetchLogbooks()
}

// Review actions
const openReviewModal = (logbook) => {
  selectedLogbook.value = logbook
  reviewNotes.value = ''
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedLogbook.value = null
  reviewNotes.value = ''
}

const approveLogbook = async () => {
  if (!selectedLogbook.value) return
  
  submitting.value = true
  try {
    await axios.put(`/guru/logbooks/${selectedLogbook.value.id}/approve`, {
      notes: reviewNotes.value
    })
    toast.success('Logbook berhasil disetujui')
    closeModal()
    fetchLogbooks()
  } catch (error) {
    console.error('Approve error:', error)
    toast.error(error.response?.data?.message || 'Gagal menyetujui logbook')
  } finally {
    submitting.value = false
  }
}

const rejectLogbook = async () => {
  if (!selectedLogbook.value) return
  
  if (!reviewNotes.value) {
    toast.error('Mohon isi catatan penolakan')
    return
  }
  
  submitting.value = true
  try {
    await axios.put(`/guru/logbooks/${selectedLogbook.value.id}/reject`, {
      notes: reviewNotes.value
    })
    toast.success('Logbook ditolak')
    closeModal()
    fetchLogbooks()
  } catch (error) {
    console.error('Reject error:', error)
    toast.error(error.response?.data?.message || 'Gagal menolak logbook')
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  fetchLogbooks()
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
</style>