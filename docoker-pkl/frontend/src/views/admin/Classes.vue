<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Kelas</h1>
        <p class="text-gray-500 mt-1">Kelola data kelas dan wali kelas</p>
      </div>
      <button 
        @click="openModal" 
        class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2.5 rounded-xl hover:shadow-lg transition-all duration-300 hover:scale-105 flex items-center gap-2"
      >
        <PlusIcon class="w-5 h-5" />
        Tambah Kelas
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Total Kelas</p>
        <p class="text-2xl font-bold">{{ classes.length }}</p>
      </div>
      <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Total Siswa</p>
        <p class="text-2xl font-bold">{{ totalStudents }}</p>
      </div>
      <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Rata-rata per Kelas</p>
        <p class="text-2xl font-bold">{{ averagePerClass }}</p>
      </div>
      <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Tahun Ajaran</p>
        <p class="text-2xl font-bold">{{ currentAcademicYear }}</p>
      </div>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-xl shadow-sm p-4">
      <div class="relative">
        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <input 
          v-model="search" 
          type="text" 
          placeholder="Cari kelas (Nama, Wali Kelas, Tahun Ajaran)..."
          class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
        >
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
        <p class="mt-4 text-gray-500">Memuat data kelas...</p>
      </div>
    </div>

    <!-- Classes Grid -->
    <div v-else-if="filteredClasses.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="cls in filteredClasses" :key="cls.id" class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-xl transition-all duration-300 group">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
          <div class="flex justify-between items-start">
            <div>
              <h3 class="text-xl font-bold text-white">Kelas {{ cls.name }}</h3>
              <p class="text-indigo-100 text-sm mt-1">
                Wali Kelas: {{ cls.teacher?.name || 'Belum ditentukan' }}
              </p>
            </div>
            <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-lg">{{ cls.name?.charAt(0) || 'K' }}</span>
            </div>
          </div>
        </div>
        <div class="p-6">
          <div class="flex justify-between items-center mb-3">
            <span class="text-gray-500">Jumlah Siswa</span>
            <span class="text-2xl font-bold text-indigo-600">{{ cls.students_count || 0 }}</span>
          </div>
          <div class="flex justify-between items-center mb-4">
            <span class="text-gray-500">Tahun Ajaran</span>
            <span class="px-2 py-1 bg-gray-100 rounded-lg text-sm">{{ cls.academic_year || '2024/2025' }}</span>
          </div>
          <div class="flex gap-2">
            <button @click="editClass(cls)" class="flex-1 bg-indigo-100 text-indigo-600 py-2 rounded-lg hover:bg-indigo-200 transition flex items-center justify-center gap-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
              </svg>
              Edit
            </button>
            <button @click="deleteClass(cls)" class="flex-1 bg-red-100 text-red-600 py-2 rounded-lg hover:bg-red-200 transition flex items-center justify-center gap-1">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
              Hapus
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!loading && filteredClasses.length === 0" class="bg-white rounded-xl p-12 text-center">
      <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21v-4H7v4m10 0h2m-2 0h-2m-6 0H5m2 0h2m4-4V9a1 1 0 00-1-1h-2a1 1 0 00-1 1v8m4 0h-4"></path>
      </svg>
      <p class="text-gray-500">Belum ada data kelas</p>
      <button @click="openModal" class="mt-3 text-indigo-600 hover:underline">Tambah kelas pertama</button>
    </div>

    <!-- Modal Tambah/Edit Kelas -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50" @click.self="closeModal">
      <div class="bg-white rounded-2xl w-full max-w-md animate-fade-in-up">
        <div class="sticky top-0 bg-white p-5 border-b rounded-t-2xl">
          <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21v-4H7v4m10 0h2m-2 0h-2m-6 0H5m2 0h2m4-4V9a1 1 0 00-1-1h-2a1 1 0 00-1 1v8m4 0h-4"></path>
                </svg>
              </div>
              <div>
                <h2 class="text-xl font-bold text-gray-800">{{ isEdit ? 'Edit Kelas' : 'Tambah Kelas' }}</h2>
                <p class="text-sm text-gray-500">{{ isEdit ? 'Ubah data kelas' : 'Masukkan data kelas baru' }}</p>
              </div>
            </div>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition">
              <XMarkIcon class="w-6 h-6" />
            </button>
          </div>
        </div>

        <form @submit.prevent="saveClass" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas <span class="text-red-500">*</span></label>
            <input 
              v-model="form.name" 
              type="text" 
              placeholder="Contoh: XII RPL 1" 
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" 
              required
            >
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Wali Kelas</label>
            <select 
              v-model="form.teacher_id" 
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
              <option value="">-- Pilih Wali Kelas --</option>
              <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
                {{ teacher.name }} {{ teacher.mata_pelajaran ? `(${teacher.mata_pelajaran})` : '' }}
              </option>
            </select>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran</label>
            <input 
              v-model="form.academic_year" 
              type="text" 
              placeholder="Contoh: 2024/2025" 
              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="closeModal" class="px-5 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium">
              Batal
            </button>
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:shadow-lg transition-all font-medium">
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
import { PlusIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const toast = useToast()
const classes = ref([])
const teachers = ref([])
const loading = ref(false)
const showModal = ref(false)
const isEdit = ref(false)
const search = ref('')

const form = ref({
  id: null,
  name: '',
  teacher_id: '',
  academic_year: new Date().getFullYear() + '/' + (new Date().getFullYear() + 1)
})

// Computed
const totalStudents = computed(() => {
  return classes.value.reduce((sum, cls) => sum + (cls.students_count || 0), 0)
})

const averagePerClass = computed(() => {
  if (!classes.value.length) return 0
  return Math.round(totalStudents.value / classes.value.length)
})

const currentAcademicYear = computed(() => {
  return new Date().getFullYear() + '/' + (new Date().getFullYear() + 1)
})

const filteredClasses = computed(() => {
  if (!search.value) return classes.value
  const searchLower = search.value.toLowerCase()
  return classes.value.filter(cls => 
    cls.name?.toLowerCase().includes(searchLower) ||
    cls.teacher?.name?.toLowerCase().includes(searchLower) ||
    cls.academic_year?.toLowerCase().includes(searchLower)
  )
})

// Fetch data
const fetchClasses = async () => {
  loading.value = true
  try {
    const res = await axios.get('/admin/classes')
    classes.value = res.data
  } catch (error) {
    console.error('Fetch classes error:', error)
    toast.error('Gagal memuat data kelas')
  } finally {
    loading.value = false
  }
}

const fetchTeachers = async () => {
  try {
    const res = await axios.get('/admin/teachers')
    teachers.value = res.data
  } catch (error) {
    console.error('Fetch teachers error:', error)
  }
}

// CRUD operations
const openModal = () => {
  isEdit.value = false
  form.value = {
    id: null,
    name: '',
    teacher_id: '',
    academic_year: new Date().getFullYear() + '/' + (new Date().getFullYear() + 1)
  }
  showModal.value = true
}

const editClass = (cls) => {
  isEdit.value = true
  form.value = {
    id: cls.id,
    name: cls.name,
    teacher_id: cls.teacher_id || '',
    academic_year: cls.academic_year || (new Date().getFullYear() + '/' + (new Date().getFullYear() + 1))
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const saveClass = async () => {
  try {
    if (isEdit.value) {
      await axios.put(`/admin/classes/${form.value.id}`, form.value)
      toast.success('Kelas berhasil diupdate')
    } else {
      await axios.post('/admin/classes', form.value)
      toast.success('Kelas berhasil ditambahkan')
    }
    closeModal()
    fetchClasses()
  } catch (error) {
    console.error('Save class error:', error)
    toast.error(error.response?.data?.message || 'Gagal menyimpan kelas')
  }
}

const deleteClass = async (cls) => {
  if (confirm(`Hapus kelas ${cls.name}?`)) {
    try {
      await axios.delete(`/admin/classes/${cls.id}`)
      toast.success('Kelas berhasil dihapus')
      fetchClasses()
    } catch (error) {
      console.error('Delete class error:', error)
      toast.error('Gagal menghapus kelas')
    }
  }
}

onMounted(() => {
  fetchClasses()
  fetchTeachers()
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