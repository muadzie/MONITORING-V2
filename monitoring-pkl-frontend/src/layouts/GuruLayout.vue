<template>
  <div class="min-h-screen bg-gray-50">
    <aside class="fixed left-0 top-0 z-40 h-screen w-72 bg-white shadow-2xl transition-all duration-300"
      :class="sidebarCollapsed ? '-translate-x-full' : 'translate-x-0'">
      <div class="flex items-center justify-between px-6 py-5 border-b">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
          </div>
          <div><h1 class="text-xl font-bold text-gray-800">Guru Panel</h1><p class="text-xs text-gray-500">SMKN 1 Subang</p></div>
        </div>
      </div>
      <div class="p-4 m-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl">
        <div class="flex items-center gap-3">
          <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-xl flex items-center justify-center text-white font-bold text-lg">{{ authStore.user?.name?.charAt(0) || 'G' }}</div>
          <div><p class="font-semibold text-gray-800 text-sm">{{ authStore.user?.name }}</p><p class="text-xs text-gray-500">Guru Pembimbing</p></div>
        </div>
      </div>
      <nav class="flex-1 px-4 py-4">
        <router-link to="/guru/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl mb-1" :class="$route.path === '/guru/dashboard' ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white' : 'text-gray-600 hover:bg-gray-100'">
          <HomeIcon class="w-5 h-5" /><span>Dashboard</span>
        </router-link>
        <router-link to="/guru/monitoring" class="flex items-center gap-3 px-4 py-3 rounded-xl mb-1 text-gray-600 hover:bg-gray-100">
          <UserGroupIcon class="w-5 h-5" /><span>Monitoring Siswa</span>
        </router-link>
        <router-link to="/guru/attendance-monitoring" class="flex items-center gap-3 px-4 py-3 rounded-xl mb-1 text-gray-600 hover:bg-gray-100">
  <ClipboardDocumentListIcon class="w-5 h-5" />
  <span>Monitoring Absensi</span>
</router-link>
        <router-link to="/guru/logbook-review" class="flex items-center gap-3 px-4 py-3 rounded-xl mb-1 text-gray-600 hover:bg-gray-100">
          <BookOpenIcon class="w-5 h-5" /><span>Review Logbook</span>
        </router-link>
        <router-link to="/guru/permissions" class="flex items-center gap-3 px-4 py-3 rounded-xl mb-1 text-gray-600 hover:bg-gray-100">
          <DocumentTextIcon class="w-5 h-5" /><span>Approval Izin</span>
        </router-link>
        <!-- MENU BARU: Review Laporan PKL -->
        <router-link to="/guru/report-review" class="flex items-center gap-3 px-4 py-3 rounded-xl mb-1 text-gray-600 hover:bg-gray-100">
          <DocumentChartBarIcon class="w-5 h-5" /><span>Review Laporan PKL</span>
        </router-link>
        <router-link to="/guru/assessment" class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100">
          <ClipboardDocumentListIcon class="w-5 h-5" /><span>Penilaian PKL</span>
        </router-link>
      </nav>
      <div class="absolute bottom-0 left-0 right-0 p-4 border-t bg-white">
        <button @click="logout" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 hover:bg-red-50"><ArrowRightOnRectangleIcon class="w-5 h-5" /> Logout</button>
      </div>
    </aside>
    <div class="transition-all duration-300 lg:ml-72">
      <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-lg border-b px-6 py-4">
        <div class="flex items-center justify-between">
          <div><h2 class="text-xl font-bold">{{ pageTitle }}</h2><p class="text-sm text-gray-500">{{ pageDescription }}</p></div>
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center text-white">{{ authStore.user?.name?.charAt(0) }}</div>
          </div>
        </div>
      </header>
      <main class="p-6"><router-view /></main>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { HomeIcon, UserGroupIcon, BookOpenIcon, DocumentTextIcon, DocumentChartBarIcon, ClipboardDocumentListIcon, ArrowRightOnRectangleIcon } from '@heroicons/vue/24/outline'

const route = useRoute(), router = useRouter(), authStore = useAuthStore()
const pageTitle = computed(() => ({ 
  '/guru/dashboard': 'Dashboard', 
  '/guru/monitoring': 'Monitoring Siswa', 
  '/guru/logbook-review': 'Review Logbook', 
  '/guru/permissions': 'Approval Izin', 
  '/guru/report-review': 'Review Laporan PKL',
  '/guru/assessment': 'Penilaian PKL'
}[route.path] || 'Dashboard'))

const pageDescription = computed(() => ({ 
  '/guru/dashboard': 'Ringkasan aktivitas bimbingan PKL', 
  '/guru/monitoring': 'Pantau aktivitas dan progres siswa',
  '/guru/report-review': 'Review dan download laporan PKL siswa'
}[route.path] || ''))

const logout = async () => { await authStore.logout(); router.push('/login') }
</script>