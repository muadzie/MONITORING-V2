<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-indigo-600 to-purple-700 py-12 px-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
      <!-- Header -->
      <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-8 py-6 text-white text-center">
        <div class="flex justify-center mb-4">
          <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
          </div>
        </div>
        <h1 class="text-2xl font-bold">Selamat Datang</h1>
        <p class="mt-1 text-indigo-100">Silakan masuk ke akun Anda</p>
      </div>

      <!-- Form Content -->
      <div class="p-8">
        <form @submit.prevent="handleLogin" class="space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input 
              v-model="form.email" 
              type="email" 
              required 
              placeholder="admin@smkn1subang.sch.id"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <div class="relative">
              <input 
                :type="showPassword ? 'text' : 'password'"
                v-model="form.password" 
                required 
                placeholder="••••••••"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent pr-10"
              >
              <button 
                type="button"
                @click="showPassword = !showPassword"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
              >
                <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                </svg>
              </button>
            </div>
          </div>

          <div class="flex items-center justify-between">
            <label class="flex items-center">
              <input type="checkbox" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
              <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
            </label>
            <a href="#" class="text-sm text-indigo-600 hover:underline">Lupa password?</a>
          </div>

          <div v-if="error" class="bg-red-100 border border-red-400 text-red-700 p-3 rounded-lg text-sm">
            {{ error }}
          </div>

          <button 
            type="submit" 
            :disabled="loading"
            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-2.5 rounded-lg hover:shadow-lg transition-all duration-300 hover:scale-105 disabled:opacity-50 font-semibold flex items-center justify-center gap-2"
          >
            <svg v-if="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ loading ? 'Memproses...' : 'Masuk' }}
          </button>
        </form>

        <!-- Demo Credentials -->
        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
          <p class="text-xs text-gray-500 text-center mb-2">Akun Demo</p>
          <div class="grid grid-cols-2 gap-2 text-xs">
            <div class="text-gray-500">Admin:</div>
            <div class="text-gray-700 font-mono">admin@smkn1subang.sch.id</div>
            <div class="text-gray-500">Guru:</div>
            <div class="text-gray-700 font-mono">guru@smkn1subang.sch.id</div>
            <div class="text-gray-500">Siswa:</div>
            <div class="text-gray-700 font-mono">ahmadfauzi@student.com</div>
            <div class="text-gray-500">Perusahaan:</div>
            <div class="text-gray-700 font-mono">perusahaan1@example.com</div>
          </div>
          <p class="text-center text-xs text-gray-500 mt-2">Password: <span class="font-mono">password</span></p>
        </div>

        <!-- Tombol Daftar dan Kembali ke Beranda -->
        <div class="mt-6 space-y-3">
          <router-link 
            to="/register" 
            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 border border-indigo-600 text-indigo-600 rounded-lg hover:bg-indigo-50 transition-all duration-300 font-medium"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Daftar Akun Baru
          </router-link>
          
          <router-link 
            to="/" 
            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-gray-500 hover:text-gray-700 transition-all duration-300"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Beranda
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../../stores/auth'

const router = useRouter()
const authStore = useAuthStore()
const form = ref({ email: '', password: '' })
const loading = ref(false)
const error = ref('')
const showPassword = ref(false)

const handleLogin = async () => {
  loading.value = true
  error.value = ''
  
  try {
    await authStore.login(form.value)
    const role = authStore.role?.toLowerCase()
    router.push(`/${role}/dashboard`)
  } catch (err) {
    error.value = err.response?.data?.message || 'Email atau password salah'
    setTimeout(() => { error.value = '' }, 3000)
  } finally {
    loading.value = false
  }
}
</script>