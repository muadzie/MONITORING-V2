<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Siswa</h1>
        <p class="text-gray-500 mt-1">Kelola data siswa peserta PKL</p>
      </div>
      <button 
        @click="openModal" 
        class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2.5 rounded-xl hover:shadow-lg transition-all duration-300 hover:scale-105 flex items-center gap-2"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Tambah Siswa
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Total Siswa</p>
        <p class="text-2xl font-bold">{{ students.length }}</p>
      </div>
      <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Sudah Ditempatkan</p>
        <p class="text-2xl font-bold">{{ students.filter(s => s.company_id).length }}</p>
      </div>
      <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Belum Ditempatkan</p>
        <p class="text-2xl font-bold">{{ students.filter(s => !s.company_id).length }}</p>
      </div>
      <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Aktif</p>
        <p class="text-2xl font-bold">{{ students.filter(s => s.is_active !== false).length }}</p>
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
            placeholder="Cari siswa (NISN, Nama, Email)..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          >
        </div>
        <select v-model="filters.company" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
          <option value="">Semua Perusahaan</option>
          <option v-for="c in companies" :key="c.id" :value="c.id">{{ c.name }}</option>
        </select>
        <button @click="resetFilters" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">Reset</button>
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NISN</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Perusahaan</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="(student, index) in filteredStudents" :key="student.id" class="hover:bg-gray-50 transition">
              <td class="px-6 py-4 text-sm text-gray-500">{{ index + 1 }}</td>
              <td class="px-6 py-4 text-sm font-medium">{{ student.nisn }}</td>
              <td class="px-6 py-4 font-medium text-gray-800">{{ student.name }}</td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ student.email }}</td>
              <td class="px-6 py-4">
                <span v-if="student.company" class="inline-flex items-center gap-1 px-2 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                  {{ student.company.name }}
                </span>
                <span v-else class="px-2 py-1 bg-gray-100 text-gray-500 rounded-full text-xs">Belum ditempatkan</span>
              </td>
              <td class="px-6 py-4">
                <span class="px-2 py-1 rounded-full text-xs" :class="student.is_active !== false ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                  {{ student.is_active !== false ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                  <button @click="editStudent(student)" class="p-1 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Edit">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                  </button>
                  <button @click="deleteStudent(student)" class="p-1 text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredStudents.length === 0 && !loading">
              <td colspan="7" class="px-6 py-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <p class="text-gray-500">Belum ada data siswa</p>
                <button @click="openModal" class="mt-3 text-indigo-600 hover:underline">Tambah siswa pertama</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Tambah/Edit -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50" @click.self="closeModal">
      <div class="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto animate-fade-in-up">
        <div class="sticky top-0 bg-white p-6 border-b rounded-t-2xl">
          <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-gray-800">{{ isEdit ? 'Edit Siswa' : 'Tambah Siswa' }}</h3>
                <p class="text-sm text-gray-500">{{ isEdit ? 'Ubah data siswa' : 'Masukkan data siswa baru' }}</p>
              </div>
            </div>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <form @submit.prevent="submitStudent" class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">NISN</label>
              <input v-model="form.nisn" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="0012345678" required>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
              <input v-model="form.name" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="Ahmad Fauzi" required>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input v-model="form.email" type="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="siswa@example.com" required>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
              <input v-model="form.phone" type="tel" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="08123456789">
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Perusahaan PKL</label>
            <select v-model="form.company_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
              <option value="">-- Pilih Perusahaan --</option>
              <option v-for="c in companies" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
              <input v-model="form.password" type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" :required="!isEdit" placeholder="Minimal 6 karakter">
              <p v-if="isEdit" class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
              <input v-model="form.password_confirmation" type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg" :required="!isEdit" placeholder="Ulangi password">
            </div>
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="closeModal" class="px-5 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium">Batal</button>
            <button type="submit" class="px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:shadow-lg transition-all font-medium">
              {{ isEdit ? 'Update' : 'Simpan' }}
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

const toast = useToast()
const students = ref([])
const companies = ref([])
const loading = ref(false)
const showModal = ref(false)
const isEdit = ref(false)
const search = ref('')
const filters = ref({ company: '' })

const form = ref({
  id: null,
  nisn: '',
  name: '',
  email: '',
  phone: '',
  company_id: '',
  password: '',
  password_confirmation: ''
})

const filteredStudents = computed(() => {
  let result = students.value
  
  if (search.value) {
    const searchLower = search.value.toLowerCase()
    result = result.filter(s => 
      s.nisn?.toLowerCase().includes(searchLower) ||
      s.name?.toLowerCase().includes(searchLower) ||
      s.email?.toLowerCase().includes(searchLower)
    )
  }
  
  if (filters.value.company) {
    result = result.filter(s => s.company_id === filters.value.company)
  }
  
  return result
})

const load = async () => {
  loading.value = true
  try {
    const [studentsRes, companiesRes] = await Promise.all([
      axios.get('/admin/students'),
      axios.get('/companies')
    ])
    students.value = studentsRes.data
    companies.value = companiesRes.data
  } catch (error) {
    toast.error('Gagal memuat data')
  } finally {
    loading.value = false
  }
}

const openModal = () => {
  isEdit.value = false
  form.value = {
    id: null,
    nisn: '',
    name: '',
    email: '',
    phone: '',
    company_id: '',
    password: '',
    password_confirmation: ''
  }
  showModal.value = true
}

const editStudent = (student) => {
  isEdit.value = true
  form.value = {
    id: student.id,
    nisn: student.nisn || '',
    name: student.name,
    email: student.email,
    phone: student.phone || '',
    company_id: student.company_id || '',
    password: '',
    password_confirmation: ''
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const submitStudent = async () => {
  if (form.value.password !== form.value.password_confirmation) {
    toast.error('Password dan konfirmasi password tidak sama')
    return
  }
  
  try {
    if (isEdit.value) {
      await axios.put(`/admin/students/${form.value.id}`, form.value)
      toast.success('Data siswa berhasil diupdate')
    } else {
      await axios.post('/admin/students', form.value)
      toast.success('Siswa berhasil ditambahkan')
    }
    closeModal()
    load()
  } catch (error) {
    toast.error(error.response?.data?.message || 'Gagal menyimpan data')
  }
}

const deleteStudent = async (student) => {
  if (confirm(`Hapus siswa "${student.name}"?`)) {
    try {
      await axios.delete(`/admin/students/${student.id}`)
      toast.success('Siswa berhasil dihapus')
      load()
    } catch (error) {
      toast.error('Gagal menghapus siswa')
    }
  }
}

const resetFilters = () => {
  search.value = ''
  filters.value.company = ''
}

onMounted(() => {
  load()
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