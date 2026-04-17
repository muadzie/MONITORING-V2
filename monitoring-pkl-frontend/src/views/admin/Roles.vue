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

    <!-- Roles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="role in roles" :key="role.id" class="bg-white rounded-xl shadow-sm p-6 hover:shadow-md transition">
        <div class="flex items-center justify-between mb-4">
          <div :class="getRoleColor(role.name)" class="w-12 h-12 rounded-xl flex items-center justify-center">
            <span class="text-2xl">{{ getRoleIcon(role.name) }}</span>
          </div>
          <div class="flex gap-2">
            <button @click="editRole(role)" class="text-indigo-600 hover:text-indigo-800">
              <PencilSquareIcon class="w-5 h-5" />
            </button>
            <button @click="deleteRole(role)" class="text-red-600 hover:text-red-800">
              <TrashIcon class="w-5 h-5" />
            </button>
          </div>
        </div>
        <h3 class="text-lg font-bold text-gray-800">{{ role.name }}</h3>
        <p class="text-sm text-gray-500 mt-1">{{ role.description || 'Tidak ada deskripsi' }}</p>
        <div class="mt-4 pt-4 border-t">
          <p class="text-xs text-gray-400">Jumlah user: {{ role.users_count || 0 }}</p>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl w-full max-w-md p-6">
        <h2 class="text-xl font-bold mb-4">{{ isEdit ? 'Edit Role' : 'Tambah Role' }}</h2>
        <form @submit.prevent="saveRole">
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Nama Role</label>
            <input v-model="form.name" type="text" class="w-full px-3 py-2 border rounded-lg" required>
          </div>
          <div class="mb-4">
            <label class="block text-sm font-medium mb-1">Deskripsi</label>
            <textarea v-model="form.description" rows="3" class="w-full px-3 py-2 border rounded-lg"></textarea>
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
import { PencilSquareIcon, TrashIcon } from '@heroicons/vue/24/outline'

const toast = useToast()
const roles = ref([])
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
  try {
    const res = await axios.get('/roles')
    roles.value = res.data
  } catch (error) {
    console.error(error)
  }
}

const openModal = () => {
  isEdit.value = false
  form.value = { id: null, name: '', description: '' }
  showModal.value = true
}

const editRole = (role) => {
  isEdit.value = true
  form.value = { id: role.id, name: role.name, description: role.description }
  showModal.value = true
}

const closeModal = () => { showModal.value = false }

const saveRole = async () => {
  try {
    if (isEdit.value) {
      await axios.put(`/roles/${form.value.id}`, form.value)
      toast.success('Role berhasil diupdate')
    } else {
      await axios.post('/roles', form.value)
      toast.success('Role berhasil ditambahkan')
    }
    closeModal()
    fetchRoles()
  } catch (error) {
    toast.error('Gagal menyimpan role')
  }
}

const deleteRole = async (role) => {
  if (confirm(`Hapus role "${role.name}"?`)) {
    try {
      await axios.delete(`/roles/${role.id}`)
      toast.success('Role berhasil dihapus')
      fetchRoles()
    } catch (error) {
      toast.error('Gagal menghapus role')
    }
  }
}

onMounted(() => fetchRoles())
</script>