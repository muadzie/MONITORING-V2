<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Penempatan PKL</h1>
        <p class="text-gray-500 mt-1">Kelola penempatan siswa ke perusahaan mitra</p>
      </div>
      <div class="flex gap-2">
        <router-link 
          to="/admin/map" 
          class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"></path>
          </svg>
          Peta Sebaran
        </router-link>
        <button 
          @click="openModal" 
          class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2.5 rounded-xl hover:shadow-lg transition-all duration-300 hover:scale-105 flex items-center gap-2"
        >
          <PlusIcon class="w-5 h-5" />
          Tambah Penempatan
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Total Penempatan</p>
        <p class="text-2xl font-bold">{{ placements.length }}</p>
      </div>
      <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Aktif</p>
        <p class="text-2xl font-bold">{{ placements.filter(p => p.status === 'active').length }}</p>
      </div>
      <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Selesai</p>
        <p class="text-2xl font-bold">{{ placements.filter(p => p.status === 'completed').length }}</p>
      </div>
      <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Total Perusahaan</p>
        <p class="text-2xl font-bold">{{ uniqueCompanies }}</p>
      </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-xl shadow-sm p-4">
      <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
          <input 
            v-model="search" 
            type="text" 
            placeholder="Cari berdasarkan nama siswa atau perusahaan..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          >
        </div>
        <select v-model="filters.status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
          <option value="">Semua Status</option>
          <option value="active">Aktif</option>
          <option value="completed">Selesai</option>
          <option value="canceled">Dibatalkan</option>
        </select>
        <button @click="resetFilters" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">Reset</button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
        <p class="mt-4 text-gray-500">Memuat data penempatan...</p>
      </div>
    </div>

    <!-- Table -->
    <div v-else-if="filteredPlacements.length > 0" class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Siswa</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NISN</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Perusahaan</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Selesai</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="(placement, index) in filteredPlacements" :key="placement.id" class="hover:bg-gray-50 transition">
              <td class="px-6 py-4 text-sm text-gray-500">{{ index + 1 }}</td>
              <td class="px-6 py-4 font-medium text-gray-800">{{ placement.student?.name || '-' }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ placement.student?.nisn || '-' }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ placement.company?.name || '-' }}</td>
              <td class="px-6 py-4 text-sm">{{ formatDate(placement.start_date) }}</td>
              <td class="px-6 py-4 text-sm">{{ formatDate(placement.end_date) }}</td>
              <td class="px-6 py-4">
                <span :class="getStatusClass(placement.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                  {{ getStatusText(placement.status) }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <button @click="editPlacement(placement)" class="p-1 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Edit">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                  </button>
                  <button @click="deletePlacement(placement)" class="p-1 text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!loading && filteredPlacements.length === 0" class="bg-white rounded-xl p-12 text-center">
      <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
      </svg>
      <p class="text-gray-500">Belum ada data penempatan PKL</p>
      <button @click="openModal" class="mt-3 text-indigo-600 hover:underline">Tambah penempatan pertama</button>
    </div>

    <!-- Modal Tambah/Edit Penempatan -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50" @click.self="closeModal">
      <div class="bg-white rounded-2xl w-full max-w-lg animate-fade-in-up">
        <div class="sticky top-0 bg-white p-5 border-b rounded-t-2xl">
          <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-xl font-bold text-gray-800">{{ isEdit ? 'Edit Penempatan' : 'Tambah Penempatan' }}</h2>
                <p class="text-sm text-gray-500">{{ isEdit ? 'Ubah data penempatan' : 'Tempatkan siswa ke perusahaan' }}</p>
              </div>
            </div>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition">
              <XMarkIcon class="w-6 h-6" />
            </button>
          </div>
        </div>

        <form @submit.prevent="savePlacement" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Siswa <span class="text-red-500">*</span></label>
            <select 
              v-model="form.student_id" 
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              required
            >
              <option value="">-- Pilih Siswa --</option>
              <option v-for="student in availableStudents" :key="student.id" :value="student.id">
                {{ student.name }} ({{ student.nisn }})
              </option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Perusahaan <span class="text-red-500">*</span></label>
            <select 
              v-model="form.company_id" 
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              required
            >
              <option value="">-- Pilih Perusahaan --</option>
              <option v-for="company in companies" :key="company.id" :value="company.id">
                {{ company.name }} (Radius: {{ company.radius }}m)
              </option>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
              <input 
                v-model="form.start_date" 
                type="date" 
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                required
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
              <input 
                v-model="form.end_date" 
                type="date" 
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                required
              >
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select 
              v-model="form.status" 
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="active">Aktif</option>
              <option value="completed">Selesai</option>
              <option value="canceled">Dibatalkan</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
            <textarea 
              v-model="form.notes" 
              rows="2" 
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              placeholder="Catatan tambahan..."
            ></textarea>
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="closeModal" class="px-5 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium">
              Batal
            </button>
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:shadow-lg transition-all font-medium" :disabled="saving">
              {{ saving ? 'Menyimpan...' : (isEdit ? 'Update' : 'Simpan') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'
import { PlusIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const toast = useToast()
const placements = ref([])
const students = ref([])
const companies = ref([])
const loading = ref(false)
const showModal = ref(false)
const isEdit = ref(false)
const saving = ref(false)
const search = ref('')
const filters = ref({ status: '' })

const form = ref({
  id: null,
  student_id: '',
  company_id: '',
  start_date: new Date().toISOString().split('T')[0],
  end_date: '',
  status: 'active',
  notes: ''
})

// Computed
const uniqueCompanies = computed(() => {
  return new Set(placements.value.map(p => p.company_id)).size
})

const availableStudents = computed(() => {
  // Siswa yang belum ditempatkan (tidak memiliki placement active)
  const placedStudentIds = placements.value
    .filter(p => p.status === 'active')
    .map(p => p.student_id)
  return students.value.filter(s => !placedStudentIds.includes(s.id))
})

const filteredPlacements = computed(() => {
  let result = placements.value
  
  if (search.value) {
    const searchLower = search.value.toLowerCase()
    result = result.filter(p => 
      p.student?.name?.toLowerCase().includes(searchLower) ||
      p.company?.name?.toLowerCase().includes(searchLower)
    )
  }
  
  if (filters.value.status) {
    result = result.filter(p => p.status === filters.value.status)
  }
  
  return result
})

// Helper functions
const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID')
}

const getStatusText = (status) => {
  const map = { active: 'Aktif', completed: 'Selesai', canceled: 'Dibatalkan' }
  return map[status] || status
}

const getStatusClass = (status) => {
  const map = {
    active: 'bg-green-100 text-green-800',
    completed: 'bg-blue-100 text-blue-800',
    canceled: 'bg-red-100 text-red-800'
  }
  return map[status] || 'bg-gray-100 text-gray-800'
}

// Fetch data
const fetchPlacements = async () => {
  loading.value = true
  try {
    const res = await axios.get('/admin/placements')
    placements.value = res.data
  } catch (error) {
    console.error('Fetch placements error:', error)
    toast.error('Gagal memuat data penempatan')
  } finally {
    loading.value = false
  }
}

const fetchStudents = async () => {
  try {
    const res = await axios.get('/admin/students')
    students.value = res.data
  } catch (error) {
    console.error('Fetch students error:', error)
  }
}

const fetchCompanies = async () => {
  try {
    const res = await axios.get('/companies')
    companies.value = res.data
  } catch (error) {
    console.error('Fetch companies error:', error)
  }
}

// CRUD operations
const openModal = () => {
  isEdit.value = false
  form.value = {
    id: null,
    student_id: '',
    company_id: '',
    start_date: new Date().toISOString().split('T')[0],
    end_date: '',
    status: 'active',
    notes: ''
  }
  showModal.value = true
}

const editPlacement = (placement) => {
  isEdit.value = true
  form.value = {
    id: placement.id,
    student_id: placement.student_id,
    company_id: placement.company_id,
    start_date: placement.start_date,
    end_date: placement.end_date,
    status: placement.status,
    notes: placement.notes || ''
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const savePlacement = async () => {
  saving.value = true
  try {
    if (isEdit.value) {
      await axios.put(`/admin/placements/${form.value.id}`, form.value)
      toast.success('Penempatan berhasil diupdate')
    } else {
      await axios.post('/admin/placements', form.value)
      toast.success('Penempatan berhasil ditambahkan')
    }
    closeModal()
    fetchPlacements()
    fetchStudents() // Refresh available students
  } catch (error) {
    console.error('Save placement error:', error)
    toast.error(error.response?.data?.message || 'Gagal menyimpan penempatan')
  } finally {
    saving.value = false
  }
}

const deletePlacement = async (placement) => {
  if (confirm(`Hapus penempatan siswa ${placement.student?.name} di ${placement.company?.name}?`)) {
    try {
      await axios.delete(`/admin/placements/${placement.id}`)
      toast.success('Penempatan berhasil dihapus')
      fetchPlacements()
      fetchStudents()
    } catch (error) {
      console.error('Delete placement error:', error)
      toast.error('Gagal menghapus penempatan')
    }
  }
}

const resetFilters = () => {
  search.value = ''
  filters.value.status = ''
}

onMounted(() => {
  fetchPlacements()
  fetchStudents()
  fetchCompanies()
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