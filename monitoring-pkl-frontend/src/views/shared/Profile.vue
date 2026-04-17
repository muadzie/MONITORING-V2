<template>
  <div class="max-w-4xl mx-auto space-y-6">
    <div>
      <h1 class="text-2xl font-bold text-gray-800">Profil Saya</h1>
      <p class="text-gray-500 mt-1">Kelola informasi akun Anda</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
      <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-8">
        <div class="flex items-center gap-4">
          <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center text-3xl font-bold text-indigo-600">
            {{ authStore.user?.name?.charAt(0) || 'U' }}
          </div>
          <div class="text-white">
            <h2 class="text-xl font-bold">{{ authStore.user?.name }}</h2>
            <p class="text-white/80">{{ authStore.role }}</p>
            <p class="text-sm text-white/70 mt-1">{{ authStore.user?.email }}</p>
          </div>
        </div>
      </div>

      <div class="p-6 space-y-6">
        <form @submit.prevent="updateProfile" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
              <input v-model="profile.name" type="text" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input v-model="profile.email" type="email" class="w-full px-4 py-2 border rounded-lg">
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
            <input v-model="profile.phone" type="tel" class="w-full px-4 py-2 border rounded-lg">
          </div>
          
          <div class="border-t pt-4">
            <h3 class="font-semibold text-gray-800 mb-4">Ganti Password</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input v-model="password.new" type="password" class="w-full px-4 py-2 border rounded-lg" placeholder="Kosongkan jika tidak ingin mengganti">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input v-model="password.confirm" type="password" class="w-full px-4 py-2 border rounded-lg" placeholder="Konfirmasi password baru">
              </div>
            </div>
          </div>
          
          <div class="flex justify-end">
            <button type="submit" :disabled="loading" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
              {{ loading ? 'Menyimpan...' : 'Simpan Perubahan' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import axios from '../plugins/axios'
import { useToast } from 'vue-toastification'

const authStore = useAuthStore()
const toast = useToast()
const loading = ref(false)

const profile = ref({
  name: '',
  email: '',
  phone: ''
})

const password = ref({
  new: '',
  confirm: ''
})

const loadProfile = () => {
  profile.value.name = authStore.user?.name || ''
  profile.value.email = authStore.user?.email || ''
  profile.value.phone = authStore.user?.phone || ''
}

const updateProfile = async () => {
  loading.value = true
  try {
    const data = { name: profile.value.name, email: profile.value.email, phone: profile.value.phone }
    if (password.value.new) {
      if (password.value.new !== password.value.confirm) {
        toast.error('Konfirmasi password tidak sesuai')
        return
      }
      data.password = password.value.new
    }
    await axios.put('/profile', data)
    toast.success('Profil berhasil diperbarui')
    authStore.user.name = profile.value.name
    authStore.user.email = profile.value.email
    authStore.user.phone = profile.value.phone
    password.value.new = ''
    password.value.confirm = ''
  } catch (error) {
    toast.error(error.response?.data?.message || 'Gagal memperbarui profil')
  } finally {
    loading.value = false
  }
}

onMounted(() => loadProfile())
</script>