<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Manajemen User</h1>
        <p class="text-gray-500 mt-1">Kelola semua pengguna sistem</p>
      </div>
      <button @click="openModal" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
        + Tambah User
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600"></div>
    </div>

    <!-- Error -->
    <div v-else-if="error" class="bg-red-100 text-red-700 p-4 rounded-lg">
      {{ error }}
      <button @click="fetchUsers" class="ml-4 underline">Coba lagi</button>
    </div>

    <!-- Table -->
    <div v-else class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold">Nama</th>
              <th class="px-6 py-3 text-left text-xs font-semibold">Email</th>
              <th class="px-6 py-3 text-left text-xs font-semibold">Role</th>
              <th class="px-6 py-3 text-left text-xs font-semibold">Tanggal</th>
              <th class="px-6 py-3 text-left text-xs font-semibold">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50">
              <td class="px-6 py-4">{{ user.name }}</td>
              <td class="px-6 py-4">{{ user.email }}</td>
              <td class="px-6 py-4">
                <span :class="getRoleClass(user.role)" class="px-2 py-1 rounded-full text-xs">
                  {{ user.role }}
                </span>
              </td>
              <td class="px-6 py-4">{{ formatDate(user.created_at) }}</td>
              <td class="px-6 py-4">
                <button @click="editUser(user)" class="text-indigo-600 mr-2">Edit</button>
                <button @click="deleteUser(user)" class="text-red-600">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div class="px-6 py-4 border-t flex justify-between items-center">
        <span class="text-sm text-gray-500">Total: {{ pagination.total }} user</span>
        <div class="flex gap-2">
          <button 
            @click="prevPage" 
            :disabled="pagination.current_page <= 1"
            class="px-3 py-1 border rounded disabled:opacity-50"
          >Prev</button>
          <span class="px-3 py-1">Halaman {{ pagination.current_page }} dari {{ pagination.last_page }}</span>
          <button 
            @click="nextPage" 
            :disabled="pagination.current_page >= pagination.last_page"
            class="px-3 py-1 border rounded disabled:opacity-50"
          >Next</button>
        </div>
      </div>
    </div>

    <!-- Modal (sama seperti sebelumnya) -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-xl p-6 w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">{{ isEdit ? 'Edit User' : 'Tambah User' }}</h2>
        <form @submit.prevent="saveUser">
          <input v-model="form.name" placeholder="Nama" class="w-full p-2 border rounded mb-2" required>
          <input v-model="form.email" type="email" placeholder="Email" class="w-full p-2 border rounded mb-2" required>
          <select v-model="form.role_id" class="w-full p-2 border rounded mb-2" required>
            <option value="">Pilih Role</option>
            <option :value="1">Admin</option>
            <option :value="2">Siswa</option>
            <option :value="3">Guru</option>
            <option :value="4">Perusahaan</option>
          </select>
          <input v-model="form.password" type="password" placeholder="Password" class="w-full p-2 border rounded mb-4" :required="!isEdit">
          <div class="flex justify-end gap-2">
            <button type="button" @click="closeModal" class="px-4 py-2 border rounded">Batal</button>
            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Simpan</button>
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
const users = ref([])
const loading = ref(true)
const error = ref('')
const showModal = ref(false)
const isEdit = ref(false)
const currentPage = ref(1)
const pagination = ref({
  current_page: 1,
  last_page: 1,
  total: 0
})

const form = ref({ id: null, name: '', email: '', role_id: '', password: '' })

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID')
}

const getRoleClass = (role) => {
  const classes = {
    Admin: 'bg-red-100 text-red-800',
    Guru: 'bg-blue-100 text-blue-800',
    Siswa: 'bg-green-100 text-green-800',
    Perusahaan: 'bg-purple-100 text-purple-800'
  }
  return classes[role] || 'bg-gray-100 text-gray-800'
}

const fetchUsers = async () => {
  loading.value = true
  error.value = ''
  try {
    const res = await axios.get(`/admin/users?page=${currentPage.value}`)
    users.value = res.data.data
    pagination.value = {
      current_page: res.data.current_page,
      last_page: res.data.last_page,
      total: res.data.total
    }
  } catch (err) {
    error.value = err.response?.data?.message || 'Gagal memuat data'
    toast.error(error.value)
  } finally {
    loading.value = false
  }
}

const prevPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--
    fetchUsers()
  }
}

const nextPage = () => {
  if (currentPage.value < pagination.value.last_page) {
    currentPage.value++
    fetchUsers()
  }
}

const openModal = () => {
  isEdit.value = false
  form.value = { id: null, name: '', email: '', role_id: '', password: '' }
  showModal.value = true
}

const editUser = (user) => {
  isEdit.value = true
  form.value = {
    id: user.id,
    name: user.name,
    email: user.email,
    role_id: user.role_id,
    password: ''
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const saveUser = async () => {
  try {
    if (isEdit.value) {
      await axios.put(`/admin/users/${form.value.id}`, form.value)
      toast.success('User berhasil diupdate')
    } else {
      await axios.post('/admin/users', form.value)
      toast.success('User berhasil ditambahkan')
    }
    closeModal()
    fetchUsers()
  } catch (err) {
    toast.error(err.response?.data?.message || 'Gagal menyimpan')
  }
}

const deleteUser = async (user) => {
  if (confirm(`Hapus user "${user.name}"?`)) {
    try {
      await axios.delete(`/admin/users/${user.id}`)
      toast.success('User berhasil dihapus')
      fetchUsers()
    } catch (err) {
      toast.error(err.response?.data?.message || 'Gagal menghapus')
    }
  }
}

onMounted(() => {
  fetchUsers()
})
</script>