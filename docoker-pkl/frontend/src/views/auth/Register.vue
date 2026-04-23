<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-600 to-purple-700 py-12 px-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl overflow-hidden">
      <!-- Header -->
      <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6 text-white text-center">
        <div class="flex justify-center mb-4">
          <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
          </div>
        </div>
        <h1 class="text-2xl font-bold">Daftar Akun Baru</h1>
        <p class="mt-1 text-indigo-100">Pilih role Anda untuk mendaftar sebagai peserta PKL</p>
      </div>

      <!-- Role Selection Tabs -->
      <div class="flex border-b">
        <button 
          v-for="role in roles" 
          :key="role.value"
          @click="selectedRole = role.value"
          class="flex-1 py-4 text-center font-medium transition-all duration-300"
          :class="selectedRole === role.value 
            ? 'text-indigo-600 border-b-2 border-indigo-600 bg-indigo-50' 
            : 'text-gray-500 hover:text-gray-700'"
        >
          <component :is="role.icon" class="w-5 h-5 inline mr-2" />
          {{ role.label }}
        </button>
      </div>

      <!-- Form Content -->
      <div class="p-8">
        <!-- Siswa Form -->
        <form v-if="selectedRole === 'siswa'" @submit.prevent="submitSiswa" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
              <input v-model="siswaForm.name" type="text" required placeholder="Masukkan nama lengkap" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">NISN</label>
              <input v-model="siswaForm.nisn" type="text" required placeholder="Nomor Induk Siswa Nasional" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input v-model="siswaForm.email" type="email" required placeholder="email@example.com" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
              <input v-model="siswaForm.phone" type="tel" placeholder="08123456789" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
              <select v-model="siswaForm.kelas" class="w-full px-4 py-2 border rounded-lg">
                <option value="">Pilih Kelas</option>
                <option value="10">Kelas 10</option>
                <option value="11">Kelas 11</option>
                <option value="12">Kelas 12</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Jurusan</label>
              <select v-model="siswaForm.jurusan" class="w-full px-4 py-2 border rounded-lg">
                <option value="">Pilih Jurusan</option>
                <option value="RPL">RPL (Rekayasa Perangkat Lunak)</option>
                <option value="TKJ">TKJ (Teknik Komputer Jaringan)</option>
                <option value="MM">MM (Multimedia)</option>
                <option value="BC">BC (Broadcasting)</option>
                <option value="DKV">DKV (Desain Komunikasi Visual)</option>
              </select>
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
            <textarea v-model="siswaForm.address" rows="2" placeholder="Alamat lengkap" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
              <input v-model="siswaForm.password" type="password" required placeholder="Minimal 6 karakter" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
              <input v-model="siswaForm.password_confirmation" type="password" required placeholder="Ulangi password" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
          </div>
          <button type="submit" :disabled="loading" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-lg hover:shadow-lg transition-all duration-300 hover:scale-105 disabled:opacity-50 font-semibold">
            {{ loading ? 'Memproses...' : 'Daftar sebagai Siswa' }}
          </button>
        </form>

        <!-- Guru Form -->
        <form v-if="selectedRole === 'guru'" @submit.prevent="submitGuru" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
              <input v-model="guruForm.name" type="text" required placeholder="Masukkan nama lengkap" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
              <input v-model="guruForm.nip" type="text" required placeholder="Nomor Induk Pegawai" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input v-model="guruForm.email" type="email" required placeholder="email@example.com" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
              <input v-model="guruForm.phone" type="tel" placeholder="08123456789" class="w-full px-4 py-2 border rounded-lg">
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Mata Pelajaran</label>
            <input v-model="guruForm.mata_pelajaran" type="text" placeholder="Contoh: Pemrograman Web" class="w-full px-4 py-2 border rounded-lg">
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
              <input v-model="guruForm.password" type="password" required placeholder="Minimal 6 karakter" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
              <input v-model="guruForm.password_confirmation" type="password" required placeholder="Ulangi password" class="w-full px-4 py-2 border rounded-lg">
            </div>
          </div>
          <button type="submit" :disabled="loading" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-lg hover:shadow-lg transition-all duration-300 hover:scale-105 disabled:opacity-50 font-semibold">
            {{ loading ? 'Memproses...' : 'Daftar sebagai Guru' }}
          </button>
        </form>

        <!-- Perusahaan Form -->
        <form v-if="selectedRole === 'perusahaan'" @submit.prevent="submitPerusahaan" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan</label>
              <input v-model="perusahaanForm.nama_perusahaan" type="text" required placeholder="Nama perusahaan" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Bidang Usaha</label>
              <input v-model="perusahaanForm.bidang_usaha" type="text" required placeholder="Bidang usaha perusahaan" class="w-full px-4 py-2 border rounded-lg">
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email Perusahaan</label>
              <input v-model="perusahaanForm.email" type="email" required placeholder="email@perusahaan.com" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
              <input v-model="perusahaanForm.telepon" type="tel" required placeholder="Nomor telepon perusahaan" class="w-full px-4 py-2 border rounded-lg">
            </div>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
            <textarea v-model="perusahaanForm.alamat" rows="2" required placeholder="Alamat lengkap perusahaan" class="w-full px-4 py-2 border rounded-lg"></textarea>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Kontak Person</label>
              <input v-model="perusahaanForm.kontak_person" type="text" required placeholder="Nama kontak person" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan Kontak Person</label>
              <input v-model="perusahaanForm.jabatan" type="text" placeholder="Jabatan kontak person" class="w-full px-4 py-2 border rounded-lg">
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pengguna</label>
              <input v-model="perusahaanForm.name" type="text" required placeholder="Nama untuk login" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon Pengguna</label>
              <input v-model="perusahaanForm.phone" type="tel" placeholder="Nomor telepon pengguna" class="w-full px-4 py-2 border rounded-lg">
            </div>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
              <input v-model="perusahaanForm.password" type="password" required placeholder="Minimal 6 karakter" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
              <input v-model="perusahaanForm.password_confirmation" type="password" required placeholder="Ulangi password" class="w-full px-4 py-2 border rounded-lg">
            </div>
          </div>
          <button type="submit" :disabled="loading" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-lg hover:shadow-lg transition-all duration-300 hover:scale-105 disabled:opacity-50 font-semibold">
            {{ loading ? 'Memproses...' : 'Daftar sebagai Perusahaan' }}
          </button>
        </form>

        <!-- Message Alert -->
        <div v-if="message" class="mt-4 p-4 rounded-lg" :class="messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
          {{ message }}
        </div>

        <!-- Login Link -->
        <div class="mt-6 text-center text-sm text-gray-500">
          Sudah punya akun? 
          <router-link to="/login" class="text-indigo-600 hover:underline font-semibold">Login di sini</router-link>
        </div>

        <!-- Back to Landing -->
        <div class="mt-4 text-center">
          <router-link to="/" class="text-sm text-gray-400 hover:text-gray-600 transition">
            ← Kembali ke Beranda
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import axios from '../../plugins/axios'
import { UserIcon, AcademicCapIcon, BuildingOffice2Icon } from '@heroicons/vue/24/outline'

const router = useRouter()
const loading = ref(false)
const message = ref('')
const messageType = ref('success')
const selectedRole = ref('siswa')

const roles = [
  { value: 'siswa', label: 'Siswa', icon: AcademicCapIcon },
  { value: 'guru', label: 'Guru', icon: UserIcon },
  { value: 'perusahaan', label: 'Perusahaan', icon: BuildingOffice2Icon }
]

// Siswa Form
const siswaForm = ref({
  name: '', nisn: '', email: '', phone: '', kelas: '', jurusan: '', address: '', password: '', password_confirmation: ''
})

// Guru Form
const guruForm = ref({
  name: '', nip: '', email: '', phone: '', mata_pelajaran: '', password: '', password_confirmation: ''
})

// Perusahaan Form
const perusahaanForm = ref({
  name: '', email: '', phone: '', password: '', password_confirmation: '',
  nama_perusahaan: '', alamat: '', telepon: '', bidang_usaha: '', kontak_person: '', jabatan: ''
})

const submitSiswa = async () => {
  loading.value = true
  message.value = ''
  try {
    await axios.post('/register/siswa', siswaForm.value)
    messageType.value = 'success'
    message.value = 'Pendaftaran berhasil! Silakan tunggu konfirmasi dari admin.'
    setTimeout(() => router.push('/login'), 3000)
  } catch (error) {
    messageType.value = 'error'
    message.value = error.response?.data?.message || 'Pendaftaran gagal'
  } finally {
    loading.value = false
  }
}

const submitGuru = async () => {
  loading.value = true
  message.value = ''
  try {
    await axios.post('/register/guru', guruForm.value)
    messageType.value = 'success'
    message.value = 'Pendaftaran berhasil! Silakan tunggu konfirmasi dari admin.'
    setTimeout(() => router.push('/login'), 3000)
  } catch (error) {
    messageType.value = 'error'
    message.value = error.response?.data?.message || 'Pendaftaran gagal'
  } finally {
    loading.value = false
  }
}

const submitPerusahaan = async () => {
  loading.value = true
  message.value = ''
  try {
    await axios.post('/register/perusahaan', perusahaanForm.value)
    messageType.value = 'success'
    message.value = 'Pendaftaran berhasil! Silakan tunggu konfirmasi dari admin.'
    setTimeout(() => router.push('/login'), 3000)
  } catch (error) {
    messageType.value = 'error'
    message.value = error.response?.data?.message || 'Pendaftaran gagal'
  } finally {
    loading.value = false
  }
}
</script>