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
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
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
            placeholder="Cari berdasarkan nama siswa, perusahaan, atau guru pembimbing..."
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
        <p class="mt-4 text-gray-500">Memuat data...</p>
      </div>
    </div>

    <!-- Table -->
    <div v-else-if="filteredPlacements.length > 0" class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Siswa</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NISN</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Perusahaan</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Guru Pembimbing</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Selesai</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="(placement, index) in filteredPlacements" :key="placement.id" class="hover:bg-gray-50 transition">
              <td class="px-6 py-4 text-sm text-gray-500">{{ index + 1 }}</td>
              <td class="px-6 py-4 font-medium text-gray-800">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center text-blue-600 font-bold text-sm">
                    {{ getInitial(placement.student?.name) }}
                  </div>
                  {{ placement.student?.name || '-' }}
                </div>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ placement.student?.nisn || '-' }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ placement.company?.name || '-' }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">
                <div class="flex items-center gap-1">
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                  </svg>
                  {{ placement.teacher?.name || 'Belum ditentukan' }}
                </div>
              </td>
              <td class="px-6 py-4 text-sm">{{ formatDate(placement.start_date) }}</td>
              <td class="px-6 py-4 text-sm">{{ formatDate(placement.end_date) }}</td>
              <td class="px-6 py-4">
                <span :class="getStatusClass(placement.status)" class="px-2 py-1 rounded-full text-xs font-medium">
                  {{ getStatusText(placement.status) }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <button @click="editPlacement(placement)" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Edit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                  </button>
                  <button @click="deletePlacement(placement)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
      <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
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
                <p class="text-sm text-gray-500">{{ isEdit ? 'Ubah data penempatan' : 'Tempatkan siswa ke perusahaan dengan guru pembimbing' }}</p>
              </div>
            </div>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <form @submit.prevent="savePlacement" class="p-6 space-y-4">
          <!-- Pilih Siswa -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Siswa <span class="text-red-500">*</span></label>
            <select 
              v-model="form.student_id" 
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              required
            >
              <option value="">-- Pilih Siswa --</option>
              <option v-for="student in availableStudents" :key="student.id" :value="student.id">
                {{ student.name }} - {{ student.nisn }} (Kelas: {{ student.class_name || '-' }})
              </option>
            </select>
            <p v-if="availableStudents.length === 0" class="text-xs text-yellow-600 mt-1">
              {{ students.length === 0 ? 'Belum ada data siswa. Silakan tambahkan siswa terlebih dahulu.' : 'Semua siswa sudah memiliki penempatan aktif.' }}
            </p>
          </div>

          <!-- Pilih Perusahaan -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Perusahaan <span class="text-red-500">*</span></label>
            <select 
              v-model="form.company_id" 
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              required
              @change="onCompanyChange"
            >
              <option value="">-- Pilih Perusahaan --</option>
              <option v-for="company in companies" :key="company.id" :value="company.id">
                {{ company.name }} - {{ company.city || 'Lokasi tidak tersedia' }}
              </option>
            </select>
            <p v-if="companies.length === 0" class="text-xs text-red-600 mt-1">
              Belum ada data perusahaan. Silakan tambahkan perusahaan terlebih dahulu.
            </p>
          </div>

          <!-- Pilih Guru Pembimbing -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Guru Pembimbing <span class="text-red-500">*</span></label>
            <select 
              v-model="form.teacher_id" 
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              required
            >
              <option value="">-- Pilih Guru Pembimbing --</option>
              <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
                {{ teacher.name }} - {{ teacher.mata_pelajaran || 'Guru' }}
              </option>
            </select>
            <p v-if="teachers.length === 0" class="text-xs text-red-600 mt-1">
              Belum ada data guru. Silakan tambahkan guru terlebih dahulu.
            </p>
          </div>

          <!-- Tanggal -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
              <input 
                v-model="form.start_date" 
                type="date" 
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                required
              >
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
              <input 
                v-model="form.end_date" 
                type="date" 
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                required
                :min="form.start_date"
              >
            </div>
          </div>

          <!-- Jam Mulai Shift -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai Shift</label>
            <input
              v-model="form.shift_start"
              type="time"
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
            <p class="text-xs text-gray-400 mt-1">Kosongi jika pakai jam default perusahaan</p>
          </div>

          <!-- Status -->
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

          <!-- Catatan -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
            <textarea 
              v-model="form.notes" 
              rows="3" 
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
              placeholder="Catatan tambahan (opsional)..."
            ></textarea>
          </div>

          <!-- Informasi Ringkasan -->
          <div v-if="selectedCompany" class="bg-gray-50 rounded-lg p-3 text-sm">
            <p class="font-medium text-gray-700 mb-2">Informasi Perusahaan:</p>
            <div class="space-y-1 text-gray-600">
              <p>📍 Alamat: {{ selectedCompany.address || '-' }}</p>
              <p>🏙️ Kota: {{ selectedCompany.city || '-' }}</p>
              <p>📞 Telepon: {{ selectedCompany.phone || '-' }}</p>
              <p v-if="selectedCompany.radius">🎯 Radius Absensi: {{ selectedCompany.radius }} meter</p>
            </div>
          </div>

          <!-- Tombol Aksi -->
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
import { ref, computed, onMounted, watch } from 'vue'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'
import { useConfirm } from '../../composables/useConfirm'

const toast = useToast()
const { confirm } = useConfirm()

// State
const placements = ref([])
const students = ref([])
const companies = ref([])
const teachers = ref([])
const loading = ref(false)
const loadingStudents = ref(false)
const loadingCompanies = ref(false)
const loadingTeachers = ref(false)
const showModal = ref(false)
const isEdit = ref(false)
const saving = ref(false)
const search = ref('')
const filters = ref({ status: '' })

// Form data
const form = ref({
  id: null,
  student_id: '',
  company_id: '',
  teacher_id: '',
  start_date: new Date().toISOString().split('T')[0],
  end_date: '',
  status: 'active',
  notes: '',
  shift_start: ''
})

// Helper functions
const getInitial = (name) => name?.charAt(0) || 'S'
const formatDate = (date) => date ? new Date(date).toLocaleDateString('id-ID') : '-'
const getStatusText = (status) => ({ active: 'Aktif', completed: 'Selesai', canceled: 'Dibatalkan' }[status] || status)
const getStatusClass = (status) => ({
  active: 'bg-green-100 text-green-800',
  completed: 'bg-blue-100 text-blue-800',
  canceled: 'bg-red-100 text-red-800'
}[status] || 'bg-gray-100 text-gray-800')

// Computed
const uniqueCompanies = computed(() => new Set(placements.value.map(p => p.company_id)).size)

// Siswa yang BELUM memiliki penempatan aktif
const availableStudents = computed(() => {
  // Ambil ID siswa yang sudah memiliki penempatan aktif
  const placedStudentIds = placements.value
    .filter(p => p.status === 'active')
    .map(p => p.student_id)
  
  // Filter siswa yang belum ditempatkan
  return students.value.filter(s => !placedStudentIds.includes(s.id))
})

const selectedCompany = computed(() => companies.value.find(c => c.id === form.value.company_id))

const filteredPlacements = computed(() => {
  let result = placements.value
  if (search.value) {
    const searchLower = search.value.toLowerCase()
    result = result.filter(p => 
      (p.student?.name || '').toLowerCase().includes(searchLower) ||
      (p.company?.name || '').toLowerCase().includes(searchLower) ||
      (p.teacher?.name || '').toLowerCase().includes(searchLower)
    )
  }
  if (filters.value.status) result = result.filter(p => p.status === filters.value.status)
  return result
})

// ============================================================
// API CALLS
// ============================================================

const fetchCompanies = async () => {
  loadingCompanies.value = true
  try {
    const res = await axios.get('/companies')
    console.log('Companies response:', res.data)
    
    let data = Array.isArray(res.data) ? res.data : (res.data.data || [])
    
    companies.value = data.map(c => ({
      id: c.id,
      name: c.name,
      address: c.address,
      city: c.city || 'Jakarta',
      phone: c.phone,
      radius: c.radius || 100,
      latitude: c.latitude,
      longitude: c.longitude
    }))
    
    console.log('Companies loaded:', companies.value.length)
    if (companies.value.length === 0) {
      toast.warning('Belum ada data perusahaan. Silakan tambahkan perusahaan terlebih dahulu.')
    }
  } catch (error) {
    console.error('Fetch companies error:', error)
    toast.error('Gagal memuat data perusahaan')
  } finally {
    loadingCompanies.value = false
  }
}

const fetchStudents = async () => {
  loadingStudents.value = true
  try {
    const res = await axios.get('/admin/students')
    console.log('Students response:', res.data)
    
    let data = Array.isArray(res.data) ? res.data : (res.data.data || [])
    
    students.value = data.map(s => ({
      id: s.id,
      name: s.name,
      nisn: s.nisn || '-',
      class_name: s.class?.name || s.kelas?.name || '-'
    }))
    
    console.log('Students loaded:', students.value.length)
    if (students.value.length === 0) {
      toast.warning('Belum ada data siswa. Silakan tambahkan siswa terlebih dahulu.')
    }
  } catch (error) {
    console.error('Fetch students error:', error)
    toast.error('Gagal memuat data siswa')
  } finally {
    loadingStudents.value = false
  }
}

const fetchTeachers = async () => {
  loadingTeachers.value = true
  try {
    const res = await axios.get('/admin/teachers')
    console.log('Teachers response:', res.data)
    
    let data = Array.isArray(res.data) ? res.data : (res.data.data || [])
    
    teachers.value = data.map(t => ({
      id: t.id,
      name: t.name,
      nip: t.nip || '-',
      mata_pelajaran: t.mata_pelajaran || '-'
    }))
    
    console.log('Teachers loaded:', teachers.value.length)
    if (teachers.value.length === 0) {
      toast.warning('Belum ada data guru. Silakan tambahkan guru terlebih dahulu.')
    }
  } catch (error) {
    console.error('Fetch teachers error:', error)
    toast.error('Gagal memuat data guru')
  } finally {
    loadingTeachers.value = false
  }
}

const fetchPlacements = async () => {
  loading.value = true
  try {
    const res = await axios.get('/admin/placements')
    console.log('Placements response:', res.data)
    
    let data = Array.isArray(res.data) ? res.data : (res.data.data || [])
    
    placements.value = data
    console.log('Placements loaded:', placements.value.length)
  } catch (error) {
    console.error('Fetch placements error:', error)
    // Jangan tampilkan error toast jika hanya karena belum ada data
    placements.value = []
  } finally {
    loading.value = false
  }
}

// CRUD Operations
const openModal = () => {
  // Validasi sebelum buka modal
  if (students.value.length === 0) {
    toast.error('Belum ada data siswa. Silakan tambahkan siswa terlebih dahulu.')
    return
  }
  if (companies.value.length === 0) {
    toast.error('Belum ada data perusahaan. Silakan tambahkan perusahaan terlebih dahulu.')
    return
  }
  if (teachers.value.length === 0) {
    toast.error('Belum ada data guru. Silakan tambahkan guru terlebih dahulu.')
    return
  }
  if (availableStudents.value.length === 0) {
    toast.warning('Semua siswa sudah memiliki penempatan aktif.')
    return
  }
  
  isEdit.value = false
  form.value = {
    id: null,
    student_id: '',
    company_id: '',
    teacher_id: '',
    start_date: new Date().toISOString().split('T')[0],
    end_date: '',
    status: 'active',
    notes: '',
    shift_start: ''
  }
  showModal.value = true
}

const editPlacement = (placement) => {
  isEdit.value = true
  form.value = {
    id: placement.id,
    student_id: placement.student_id,
    company_id: placement.company_id,
    teacher_id: placement.teacher_id || '',
    start_date: placement.start_date,
    end_date: placement.end_date,
    status: placement.status,
    notes: placement.notes || '',
    shift_start: placement.shift_start || ''
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const savePlacement = async () => {
  // Validasi lengkap
  if (!form.value.student_id) {
    toast.error('Pilih siswa terlebih dahulu')
    return
  }
  if (!form.value.company_id) {
    toast.error('Pilih perusahaan terlebih dahulu')
    return
  }
  if (!form.value.teacher_id) {
    toast.error('Pilih guru pembimbing terlebih dahulu')
    return
  }
  if (!form.value.start_date) {
    toast.error('Tanggal mulai harus diisi')
    return
  }
  if (!form.value.end_date) {
    toast.error('Tanggal selesai harus diisi')
    return
  }
  if (form.value.start_date > form.value.end_date) {
    toast.error('Tanggal selesai harus setelah tanggal mulai')
    return
  }
  
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
    await fetchPlacements()
    await fetchStudents()
  } catch (error) {
    console.error('Save placement error:', error)
    const message = error.response?.data?.message || 'Gagal menyimpan penempatan'
    toast.error(message)
  } finally {
    saving.value = false
  }
}

const deletePlacement = async (placement) => {
  const ok = await confirm({ title: 'Hapus Penempatan', message: `Hapus penempatan siswa ${placement.student?.name} di ${placement.company?.name}?` })
  if (!ok) return
  
  try {
    await axios.delete(`/admin/placements/${placement.id}`)
    toast.success('Penempatan berhasil dihapus')
    await fetchPlacements()
    await fetchStudents()
  } catch (error) {
    console.error('Delete placement error:', error)
    toast.error('Gagal menghapus penempatan')
  }
}

const resetFilters = () => {
  search.value = ''
  filters.value.status = ''
}

const onCompanyChange = () => {
  console.log('Selected company:', selectedCompany.value)
}

// Watch untuk validasi tanggal
watch(() => form.value.start_date, (newStartDate) => {
  if (form.value.end_date && newStartDate > form.value.end_date) {
    form.value.end_date = ''
  }
})

// Lifecycle
onMounted(async () => {
  console.log('🟡 Loading all data...')
  await Promise.all([
    fetchPlacements(),
    fetchStudents(),
    fetchCompanies(),
    fetchTeachers()
  ])
  console.log('✅ All data loaded')
  console.log('Students total:', students.value.length)
  console.log('Students available:', availableStudents.value.length)
  console.log('Companies:', companies.value.length)
  console.log('Teachers:', teachers.value.length)
  console.log('Placements:', placements.value.length)
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