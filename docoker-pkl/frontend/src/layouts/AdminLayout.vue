<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside 
      class="fixed left-0 top-0 z-40 h-screen w-72 bg-white shadow-2xl transition-all duration-300"
      :class="sidebarCollapsed ? '-translate-x-full' : 'translate-x-0'"
    >
      <!-- Logo -->
      <div class="flex items-center justify-between px-6 py-5 border-b">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
          </div>
          <div>
            <h1 class="text-xl font-bold text-gray-800">Admin Panel</h1>
            <p class="text-xs text-gray-500">SMKN 1 Subang</p>
          </div>
        </div>
        <button @click="sidebarCollapsed = !sidebarCollapsed" class="lg:hidden text-gray-500">
          <XMarkIcon class="w-6 h-6" />
        </button>
      </div>

      <!-- User Profile -->
      <div class="p-4 m-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl">
        <div class="flex items-center gap-3">
          <div class="relative">
            <div class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md">
              {{ authStore.user?.name?.charAt(0) || 'A' }}
            </div>
            <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-green-500 rounded-full border-2 border-white"></div>
          </div>
          <div class="flex-1">
            <p class="font-semibold text-gray-800 text-sm">{{ authStore.user?.name }}</p>
            <p class="text-xs text-gray-500">Administrator</p>
          </div>
        </div>
      </div>

      <!-- Navigation Menu -->
      <nav class="flex-1 px-4 py-4 overflow-y-auto h-[calc(100vh-200px)]">
        <!-- Dashboard -->
        <div class="mb-6">
          <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">MAIN</p>
          <router-link 
            to="/admin/dashboard"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200"
            :class="$route.path === '/admin/dashboard' ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100'"
          >
            <HomeIcon class="w-5 h-5" />
            <span class="text-sm font-medium">Dashboard</span>
          </router-link>
        </div>

        <!-- Manajemen User -->
        <div class="mb-6">
          <button 
            @click="toggleMenu('users')"
            class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition"
          >
            <div class="flex items-center gap-3">
              <UsersIcon class="w-5 h-5" />
              <span class="text-sm font-medium">Manajemen User</span>
            </div>
            <ChevronDownIcon class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openMenus.users }" />
          </button>
          <div v-show="openMenus.users" class="ml-9 mt-1 space-y-1">
            <router-link to="/admin/users" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">
              📋 Semua User
            </router-link>
            <router-link to="/admin/roles" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">
              👥 Manajemen Role
            </router-link>
          </div>
        </div>

        <!-- Data Master -->
        <div class="mb-6">
          <button 
            @click="toggleMenu('master')"
            class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition"
          >
            <div class="flex items-center gap-3">
              <BookOpenIcon class="w-5 h-5" />
              <span class="text-sm font-medium">Data Master</span>
            </div>
            <ChevronDownIcon class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openMenus.master }" />
          </button>
          <div v-show="openMenus.master" class="ml-9 mt-1 space-y-1">
            <router-link to="/admin/students" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">🎓 Data Siswa</router-link>
            <router-link to="/admin/teachers" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">👨‍🏫 Data Guru</router-link>
            <router-link to="/admin/companies" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">🏢 Data Perusahaan</router-link>
            <router-link to="/admin/classes" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">📚 Data Kelas</router-link>
          </div>
        </div>

        <!-- Penempatan PKL -->
        <div class="mb-6">
          <button 
            @click="toggleMenu('placement')"
            class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition"
          >
            <div class="flex items-center gap-3">
              <MapPinIcon class="w-5 h-5" />
              <span class="text-sm font-medium">Penempatan PKL</span>
            </div>
            <ChevronDownIcon class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openMenus.placement }" />
          </button>
          <div v-show="openMenus.placement" class="ml-9 mt-1 space-y-1">
            <router-link to="/admin/placements" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">📍 Data Penempatan</router-link>
            <router-link to="/admin/map" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">🗺️ Peta Sebaran</router-link>
          </div>
        </div>

        <!-- Monitoring -->
        <div class="mb-6">
          <button 
            @click="toggleMenu('monitoring')"
            class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition"
          >
            <div class="flex items-center gap-3">
              <ViewfinderCircleIcon class="w-5 h-5" />
              <span class="text-sm font-medium">Monitoring</span>
            </div>
            <ChevronDownIcon class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openMenus.monitoring }" />
          </button>
          <div v-show="openMenus.monitoring" class="ml-9 mt-1 space-y-1">
            <router-link to="/admin/monitoring/attendance" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">📊 Monitoring Absensi</router-link>
            <router-link to="/admin/monitoring/logbook" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">📝 Monitoring Logbook</router-link>
            <router-link to="/admin/monitoring/progress" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">📈 Progress Siswa</router-link>
            <router-link to="/admin/monitoring/map" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">🗺️ Monitoring Map</router-link>
          </div>
        </div>

        <!-- Laporan -->
        <div class="mb-6">
          <button 
            @click="toggleMenu('reports')"
            class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition"
          >
            <div class="flex items-center gap-3">
              <DocumentChartBarIcon class="w-5 h-5" />
              <span class="text-sm font-medium">Laporan</span>
            </div>
            <ChevronDownIcon class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openMenus.reports }" />
          </button>
          <div v-show="openMenus.reports" class="ml-9 mt-1 space-y-1">
            <router-link to="/admin/reports/attendance" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">📋 Laporan Absensi</router-link>
            <router-link to="/admin/reports/logbook" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">📖 Laporan Logbook</router-link>
            <router-link to="/admin/reports/grade" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">🎯 Laporan Nilai</router-link>
            <router-link to="/admin/reports/summary" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">📊 Rekap Keseluruhan</router-link>
          </div>
        </div>

        <!-- Penilaian -->
        <div class="mb-6">
          <button 
            @click="toggleMenu('assessment')"
            class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition"
          >
            <div class="flex items-center gap-3">
              <StarIcon class="w-5 h-5" />
              <span class="text-sm font-medium">Penilaian</span>
            </div>
            <ChevronDownIcon class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openMenus.assessment }" />
          </button>
          <div v-show="openMenus.assessment" class="ml-9 mt-1 space-y-1">
            <router-link to="/admin/assessments" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">⭐ Nilai PKL</router-link>
            <router-link to="/admin/assessments/report" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">📊 Rekap Nilai</router-link>
          </div>
        </div>

        <!-- Pengaturan -->
        <div class="mb-6">
          <button 
            @click="toggleMenu('settings')"
            class="w-full flex items-center justify-between px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition"
          >
            <div class="flex items-center gap-3">
              <Cog6ToothIcon class="w-5 h-5" />
              <span class="text-sm font-medium">Pengaturan</span>
            </div>
            <ChevronDownIcon class="w-4 h-4 transition-transform" :class="{ 'rotate-180': openMenus.settings }" />
          </button>
          <div v-show="openMenus.settings" class="ml-9 mt-1 space-y-1">
            <router-link to="/admin/settings/general" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">⚙️ Umum</router-link>
            <router-link to="/admin/settings/school" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">🏫 Data Sekolah</router-link>
            <router-link to="/admin/settings/academic" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">📅 Tahun Ajaran</router-link>
            <router-link to="/admin/settings/radius" class="flex items-center gap-3 px-4 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100">📍 Pengaturan Radius</router-link>
          </div>
        </div>
      </nav>

      <!-- Footer -->
      <div class="absolute bottom-0 left-0 right-0 p-4 border-t bg-white">
        <button 
          @click="logout"
          class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 hover:bg-red-50 transition"
        >
          <ArrowRightOnRectangleIcon class="w-5 h-5" />
          <span class="text-sm font-medium">Logout</span>
        </button>
      </div>
    </aside>

    <!-- Overlay -->
    <div v-if="!sidebarCollapsed" @click="sidebarCollapsed = true" class="fixed inset-0 bg-black/50 z-30 lg:hidden"></div>

    <!-- Main Content -->
    <div class="transition-all duration-300 lg:ml-72">
      <!-- Top Navbar -->
      <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-lg border-b border-gray-100">
        <div class="flex items-center justify-between px-6 py-4">
          <div class="flex items-center gap-4">
            <button @click="sidebarCollapsed = !sidebarCollapsed" class="p-2 rounded-lg hover:bg-gray-100 transition">
              <Bars3Icon class="w-5 h-5 text-gray-600" />
            </button>
            <div>
              <h2 class="text-xl font-bold text-gray-800">{{ pageTitle }}</h2>
              <p class="text-sm text-gray-500">{{ pageDescription }}</p>
            </div>
          </div>
          
          <div class="flex items-center gap-3">
            <div class="hidden md:flex items-center bg-gray-100 rounded-xl px-4 py-2">
              <MagnifyingGlassIcon class="w-4 h-4 text-gray-400" />
              <input type="text" placeholder="Cari..." class="bg-transparent border-none focus:outline-none text-sm ml-2 w-48">
            </div>
            <NotificationBell />
            <div class="relative">
              <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 p-1 rounded-xl hover:bg-gray-100 transition">
                <div class="w-8 h-8 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center text-white text-sm font-bold">
                  {{ authStore.user?.name?.charAt(0) || 'A' }}
                </div>
                <ChevronDownIcon class="w-4 h-4 text-gray-500" />
              </button>
              <div v-if="userMenuOpen" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border overflow-hidden z-50">
                <div class="p-3 border-b">
                  <p class="font-semibold text-gray-800">{{ authStore.user?.name }}</p>
                  <p class="text-xs text-gray-500">{{ authStore.user?.email }}</p>
                </div>
                <router-link to="/admin/profile" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                  <UserIcon class="w-4 h-4 text-gray-500" /> Profile
                </router-link>
                <router-link to="/admin/settings" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                  <Cog6ToothIcon class="w-4 h-4 text-gray-500" /> Settings
                </router-link>
                <hr>
                <button @click="logout" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-red-50 text-red-600 transition">
                  <ArrowRightOnRectangleIcon class="w-4 h-4" /> Logout
                </button>
              </div>
            </div>
          </div>
        </div>
      </header>

      <main class="p-6">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import NotificationBell from '../components/NotificationBell.vue'
import {
  HomeIcon, 
  UsersIcon, 
  MapPinIcon, 
  ViewfinderCircleIcon,
  DocumentChartBarIcon, 
  Cog6ToothIcon, 
  MagnifyingGlassIcon,
  UserIcon, 
  ArrowRightOnRectangleIcon, 
  Bars3Icon, 
  XMarkIcon, 
  ChevronDownIcon,
  BookOpenIcon,
  StarIcon
} from '@heroicons/vue/24/outline'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

const sidebarCollapsed = ref(false)
const userMenuOpen = ref(false)
const openMenus = ref({
  users: true,
  master: true,
  placement: false,
  monitoring: false,
  reports: false,
  assessment: false,
  settings: false
})

const pageTitle = computed(() => {
  const path = route.path
  if (path.includes('/dashboard')) return 'Dashboard'
  if (path.includes('/users')) return 'Manajemen User'
  if (path.includes('/students')) return 'Manajemen Siswa'
  if (path.includes('/teachers')) return 'Manajemen Guru'
  if (path.includes('/companies')) return 'Manajemen Perusahaan'
  if (path.includes('/placements')) return 'Penempatan PKL'
  if (path.includes('/monitoring')) return 'Monitoring'
  if (path.includes('/reports')) return 'Laporan'
  if (path.includes('/assessments')) return 'Penilaian'
  if (path.includes('/settings')) return 'Pengaturan'
  return 'Admin Panel'
})

const pageDescription = computed(() => {
  const descriptions = {
    '/admin/dashboard': 'Ringkasan data dan statistik PKL',
    '/admin/users': 'Kelola semua pengguna sistem',
    '/admin/students': 'Kelola data siswa peserta PKL',
    '/admin/teachers': 'Kelola data guru pembimbing',
    '/admin/companies': 'Kelola data perusahaan mitra',
    '/admin/placements': 'Kelola penempatan PKL siswa',
    '/admin/monitoring/attendance': 'Pantau absensi siswa',
    '/admin/monitoring/logbook': 'Pantau logbook siswa',
    '/admin/assessments': 'Kelola nilai PKL siswa',
    '/admin/settings/radius': 'Atur radius absensi perusahaan'
  }
  return descriptions[route.path] || ''
})

const toggleMenu = (menu) => {
  openMenus.value[menu] = !openMenus.value[menu]
}

const logout = async () => {
  await authStore.logout()
  router.push('/login')
}
</script>