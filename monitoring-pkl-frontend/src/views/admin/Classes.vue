<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Kelas</h1>
        <p class="text-gray-500 mt-1">Kelola data kelas dan wali kelas</p>
      </div>
      <button @click="openModal" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
        + Tambah Kelas
      </button>
    </div>

    <!-- Classes Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="cls in classes" :key="cls.id" class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
          <h3 class="text-xl font-bold text-white">Kelas {{ cls.name }}</h3>
          <p class="text-indigo-100 text-sm">Wali Kelas: {{ cls.teacher?.name || 'Belum ditentukan' }}</p>
        </div>
        <div class="p-6">
          <div class="flex justify-between items-center mb-3">
            <span class="text-gray-500">Jumlah Siswa</span>
            <span class="text-2xl font-bold text-indigo-600">{{ cls.students_count || 0 }}</span>
          </div>
          <div class="flex justify-between items-center mb-4">
            <span class="text-gray-500">Tahun Ajaran</span>
            <span class="text-sm">{{ cls.academic_year || '2024/2025' }}</span>
          </div>
          <div class="flex gap-2">
            <button @click="editClass(cls)" class="flex-1 bg-indigo-100 text-indigo-600 py-2 rounded-lg hover:bg-indigo-200">Edit</button>
            <button @click="deleteClass(cls)" class="flex-1 bg-red-100 text-red-600 py-2 rounded-lg hover:bg-red-200">Hapus</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl w-full max-w-md p-6">
        <h2 class="text-xl font-bold mb-4">{{ isEdit ? 'Edit Kelas' : 'Tambah Kelas' }}</h2>
        <form @submit.prevent="saveClass">
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Nama Kelas</label>
            <input v-model="form.name" type="text" placeholder="Contoh: XII RPL 1" class="w-full px-3 py-2 border rounded-lg" required>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Wali Kelas</label>
            <select v-model="form.teacher_id" class="w-full px-3 py-2 border rounded-lg">
              <option value="">Pilih Wali Kelas</option>
              <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">{{ teacher.name }}</option>
            </select>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Tahun Ajaran</label>
            <input v-model="form.academic_year" type="text" placeholder="Contoh: 2024/2025" class="w-full px-3 py-2 border rounded-lg">
          </div>
          <div class="flex justify-end gap-2">
            <button type="button" @click="closeModal" class="px-4 py-2 border rounded-lg">Batal</button>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Simpan</button>
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

const toast = useToast()
const classes = ref([])
const teachers = ref([])
const showModal = ref(false)
const isEdit = ref(false)
const form = ref({ id: null, name: '', teacher_id: '', academic_year: '2024/2025' })

const fetchClasses = async () => {
  try { const res = await axios.get('/classes'); classes.value = res.data } catch(e) { console.error(e) }
}

const fetchTeachers = async () => {
  try { const res = await axios.get('/teachers'); teachers.value = res.data } catch(e) { console.error(e) }
}

const openModal = () => {
  isEdit.value = false
  form.value = { id: null, name: '', teacher_id: '', academic_year: '2024/2025' }
  showModal.value = true
}

const editClass = (cls) => {
  isEdit.value = true
  form.value = { id: cls.id, name: cls.name, teacher_id: cls.teacher_id, academic_year: cls.academic_year }
  showModal.value = true
}

const closeModal = () => { showModal.value = false }

const saveClass = async () => {
  try {
    if (isEdit.value) await axios.put(`/classes/${form.value.id}`, form.value)
    else await axios.post('/classes', form.value)
    toast.success('Kelas berhasil disimpan')
    closeModal()
    fetchClasses()
  } catch (error) { toast.error('Gagal menyimpan kelas') }
}

const deleteClass = async (cls) => {
  if (confirm(`Hapus kelas ${cls.name}?`)) {
    try { await axios.delete(`/classes/${cls.id}`); toast.success('Kelas dihapus'); fetchClasses() }
    catch(e) { toast.error('Gagal menghapus') }
  }
}

onMounted(() => { fetchClasses(); fetchTeachers() })
</script>