<template>
  <div class="space-y-6">
    <!-- Header Premium -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-purple-600 via-pink-500 to-purple-600 p-8 text-white">
      <div class="relative z-10 flex items-center gap-4">
        <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
          </svg>
        </div>
        <div>
          <h1 class="text-3xl font-bold">Penilaian Logbook PKL</h1>
          <p class="mt-2 text-purple-100">Berikan penilaian untuk logbook siswa magang dengan mudah</p>
        </div>
      </div>
    </div>

    <!-- Stats Cards Premium -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <div class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-2xl p-5 text-white shadow-lg hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm opacity-90">Total Logbook</p>
            <p class="text-3xl font-bold mt-1">{{ logbooks.length }}</p>
          </div>
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
          </div>
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-yellow-500 to-yellow-700 rounded-2xl p-5 text-white shadow-lg hover:shadow-xl transition-all duration-300 cursor-pointer" @click="filterStatus = 'pending'">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm opacity-90">Menunggu Nilai</p>
            <p class="text-3xl font-bold mt-1">{{ pendingCount }}</p>
          </div>
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
        <div class="mt-2 text-xs opacity-75">Perlu direview</div>
      </div>
      
      <div class="bg-gradient-to-br from-green-500 to-green-700 rounded-2xl p-5 text-white shadow-lg hover:shadow-xl transition-all duration-300 cursor-pointer" @click="filterStatus = 'graded'">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm opacity-90">Sudah Dinilai</p>
            <p class="text-3xl font-bold mt-1">{{ gradedCount }}</p>
          </div>
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
          </div>
        </div>
        <div class="mt-2 text-xs opacity-75">Selesai dinilai</div>
      </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-2xl shadow-lg p-5 border border-gray-100">
      <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
          </svg>
          <input v-model="search" type="text" placeholder="Cari siswa atau aktivitas..." class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500">
        </div>
        <div class="flex gap-2">
          <select v-model="filterStatus" class="px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 bg-white">
            <option value="all">📋 Semua Logbook</option>
            <option value="pending">⏳ Menunggu Nilai</option>
            <option value="graded">✅ Sudah Dinilai</option>
          </select>
          <button @click="refreshData" class="px-4 py-2.5 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Refresh
          </button>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white rounded-2xl shadow-sm p-12 text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-600 mx-auto"></div>
      <p class="mt-4 text-gray-500">Memuat data logbook...</p>
    </div>

    <!-- Logbook Cards Premium -->
    <div v-else-if="filteredLogbooks.length > 0" class="space-y-4">
      <div v-for="log in filteredLogbooks" :key="log.id" 
        class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 hover:shadow-xl transition-all duration-300"
        :class="{'ring-2 ring-yellow-400': !log.grade}"
      >
        <div class="p-6">
          <!-- Header Card -->
          <div class="flex flex-col md:flex-row justify-between items-start gap-4 pb-4 border-b border-gray-100">
            <div class="flex items-center gap-3">
              <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-pink-100 rounded-xl flex items-center justify-center text-purple-600 font-bold text-lg shadow-sm">
                {{ log.student?.name?.charAt(0) || 'S' }}
              </div>
              <div>
                <p class="font-semibold text-gray-800 text-lg">{{ log.student?.name }}</p>
                <p class="text-xs text-gray-500">NISN: {{ log.student?.nisn }}</p>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <span v-if="log.grade" class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Sudah Dinilai ({{ log.grade }})
              </span>
              <span v-else class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-medium flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Menunggu Nilai
              </span>
            </div>
          </div>

          <!-- Body Card -->
          <div class="py-4">
            <div class="flex flex-wrap gap-4 mb-3">
              <div class="flex items-center gap-1 text-sm text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ formatDate(log.date) }}
              </div>
              <div class="flex items-center gap-1 text-sm text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                {{ log.activity }}
              </div>
            </div>
            <p class="text-gray-600 leading-relaxed">{{ log.description }}</p>
            
            <!-- Lampiran -->
            <div v-if="log.attachment" class="mt-3">
              <a :href="log.attachment_url" target="_blank" class="inline-flex items-center gap-1 text-purple-600 text-sm hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                </svg>
                📎 Lihat Lampiran
              </a>
            </div>
          </div>

          <!-- Form Penilaian Premium -->
          <div class="bg-gradient-to-r from-gray-50 to-purple-50 rounded-xl p-5 mt-2">
            <div class="flex items-center gap-2 mb-4">
              <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
              </div>
              <h4 class="font-semibold text-gray-800">Form Penilaian</h4>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <!-- Rating Slider untuk Nilai -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">⭐ Nilai (0-100)</label>
                <div class="flex items-center gap-4">
                  <input 
                    v-model.number="log.form.grade" 
                    type="range" 
                    min="0" 
                    max="100" 
                    step="1"
                    class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                    :style="{ background: `linear-gradient(to right, #9333ea 0%, #9333ea ${log.form.grade || 0}%, #e5e7eb ${log.form.grade || 0}%, #e5e7eb 100%)` }"
                  />
                  <div class="relative">
                    <input 
                      v-model.number="log.form.grade" 
                      type="number" 
                      min="0" 
                      max="100" 
                      class="w-20 px-2 py-2 text-center border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500"
                    >
                    <span class="absolute -right-6 top-1/2 transform -translate-y-1/2 text-gray-500"></span>
                  </div>
                </div>
                <!-- Indikator Grade -->
                <div class="mt-2 flex items-center gap-2">
                  <div class="flex-1 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-300" 
                      :class="{
                        'bg-red-500': (log.form.grade || 0) < 60,
                        'bg-yellow-500': (log.form.grade || 0) >= 60 && (log.form.grade || 0) < 75,
                        'bg-blue-500': (log.form.grade || 0) >= 75 && (log.form.grade || 0) < 90,
                        'bg-green-500': (log.form.grade || 0) >= 90
                      }"
                      :style="{ width: (log.form.grade || 0) + '%' }"
                    ></div>
                  </div>
                  <span class="text-xs font-semibold" 
                    :class="{
                      'text-red-600': (log.form.grade || 0) < 60,
                      'text-yellow-600': (log.form.grade || 0) >= 60 && (log.form.grade || 0) < 75,
                      'text-blue-600': (log.form.grade || 0) >= 75 && (log.form.grade || 0) < 90,
                      'text-green-600': (log.form.grade || 0) >= 90
                    }">
                    {{ getGradeLabel(log.form.grade) }}
                  </span>
                </div>
              </div>

              <!-- Feedback -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">💬 Feedback</label>
                <textarea 
                  v-model="log.form.feedback" 
                  rows="2" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                ></textarea>
              </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="mt-4 flex justify-end">
              <button 
                @click="submitAssessment(log)" 
                class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl font-medium hover:shadow-lg transition-all duration-300 hover:scale-105 flex items-center gap-2 disabled:opacity-50"
                :disabled="submitting"
              >
                <svg v-if="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <svg v-else class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                {{ submitting ? 'Menyimpan...' : '💾 Simpan Penilaian' }}
              </button>
            </div>

            <!-- Tampilkan nilai sebelumnya jika sudah dinilai -->
            <div v-if="log.grade && log.form.grade !== log.grade" class="mt-3 p-2 bg-yellow-50 rounded-lg text-xs text-yellow-700">
              ⚠️ Nilai sebelumnya: {{ log.grade }} - {{ log.feedback }}
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State Premium -->
    <div v-else-if="!loading && filteredLogbooks.length === 0" class="bg-white rounded-2xl shadow-sm p-12 text-center">
      <div class="w-24 h-24 mx-auto bg-purple-100 rounded-full flex items-center justify-center mb-4">
        <svg class="w-12 h-12 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
      </div>
      <p class="text-gray-500 text-lg font-medium">Belum ada logbook</p>
      <p class="text-sm text-gray-400 mt-1">Belum ada logbook dari siswa magang yang perlu dinilai</p>
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
const search = ref('')
const filterStatus = ref('all')

// Computed
const pendingCount = computed(() => logbooks.value.filter(l => !l.grade).length)
const gradedCount = computed(() => logbooks.value.filter(l => l.grade).length)

const filteredLogbooks = computed(() => {
  let result = logbooks.value
  
  if (filterStatus.value === 'pending') {
    result = result.filter(l => !l.grade)
  } else if (filterStatus.value === 'graded') {
    result = result.filter(l => l.grade)
  }
  
  if (search.value) {
    const searchLower = search.value.toLowerCase()
    result = result.filter(l => 
      l.student?.name?.toLowerCase().includes(searchLower) ||
      l.activity?.toLowerCase().includes(searchLower)
    )
  }
  
  return result
})

// Helper functions
const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', { 
    day: 'numeric', 
    month: 'long', 
    year: 'numeric' 
  })
}

const getGradeLabel = (grade) => {
  if (!grade) return 'Belum Dinilai'
  if (grade >= 90) return 'A (Sangat Baik)'
  if (grade >= 80) return 'B (Baik)'
  if (grade >= 70) return 'C (Cukup)'
  if (grade >= 60) return 'D (Kurang)'
  return 'E (Sangat Kurang)'
}

// Fetch data
const fetchLogbooks = async () => {
  loading.value = true
  try {
    const res = await axios.get('/perusahaan/logbooks')
    const data = res.data.data || res.data || []
    logbooks.value = data.map(l => ({
      ...l,
      form: {
        grade: l.grade || '',
        feedback: l.feedback || ''
      }
    }))
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

// Submit assessment
const submitAssessment = async (log) => {
  if (log.form.grade === '' || log.form.grade === null) {
    toast.error('Masukkan nilai terlebih dahulu')
    return
  }
  if (log.form.grade < 0 || log.form.grade > 100) {
    toast.error('Nilai harus antara 0-100')
    return
  }
  
  submitting.value = true
  try {
    await axios.put(`/perusahaan/logbooks/${log.id}/grade`, {
      grade: log.form.grade,
      feedback: log.form.feedback
    })
    toast.success('Penilaian berhasil disimpan')
    await fetchLogbooks()
  } catch (error) {
    console.error('Submit error:', error)
    toast.error(error.response?.data?.message || 'Gagal menyimpan penilaian')
  } finally {
    submitting.value = false
  }
}

onMounted(() => {
  fetchLogbooks()
})
</script>

<style scoped>
/* Custom range input styling */
input[type="range"] {
  -webkit-appearance: none;
}

input[type="range"]:focus {
  outline: none;
}

input[type="range"]::-webkit-slider-thumb {
  -webkit-appearance: none;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: #9333ea;
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}

input[type="range"]::-webkit-slider-thumb:hover {
  transform: scale(1.2);
  background: #7e22ce;
}
</style>