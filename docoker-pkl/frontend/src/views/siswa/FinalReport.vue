<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-2xl font-bold text-gray-800">Laporan PKL</h1>
      <p class="text-gray-500 mt-1">Upload laporan akhir PKL Anda</p>
    </div>

    <!-- Status Card -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
      <div class="flex items-center gap-4">
        <div class="w-16 h-16 rounded-full flex items-center justify-center" :class="statusClass">
          <component :is="statusIcon" class="w-8 h-8" />
        </div>
        <div>
          <h3 class="text-lg font-semibold">Status Laporan</h3>
          <p class="text-gray-600">{{ statusText }}</p>
          <p v-if="report?.submitted_at" class="text-sm text-gray-500 mt-1">Diajukan: {{ formatDate(report.submitted_at) }}</p>
        </div>
      </div>
    </div>

    <!-- Upload Form -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
      <form @submit.prevent="uploadReport" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Judul Laporan</label>
          <input v-model="form.title" type="text" class="w-full px-4 py-2 border rounded-lg" required>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Abstrak</label>
          <textarea v-model="form.abstract" rows="4" class="w-full px-4 py-2 border rounded-lg" required></textarea>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">File Laporan (PDF)</label>
          <div class="border-2 border-dashed rounded-xl p-6 text-center cursor-pointer hover:border-indigo-400 transition"
            @click="$refs.fileInput.click()"
            @dragover.prevent
            @drop.prevent="handleDrop"
          >
            <input type="file" ref="fileInput" class="hidden" accept=".pdf" @change="handleFileSelect">
            <DocumentIcon class="w-12 h-12 mx-auto text-gray-400 mb-2" />
            <p class="text-sm text-gray-500">Klik atau drag file PDF ke sini</p>
            <p class="text-xs text-gray-400 mt-1">Maksimal 10MB</p>
          </div>
          <div v-if="selectedFile" class="mt-3 p-3 bg-gray-50 rounded-lg flex items-center justify-between">
            <div class="flex items-center gap-2">
              <DocumentIcon class="w-5 h-5 text-indigo-600" />
              <span class="text-sm">{{ selectedFile.name }}</span>
            </div>
            <button type="button" @click="selectedFile = null" class="text-red-500">Hapus</button>
          </div>
        </div>
        
        <div v-if="existingFile" class="mt-3 p-3 bg-green-50 rounded-lg">
          <p class="text-sm text-green-700">Laporan sebelumnya: {{ existingFile }}</p>
          <a :href="existingFileUrl" target="_blank" class="text-indigo-600 text-sm hover:underline">Lihat Laporan</a>
        </div>
        
        <div class="flex justify-end">
          <button type="submit" :disabled="uploading" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50">
            {{ uploading ? 'Mengupload...' : (report?.status === 'draft' ? 'Simpan Draft' : 'Ajukan Laporan') }}
          </button>
        </div>
      </form>
    </div>

    <!-- Revisi Notes -->
    <div v-if="report?.revision_notes" class="bg-yellow-50 rounded-2xl p-6 border border-yellow-200">
      <div class="flex items-start gap-3">
        <ExclamationCircleIcon class="w-5 h-5 text-yellow-600 mt-0.5" />
        <div>
          <h4 class="font-semibold text-yellow-800">Catatan Revisi</h4>
          <p class="text-yellow-700 mt-1">{{ report.revision_notes }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'
import { CheckCircleIcon, ClockIcon, XCircleIcon, DocumentIcon, ExclamationCircleIcon } from '@heroicons/vue/24/outline'

const toast = useToast()
const uploading = ref(false)
const selectedFile = ref(null)
const fileInput = ref(null)
const report = ref(null)

const form = ref({
  title: '',
  abstract: ''
})

const existingFile = ref(null)
const existingFileUrl = ref(null)

const statusConfig = {
  draft: { text: 'Draft - Belum diajukan', icon: ClockIcon, class: 'bg-yellow-100 text-yellow-600' },
  submitted: { text: 'Diajukan - Menunggu review', icon: ClockIcon, class: 'bg-blue-100 text-blue-600' },
  reviewing: { text: 'Sedang direview', icon: ClockIcon, class: 'bg-purple-100 text-purple-600' },
  approved: { text: 'Disetujui', icon: CheckCircleIcon, class: 'bg-green-100 text-green-600' },
  rejected: { text: 'Ditolak', icon: XCircleIcon, class: 'bg-red-100 text-red-600' }
}

const statusText = computed(() => statusConfig[report.value?.status]?.text || 'Belum ada laporan')
const statusIcon = computed(() => statusConfig[report.value?.status]?.icon || ClockIcon)
const statusClass = computed(() => statusConfig[report.value?.status]?.class || 'bg-gray-100 text-gray-600')

const formatDate = (date) => new Date(date).toLocaleDateString('id-ID')

const handleFileSelect = (e) => {
  const file = e.target.files[0]
  if (file && file.type === 'application/pdf' && file.size <= 10 * 1024 * 1024) {
    selectedFile.value = file
  } else {
    toast.error('File harus PDF dan maksimal 10MB')
  }
}

const handleDrop = (e) => {
  const file = e.dataTransfer.files[0]
  if (file && file.type === 'application/pdf' && file.size <= 10 * 1024 * 1024) {
    selectedFile.value = file
  } else {
    toast.error('File harus PDF dan maksimal 10MB')
  }
}

const fetchReport = async () => {
  try {
    const res = await axios.get('/siswa/final-reports')
    if (res.data) {
      report.value = res.data
      form.value.title = res.data.title
      form.value.abstract = res.data.abstract
      existingFile.value = res.data.file_name
      existingFileUrl.value = res.data.file_url
    }
  } catch (error) {
    console.error('Failed to fetch report:', error)
  }
}

const uploadReport = async () => {
  uploading.value = true
  const formData = new FormData()
  formData.append('title', form.value.title)
  formData.append('abstract', form.value.abstract)
  if (selectedFile.value) {
    formData.append('file', selectedFile.value)
  }
  
  try {
    const response = await axios.post('/siswa/final-reports', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    toast.success('Laporan berhasil disimpan')
    selectedFile.value = null
    await fetchReport()
  } catch (error) {
    toast.error(error.response?.data?.message || 'Gagal menyimpan laporan')
  } finally {
    uploading.value = false
  }
}

onMounted(() => fetchReport())
</script>