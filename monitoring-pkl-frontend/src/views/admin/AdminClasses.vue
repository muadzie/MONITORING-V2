<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Kelas</h1>
        <p class="text-gray-500 mt-1">Kelola data kelas dan wali kelas</p>
      </div>
      <button 
        @click="openModal"
        class="bg-indigo-600 text-white px-4 py-2 rounded-xl hover:bg-indigo-700 transition flex items-center space-x-2"
      >
        <PlusIcon class="w-5 h-5" />
        <span>Tambah Kelas</span>
      </button>
    </div>

    <!-- Classes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="cls in classes" :key="cls.id" class="bg-white rounded-2xl shadow-sm hover:shadow-md transition overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
          <h3 class="text-xl font-bold text-white">Kelas {{ cls.name }}</h3>
          <p class="text-white/80 text-sm">Wali Kelas: {{ cls.homeroom_teacher?.name || 'Belum ditentukan' }}</p>
        </div>
        <div class="p-6">
          <div class="flex justify-between items-center mb-4">
            <span class="text-gray-500">Jumlah Siswa</span>
            <span class="text-2xl font-bold text-indigo-600">{{ cls.students_count || 0 }}</span>
          </div>
          <div class="flex justify-between items-center mb-4">
            <span class="text-gray-500">Tahun Ajaran</span>
            <span class="text-sm">{{ cls.academic_year || '2025/2026' }}</span>
          </div>
          <div class="flex space-x-2 mt-4">
            <button @click="editClass(cls)" class="flex-1 bg-indigo-100 text-indigo-600 px-3 py-2 rounded-lg hover:bg-indigo-200 transition">
              Edit
            </button>
            <button @click="deleteClass(cls)" class="flex-1 bg-red-100 text-red-600 px-3 py-2 rounded-lg hover:bg-red-200 transition">
              Hapus
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Add/Edit Class -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b">
          <div class="flex justify-between items-center">
            <h3 class="text-xl font-bold">{{ isEdit ? 'Edit Kelas' : 'Tambah Kelas Baru' }}</h3>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
              <XMarkIcon class="w-6 h-6" />
            </button>
          </div>
        </div>
        <form @submit.prevent="submitClass" class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kelas</label>
            <input v-model="form.name" type="text" required placeholder="Contoh: XII RPL 1" class="w-full px-4 py-2 border rounded-lg">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Wali Kelas</label>
            <select v-model="form.homeroom_teacher_id" class="w-full px-4 py-2 border rounded-lg">
              <option value="">Pilih Wali Kelas</option>
              <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">
                {{ teacher.name }}
              </option>
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran</label>
            <input v-model="form.academic_year" type="text" placeholder="Contoh: 2025/2026" class="w-full px-4 py-2 border rounded-lg">
          </div>
          <div class="flex justify-end space-x-3 pt-4">
            <button type="button" @click="closeModal" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Batal</button>
            <button type="submit" :disabled="loading" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50">
              {{ loading ? 'Menyimpan...' : (isEdit ? 'Update' : 'Simpan') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'
import { PlusIcon, XMarkIcon } from '@heroicons/vue/24/outline'

const toast = useToast()
const loading = ref(false)
const showModal = ref(false)
const isEdit = ref(false)
const classes = ref([])
const teachers = ref([])

const form = ref({
  id: null,
  name: '',
  homeroom_teacher_id: '',
  academic_year: '2025/2026'
})

const loadClasses = async () => {
  try {
    const response = await axios.get('/admin/classes')
    classes.value = response.data
  } catch (error) {
    toast.error('Gagal memuat data kelas')
  }
}

const loadTeachers = async () => {
  try {
    const response = await axios.get('/admin/teachers')
    teachers.value = response.data
  } catch (error) {
    console.error('Failed to load teachers:', error)
  }
}

const openModal = () => {
  isEdit.value = false
  form.value = {
    id: null,
    name: '',
    homeroom_teacher_id: '',
    academic_year: '2025/2026'
  }
  showModal.value = true
}

const editClass = (cls) => {
  isEdit.value = true
  form.value = {
    id: cls.id,
    name: cls.name,
    homeroom_teacher_id: cls.homeroom_teacher_id || '',
    academic_year: cls.academic_year || '2025/2026'
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const submitClass = async () => {
  loading.value = true
  try {
    if (isEdit.value) {
      await axios.put(`/admin/classes/${form.value.id}`, form.value)
      toast.success('Data kelas berhasil diupdate')
    } else {
      await axios.post('/admin/classes', form.value)
      toast.success('Kelas berhasil ditambahkan')
    }
    closeModal()
    loadClasses()
  } catch (error) {
    toast.error(error.response?.data?.message || 'Gagal menyimpan data')
  } finally {
    loading.value = false
  }
}

const deleteClass = async (cls) => {
  if (confirm(`Apakah Anda yakin ingin menghapus kelas ${cls.name}?`)) {
    try {
      await axios.delete(`/admin/classes/${cls.id}`)
      toast.success('Kelas berhasil dihapus')
      loadClasses()
    } catch (error) {
      toast.error('Gagal menghapus kelas')
    }
  }
}

onMounted(() => {
  loadClasses()
  loadTeachers()
})
</script>