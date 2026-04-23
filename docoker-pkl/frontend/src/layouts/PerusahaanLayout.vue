<template>
  <div class="min-h-screen bg-gray-50">
    <aside class="fixed left-0 top-0 z-40 h-screen w-72 bg-white shadow-2xl">
      <div class="flex items-center gap-3 px-6 py-5 border-b">
        <div class="w-10 h-10 bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
        </div>
        <div><h1 class="text-xl font-bold text-gray-800">Perusahaan Panel</h1><p class="text-xs text-gray-500">{{ authStore.user?.company?.name }}</p></div>
      </div>
      <div class="p-4 m-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl">
        <div class="flex items-center gap-3"><div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center text-white text-lg font-bold">{{ authStore.user?.name?.charAt(0) || 'P' }}</div><div><p class="font-semibold">{{ authStore.user?.name }}</p><p class="text-xs text-gray-500">Pembimbing Perusahaan</p></div></div>
      </div>
      <nav class="px-4 py-4">
        <router-link to="/perusahaan/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl mb-1" :class="$route.path === '/perusahaan/dashboard' ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white' : 'text-gray-600 hover:bg-gray-100'"><HomeIcon class="w-5 h-5" /> Dashboard</router-link>
        <router-link to="/perusahaan/assessment" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100"><StarIcon class="w-5 h-5" /> Penilaian Logbook</router-link>
        <router-link to="/perusahaan/progress" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100"><ChartBarIcon class="w-5 h-5" /> Progress Siswa</router-link>
      </nav>
      <div class="absolute bottom-0 left-0 right-0 p-4 border-t"><button @click="logout" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 hover:bg-red-50"><ArrowRightOnRectangleIcon class="w-5 h-5" /> Logout</button></div>
    </aside>
    <div class="ml-72">
      <header class="bg-white/80 backdrop-blur-lg border-b px-6 py-4"><h2 class="text-xl font-bold">{{ $route.path.split('/').pop() || 'Dashboard' }}</h2></header>
      <main class="p-6"><router-view /></main>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { HomeIcon, StarIcon, ChartBarIcon, ArrowRightOnRectangleIcon } from '@heroicons/vue/24/outline'

const router = useRouter(), authStore = useAuthStore()
const logout = async () => { await authStore.logout(); router.push('/login') }
</script>