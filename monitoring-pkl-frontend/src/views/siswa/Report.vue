<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-gray-800">Upload Laporan PKL</h1>
      <p class="text-gray-500 mt-1">Upload laporan akhir PKL Anda dalam format PDF/DOCX</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl p-4 shadow-sm">
        <p class="text-gray-500 text-sm">Status Laporan</p>
        <p class="text-2xl font-bold" :class="reportStatus.color">{{ reportStatus.text }}</p>
      </div>
      <div class="bg-white rounded-xl p-4 shadow-sm">
        <p class="text-gray-500 text-sm">Terakhir Upload</p>
        <p class="text-2xl font-bold">{{ lastUpload || '-' }}</p>
      </div>
      <div class="bg-white rounded-xl p-4 shadow-sm">
        <p class="text-gray-500 text-sm">Batas Upload</p>
        <p class="text-2xl font-bold text-red-600">31 Maret 2026</p>
      </div>
    </div>

    <!-- Upload Section -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
      <div class="p-6 border-b">
        <h3 class="text-lg font-semibold">Upload Laporan PKL</h3>
        <p class="text-sm text-gray-500 mt-1">Upload file laporan akhir PKL Anda (PDF, DOC, DOCX, maks 10MB)</p>
      </div>
      
      <div class="p-6">
        <!-- Upload Area -->
        <div 
          class="border-2 border-dashed rounded-xl p-8 text-center cursor-pointer transition-all duration-300"
          :class="isDragging ? 'border-indigo-600 bg-indigo-50' : 'border-gray-300 hover:border-indigo-400'"
          @dragover.prevent="isDragging = true"
          @dragleave.prevent="isDragging = false"
          @drop.prevent="handleDrop"
          @click="$refs.fileInput.click()"
        >
          <input 
            type="file" 
            ref="fileInput" 
            class="hidden" 
            accept=".pdf,.doc,.docx"
            @change="handleFileSelect"
          />
          
          <div class="flex flex-col items-center gap-3">
            <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center">
              <CloudArrowUpIcon class="w-8 h-8 text-indigo-600" />
            </div>
            <div>
              <p class="font-medium text-gray-800">Klik atau drag file ke sini</p>
              <p class="text-sm text-gray-500 mt-1">PDF, DOC, DOCX (Max 10MB)</p>
            </div>
          </div>
        </div>

        <!-- Selected File Preview -->
        <div v-if="selectedFile" class="mt-4 p-4 bg-gray-50 rounded-xl flex items-center justify-between">
          <div class="flex items-center gap-3">
            <DocumentIcon class="w-8 h-8 text-indigo-600" />
            <div>
              <p class="font-medium">{{ selectedFile.name }}</p>
              <p class="text-xs text-gray-500">{{ formatFileSize(selectedFile.size) }}</p>
            </div>
          </div>
          <button @click="selectedFile = null" class="text-red-500 hover:text-red-700">
            <XMarkIcon class="w-5 h-5" />
          </button>
        </div>

        <!-- Existing Report -->
        <div v-if="existingReport" class="mt-4 p-4 bg-green-50 rounded-xl">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <DocumentIcon class="w-8 h-8 text-green-600" />
              <div>
                <p class="font-medium">Laporan sebelumnya</p>
                <p class="text-xs text-gray-500">Upload: {{ formatDate(existingReport.created_at) }}</p>
                <p class="text-xs" :class="getStatusClass(existingReport.status)">
                  Status: {{ getStatusText(existingReport.status) }}
                </p>
              </div>
            </div>
            <a :href="existingReport.file_url" target="_blank" class="text-indigo-600 hover:text-indigo-800 text-sm">Lihat Laporan</a>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-6 flex justify-end gap-3">
          <button 
            v-if="selectedFile"
            @click="uploadReport" 
            :disabled="uploading"
            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50"
          >
            {{ uploading ? 'Mengupload...' : 'Upload Laporan' }}
          </button>
          <button 
            v-if="existingReport && !selectedFile"
            @click="deleteReport" 
            :disabled="deleting"
            class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
          >
            {{ deleting ? 'Menghapus...' : 'Hapus Laporan' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Guidelines -->
    <div class="bg-blue-50 rounded-2xl p-6">
      <div class="flex items-start gap-3">
        <InformationCircleIcon class="w-6 h-6 text-blue-600 mt-0.5" />
        <div>
          <h4 class="font-semibold text-blue-800">Panduan Upload Laporan</h4>
          <ul class="mt-2 space-y-1 text-sm text-blue-700">
            <li>• File harus dalam format PDF, DOC, atau DOCX</li>
            <li>• Ukuran file maksimal 10MB</li>
            <li>• Laporan akan direview oleh guru pembimbing</li>
            <li>• Batas akhir upload laporan: 31 Maret 2026</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'
import { CloudArrowUpIcon, DocumentIcon, XMarkIcon, InformationCircleIcon } from '@heroicons/vue/24/outline'

const toast = useToast()
const fileInput = ref(null)
const selectedFile = ref(null)
const uploading = ref(false)
const deleting = ref(false)
const isDragging = ref(false)
const existingReport = ref(null)
const lastUpload = ref('')
const reportStatus = ref({ text: 'Belum Upload', color: 'text-red-600' })

const formatFileSize = (bytes) => {
  if (bytes < 1024) return bytes + ' B'
  if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(2) + ' KB'
  return (bytes / (1024 * 1024)).toFixed(2) + ' MB'
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })
}

const getStatusText = (status) => {
  const map = { pending: 'Menunggu Review', approved: 'Disetujui', rejected: 'Ditolak' }
  return map[status] || status || 'Menunggu'
}

const getStatusClass = (status) => {
  const map = {
    pending: 'text-yellow-600',
    approved: 'text-green-600',
    rejected: 'text-red-600'
  }
  return map[status] || 'text-gray-600'
}

const handleFileSelect = (e) => {
  const file = e.target.files[0]
  if (file) validateAndSetFile(file)
}

const handleDrop = (e) => {
  isDragging.value = false
  const file = e.dataTransfer.files[0]
  if (file) validateAndSetFile(file)
}

const validateAndSetFile = (file) => {
  // Validasi ekstensi file (lebih fleksibel)
  const allowedExtensions = ['pdf', 'doc', 'docx']
  const fileExtension = file.name.split('.').pop().toLowerCase()
  
  // Validasi tipe MIME
  const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
  const maxSize = 10 * 1024 * 1024 // 10MB

  if (!allowedExtensions.includes(fileExtension) && !allowedTypes.includes(file.type)) {
    toast.error('Format file tidak didukung. Gunakan PDF, DOC, atau DOCX')
    return
  }

  if (file.size > maxSize) {
    toast.error('Ukuran file terlalu besar. Maksimal 10MB')
    return
  }

  selectedFile.value = file
  console.log('File selected:', file.name, file.type, file.size)
}

const uploadReport = async () => {
  if (!selectedFile.value) {
    toast.error('Pilih file terlebih dahulu')
    return
  }
  
  uploading.value = true
  const formData = new FormData()
  formData.append('report', selectedFile.value)
  
  console.log('Uploading file:', selectedFile.value.name)
  console.log('File type:', selectedFile.value.type)
  console.log('File size:', selectedFile.value.size)
  
  try {
    const response = await axios.post('/siswa/report/upload', formData, {
      headers: { 
        'Content-Type': 'multipart/form-data'
      }
    })
    
    console.log('Upload success:', response.data)
    toast.success('Laporan berhasil diupload')
    selectedFile.value = null
    await loadReport()
  } catch (error) {
    console.error('Upload error:', error)
    console.error('Response:', error.response?.data)
    
    let errorMessage = 'Gagal mengupload laporan'
    if (error.response?.data?.message) {
      errorMessage = error.response.data.message
    }
    if (error.response?.data?.errors) {
      const errors = error.response.data.errors
      errorMessage = Object.values(errors).flat().join(', ')
    }
    
    toast.error(errorMessage)
  } finally {
    uploading.value = false
  }
}

const deleteReport = async () => {
  if (!confirm('Apakah Anda yakin ingin menghapus laporan ini?')) return
  
  deleting.value = true
  try {
    await axios.delete('/siswa/report/delete')
    toast.success('Laporan berhasil dihapus')
    await loadReport()
  } catch (error) {
    console.error('Delete error:', error)
    toast.error(error.response?.data?.message || 'Gagal menghapus laporan')
  } finally {
    deleting.value = false
  }
}

const loadReport = async () => {
  try {
    const response = await axios.get('/siswa/report')
    console.log('Load report response:', response.data)
    
    if (response.data && response.data.id) {
      existingReport.value = response.data
      lastUpload.value = formatDate(response.data.created_at)
      
      // Update status berdasarkan response dari backend
      const status = response.data.status || 'pending'
      const statusMap = {
        pending: { text: 'Menunggu Review', color: 'text-yellow-600' },
        approved: { text: 'Disetujui', color: 'text-green-600' },
        rejected: { text: 'Ditolak', color: 'text-red-600' }
      }
      reportStatus.value = statusMap[status] || { text: 'Sudah Upload', color: 'text-green-600' }
    } else {
      existingReport.value = null
      reportStatus.value = { text: 'Belum Upload', color: 'text-red-600' }
      lastUpload.value = ''
    }
  } catch (error) {
    console.error('Failed to load report:', error)
    existingReport.value = null
    reportStatus.value = { text: 'Belum Upload', color: 'text-red-600' }
  }
}

onMounted(() => {
  loadReport()
})
</script>