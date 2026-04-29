<template>
  <div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Review Laporan PKL Siswa</h1>
        <p class="text-gray-500 mt-1">Review dan download laporan PKL siswa bimbingan Anda</p>
      </div>
      <button @click="fetchReports" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
        Refresh
      </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl p-4 shadow-sm">
        <p class="text-gray-500 text-sm">Total Laporan</p>
        <p class="text-2xl font-bold text-indigo-600">{{ reports.length }}</p>
      </div>
      <div class="bg-white rounded-xl p-4 shadow-sm">
        <p class="text-gray-500 text-sm">Menunggu Review</p>
        <p class="text-2xl font-bold text-yellow-600">{{ pendingCount }}</p>
      </div>
      <div class="bg-white rounded-xl p-4 shadow-sm">
        <p class="text-gray-500 text-sm">Sudah Direview</p>
        <p class="text-2xl font-bold text-green-600">{{ reviewedCount }}</p>
      </div>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-xl shadow-sm p-4">
      <input v-model="search" type="text" placeholder="Cari nama siswa..." class="w-full px-4 py-2 border rounded-lg">
    </div>

    <!-- Loading -->
    <div v-if="loading" class="text-center py-12">Memuat data...</div>

    <!-- Table -->
    <div v-else-if="filteredReports.length > 0" class="bg-white rounded-xl shadow-sm overflow-hidden">
      <table class="min-w-full">
        <thead class="bg-gray-50">
          <tr><th class="px-6 py-3 text-left">No</th><th>Siswa</th><th>Nama File</th><th>Tanggal Upload</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          <tr v-for="(report, index) in filteredReports" :key="report.id" class="border-b hover:bg-gray-50">
            <td class="px-6 py-4">{{ index + 1 }}</td>
            <td class="px-6 py-4 font-medium">{{ report.student?.name || '-' }}</td>
            <td class="px-6 py-4">{{ report.file_name }}</td>
            <td class="px-6 py-4">{{ formatDate(report.created_at) }}</td>
            <td class="px-6 py-4">
              <span :class="getStatusClass(report.status)" class="px-2 py-1 rounded-full text-xs">
                {{ getStatusText(report.status) }}
              </span>
            </td>
            <td class="px-6 py-4">
              <div class="flex gap-2">
                <a :href="report.file_url" target="_blank" class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">Download</a>
                <button @click="openReviewModal(report)" class="px-3 py-1 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">Review</button>
              </div>
            </td>
          </tr>
        </tbody>
       </table>
    </div>
    <div v-else class="text-center py-12 text-gray-500">Belum ada laporan dari siswa</div>

    <!-- Modal Review -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" @click.self="closeModal">
      <div class="bg-white rounded-2xl w-full max-w-lg p-6">
        <h2 class="text-xl font-bold mb-4">Review Laporan</h2>
        <div class="mb-4">
          <p><strong>Siswa:</strong> {{ selectedReport?.student?.name }}</p>
          <p><strong>File:</strong> <a :href="selectedReport?.file_url" target="_blank" class="text-blue-600">Lihat Laporan</a></p>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Catatan Review</label>
          <textarea v-model="reviewNotes" rows="3" class="w-full px-3 py-2 border rounded-lg" placeholder="Masukkan catatan..."></textarea>
        </div>
        <div class="flex justify-end gap-3 mt-6">
          <button @click="closeModal" class="px-4 py-2 border rounded-lg">Batal</button>
          <button @click="approveReport" class="px-4 py-2 bg-green-600 text-white rounded-lg">Setujui</button>
          <button @click="rejectReport" class="px-4 py-2 bg-red-600 text-white rounded-lg">Tolak</button>
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
const reports = ref([])
const loading = ref(false)
const showModal = ref(false)
const selectedReport = ref(null)
const reviewNotes = ref('')
const search = ref('')
const submitting = ref(false) // Tambah state untuk loading

const pendingCount = computed(() => reports.value.filter(r => r.status === 'pending').length)
const reviewedCount = computed(() => reports.value.filter(r => r.status !== 'pending').length)
const filteredReports = computed(() => {
  let result = reports.value
  if (search.value) result = result.filter(r => r.student?.name?.toLowerCase().includes(search.value.toLowerCase()))
  return result
})

const formatDate = (date) => new Date(date).toLocaleDateString('id-ID')
const getStatusText = (status) => ({ pending: 'Menunggu', approved: 'Disetujui', rejected: 'Ditolak' }[status] || status)
const getStatusClass = (status) => ({
  pending: 'bg-yellow-100 text-yellow-800',
  approved: 'bg-green-100 text-green-800',
  rejected: 'bg-red-100 text-red-800'
}[status])

const fetchReports = async () => {
  loading.value = true
  try {
    console.log('🔵 Fetching reports...')
    const res = await axios.get('/guru/reports/pending')
    console.log('🟢 Response:', res.data)
    reports.value = res.data.data || res.data || []
  } catch (error) {
    console.error('🔴 Error:', error)
    toast.error('Gagal memuat data laporan')
  } finally {
    loading.value = false
  }
}

const openReviewModal = (report) => {
  selectedReport.value = report
  reviewNotes.value = ''
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedReport.value = null
  reviewNotes.value = ''
}

const approveReport = async () => {
  if (!selectedReport.value) return
  
  submitting.value = true
  try {
    console.log('🔵 Approving report:', selectedReport.value.id)
    const response = await axios.put(`/guru/reports/${selectedReport.value.id}/approve`, {
      notes: reviewNotes.value
    })
    console.log('🟢 Approve response:', response.data)
    
    if (response.data.success) {
      toast.success('Laporan berhasil disetujui')
      closeModal()
      await fetchReports()
    } else {
      toast.error(response.data.message || 'Gagal menyetujui')
    }
  } catch (error) {
    console.error('🔴 Approve error:', error)
    console.error('Response data:', error.response?.data)
    console.error('Status:', error.response?.status)
    
    if (error.response?.status === 403) {
      toast.error('Akses ditolak. Anda tidak memiliki izin')
    } else if (error.response?.status === 404) {
      toast.error('Endpoint tidak ditemukan. Periksa kembali route API')
    } else {
      toast.error(error.response?.data?.message || 'Gagal menyetujui laporan')
    }
  } finally {
    submitting.value = false
  }
}

const rejectReport = async () => {
  if (!selectedReport.value) return
  if (!reviewNotes.value) {
    toast.error('Isi catatan penolakan terlebih dahulu')
    return
  }
  
  submitting.value = true
  try {
    console.log('🔵 Rejecting report:', selectedReport.value.id)
    const response = await axios.put(`/guru/reports/${selectedReport.value.id}/reject`, {
      notes: reviewNotes.value
    })
    console.log('🟢 Reject response:', response.data)
    
    if (response.data.success) {
      toast.success('Laporan berhasil ditolak')
      closeModal()
      await fetchReports()
    } else {
      toast.error(response.data.message || 'Gagal menolak')
    }
  } catch (error) {
    console.error('🔴 Reject error:', error)
    console.error('Response data:', error.response?.data)
    console.error('Status:', error.response?.status)
    
    if (error.response?.status === 403) {
      toast.error('Akses ditolak. Anda tidak memiliki izin')
    } else if (error.response?.status === 404) {
      toast.error('Endpoint tidak ditemukan. Periksa kembali route API')
    } else {
      toast.error(error.response?.data?.message || 'Gagal menolak laporan')
    }
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  console.log('🟡 ReportReview component mounted')
  fetchReports()
})
</script>