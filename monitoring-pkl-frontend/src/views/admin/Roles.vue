<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Role</h1>
        <p class="text-gray-500 mt-1">Kelola hak akses pengguna sistem</p>
      </div>
      <button @click="openModal" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
        + Tambah Role
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="bg-red-100 text-red-700 p-4 rounded-lg">
      {{ error }}
      <button @click="fetchRoles" class="ml-4 underline">Coba lagi</button>
    </div>

    <!-- Grid Roles -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="role in roles" :key="role.id" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-4">
          <div :class="getRoleColor(role.name)" class="w-12 h-12 rounded-xl flex items-center justify-center">
            <span class="text-2xl">{{ getRoleIcon(role.name) }}</span>
          </div>
          <div class="flex gap-2">
            <button @click="editRole(role)" class="text-indigo-600 hover:text-indigo-800">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
              </svg>
            </button>
            <button @click="deleteRole(role)" class="text-red-600 hover:text-red-800">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
              </svg>
            </button>
          </div>
        </div>
        <h3 class="text-lg font-bold text-gray-800">{{ role.name }}</h3>
        <p class="text-sm text-gray-500 mt-1">{{ role.description || 'Tidak ada deskripsi' }}</p>
        <div class="mt-4 pt-3 border-t">
          <p class="text-xs text-gray-400">Jumlah user: {{ role.users_count || 0 }}</p>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="closeModal">
      <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">{{ isEdit ? 'Edit Role' : 'Tambah Role' }}</h2>
        <form @submit.prevent="saveRole">
          <div class="mb-3">
            <label class="block text-sm font-medium mb-1">Nama Role</label>
            <input v-model="form.name" type="text" class="w-full p-2 border rounded-lg" required>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Deskripsi</label>
            <textarea v-model="form.description" rows="3" class="w-full p-2 border rounded-lg"></textarea>
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
const roles = ref([])
const loading = ref(true)
const error = ref('')
const showModal = ref(false)
const isEdit = ref(false)
const form = ref({ id: null, name: '', description: '' })

const getRoleColor = (name) => {
  const colors = {
    Admin: 'bg-red-100 text-red-600',
    Guru: 'bg-blue-100 text-blue-600',
    Siswa: 'bg-green-100 text-green-600',
    Perusahaan: 'bg-purple-100 text-purple-600'
  }
  return colors[name] || 'bg-gray-100 text-gray-600'
}

const getRoleIcon = (name) => {
  const icons = { Admin: '👑', Guru: '👨‍🏫', Siswa: '🎓', Perusahaan: '🏢' }
  return icons[name] || '👤'
}

const fetchRoles = async () => {
  loading.value = true
  error.value = ''
  try {
    const res = await axios.get('/admin/roles')
    roles.value = res.data
  } catch (err) {
    error.value = err.response?.data?.message || 'Gagal memuat data role'
    toast.error(error.value)
  } finally {
    loading.value = false
  }
}

const openModal = () => {
  isEdit.value = false
  form.value = { id: null, name: '', description: '' }
  showModal.value = true
}

const editRole = (role) => {
  isEdit.value = true
  form.value = { id: role.id, name: role.name, description: role.description || '' }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const saveRole = async () => {
  try {
    if (isEdit.value) {
      await axios.put(`/admin/roles/${form.value.id}`, form.value)
      toast.success('Role berhasil diupdate')
    } else {
      await axios.post('/admin/roles', form.value)
      toast.success('Role berhasil ditambahkan')
    }
    closeModal()
    fetchRoles()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal menyimpan role')
  }
}

const deleteRole = async (role) => {
  if (confirm(`Hapus role "${role.name}"?`)) {
    try {
      await axios.delete(`/admin/roles/${role.id}`)
      toast.success('Role berhasil dihapus')
      fetchRoles()
    } catch (err) {
      toast.error(err.response?.data?.message || 'Gagal menghapus role')
    }
  }
}

onMounted(() => {
  fetchRoles()
})
</script>