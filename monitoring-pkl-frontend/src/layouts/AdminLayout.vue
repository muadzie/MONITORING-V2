<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside 
      class="fixed left-0 top-0 z-40 h-screen bg-white shadow-2xl transition-all duration-300 ease-in-out overflow-hidden"
      :class="[
        sidebarCollapsed ? 'w-20' : 'w-72',
        isMobile && !sidebarOpen ? '-translate-x-full' : 'translate-x-0'
      ]"
    >
      <!-- Logo Section -->
      <div class="flex items-center justify-between px-5 py-5 border-b" :class="sidebarCollapsed ? 'justify-center px-2' : ''">
        <div class="flex items-center gap-3" :class="sidebarCollapsed ? 'justify-center' : ''">
          <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
          </div>
          <div v-if="!sidebarCollapsed" class="transition-opacity duration-300">
            <h1 class="text-xl font-bold text-gray-800">Admin Panel</h1>
            <p class="text-xs text-gray-500">SMKN 1 Subang</p>
          </div>
        </div>
      </div>

      <!-- User Profile -->
      <div class="p-4 m-3 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl transition-all duration-300" :class="sidebarCollapsed ? 'mx-2 p-2' : ''">
        <div class="flex items-center gap-3" :class="sidebarCollapsed ? 'justify-center' : ''">
          <div class="relative flex-shrink-0">
            <div class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md">
              {{ authStore.user?.name?.charAt(0) || 'A' }}
            </div>
            <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-green-500 rounded-full border-2 border-white"></div>
          </div>
          <div v-if="!sidebarCollapsed" class="flex-1 min-w-0">
            <p class="font-semibold text-gray-800 text-sm truncate">{{ authStore.user?.name }}</p>
            <p class="text-xs text-gray-500">Administrator</p>
          </div>
        </div>
      </div>

      <!-- Navigation Menu -->
      <nav class="flex-1 px-3 py-4 overflow-y-auto h-[calc(100vh-200px)]" :class="sidebarCollapsed ? 'px-2' : ''">
        
        <!-- Dashboard -->
        <router-link 
          to="/admin/dashboard"
          class="flex items-center gap-3 px-3 py-3 rounded-xl transition-all duration-200 mb-1 group"
          :class="[
            $route.path === '/admin/dashboard' || $route.path === '/admin' 
              ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md' 
              : 'text-gray-600 hover:bg-gray-100',
            sidebarCollapsed ? 'justify-center' : ''
          ]"
          :title="sidebarCollapsed ? 'Dashboard' : ''"
        >
          <HomeIcon class="w-5 h-5 flex-shrink-0" />
          <span v-if="!sidebarCollapsed" class="text-sm font-medium">Dashboard</span>
        </router-link>

        <!-- Manajemen User (Dropdown - Default Open) -->
        <div class="mb-1">
          <button 
            @click="toggleMenu('users')"
            class="w-full flex items-center justify-between px-3 py-3 rounded-xl transition-all duration-200 group"
            :class="[
              openMenus.users ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-100',
              sidebarCollapsed ? 'justify-center' : ''
            ]"
            :title="sidebarCollapsed ? 'Manajemen User' : ''"
          >
            <div class="flex items-center gap-3">
              <UsersIcon class="w-5 h-5 flex-shrink-0" />
              <span v-if="!sidebarCollapsed" class="text-sm font-medium">Manajemen User</span>
            </div>
            <ChevronDownIcon 
              v-if="!sidebarCollapsed"
              class="w-4 h-4 transition-transform duration-200" 
              :class="{ 'rotate-180': openMenus.users }" 
            />
          </button>
          <transition name="slide-down">
            <div v-show="openMenus.users && !sidebarCollapsed" class="ml-4 mt-1 space-y-1 border-l-2 border-indigo-200 pl-3">
              <router-link to="/admin/users" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100 hover:text-indigo-600 transition">
                <span>📋</span> Semua User
              </router-link>
              <router-link to="/admin/roles" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100 hover:text-indigo-600 transition">
                <span>👥</span> Manajemen Role
              </router-link>
            </div>
          </transition>
        </div>

        <!-- Data Master (Dropdown - Default Open) -->
        <div class="mb-1">
          <button 
            @click="toggleMenu('master')"
            class="w-full flex items-center justify-between px-3 py-3 rounded-xl transition-all duration-200 group"
            :class="[
              openMenus.master ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-100',
              sidebarCollapsed ? 'justify-center' : ''
            ]"
            :title="sidebarCollapsed ? 'Data Master' : ''"
          >
            <div class="flex items-center gap-3">
              <BookOpenIcon class="w-5 h-5 flex-shrink-0" />
              <span v-if="!sidebarCollapsed" class="text-sm font-medium">Data Master</span>
            </div>
            <ChevronDownIcon 
              v-if="!sidebarCollapsed"
              class="w-4 h-4 transition-transform duration-200" 
              :class="{ 'rotate-180': openMenus.master }" 
            />
          </button>
          <transition name="slide-down">
            <div v-show="openMenus.master && !sidebarCollapsed" class="ml-4 mt-1 space-y-1 border-l-2 border-indigo-200 pl-3">
              <router-link to="/admin/students" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100 hover:text-indigo-600 transition">
                <span>🎓</span> Data Siswa
              </router-link>
              <router-link to="/admin/teachers" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100 hover:text-indigo-600 transition">
                <span>👨‍🏫</span> Data Guru
              </router-link>
              <router-link to="/admin/companies" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100 hover:text-indigo-600 transition">
                <span>🏢</span> Data Perusahaan
              </router-link>
              <router-link to="/admin/classes" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100 hover:text-indigo-600 transition">
                <span>📚</span> Data Kelas
              </router-link>
            </div>
          </transition>
        </div>

        <!-- Penempatan PKL (Dropdown) -->
        <div class="mb-1">
          <button 
            @click="toggleMenu('placement')"
            class="w-full flex items-center justify-between px-3 py-3 rounded-xl transition-all duration-200 group"
            :class="[
              openMenus.placement ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-100',
              sidebarCollapsed ? 'justify-center' : ''
            ]"
            :title="sidebarCollapsed ? 'Penempatan PKL' : ''"
          >
            <div class="flex items-center gap-3">
              <MapPinIcon class="w-5 h-5 flex-shrink-0" />
              <span v-if="!sidebarCollapsed" class="text-sm font-medium">Penempatan PKL</span>
            </div>
            <ChevronDownIcon 
              v-if="!sidebarCollapsed"
              class="w-4 h-4 transition-transform duration-200" 
              :class="{ 'rotate-180': openMenus.placement }" 
            />
          </button>
          <transition name="slide-down">
            <div v-show="openMenus.placement && !sidebarCollapsed" class="ml-4 mt-1 space-y-1 border-l-2 border-indigo-200 pl-3">
              <router-link to="/admin/placements" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100 hover:text-indigo-600 transition">
                <span>📍</span> Data Penempatan
              </router-link>
              <router-link to="/admin/map" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100 hover:text-indigo-600 transition">
                <span>🗺️</span> Peta Sebaran
              </router-link>
            </div>
          </transition>
        </div>

        <!-- Monitoring (Dropdown) -->
        <div class="mb-1">
          <button 
            @click="toggleMenu('monitoring')"
            class="w-full flex items-center justify-between px-3 py-3 rounded-xl transition-all duration-200 group"
            :class="[
              openMenus.monitoring ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-100',
              sidebarCollapsed ? 'justify-center' : ''
            ]"
            :title="sidebarCollapsed ? 'Monitoring' : ''"
          >
            <div class="flex items-center gap-3">
              <ViewfinderCircleIcon class="w-5 h-5 flex-shrink-0" />
              <span v-if="!sidebarCollapsed" class="text-sm font-medium">Monitoring</span>
            </div>
            <ChevronDownIcon 
              v-if="!sidebarCollapsed"
              class="w-4 h-4 transition-transform duration-200" 
              :class="{ 'rotate-180': openMenus.monitoring }" 
            />
          </button>
          <transition name="slide-down">
            <div v-show="openMenus.monitoring && !sidebarCollapsed" class="ml-4 mt-1 space-y-1 border-l-2 border-indigo-200 pl-3">
              <router-link to="/admin/monitoring/attendance" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100 hover:text-indigo-600 transition">
                <span>📊</span> Monitoring Absensi
              </router-link>
              <router-link to="/admin/monitoring/logbook" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100 hover:text-indigo-600 transition">
                <span>📝</span> Monitoring Logbook
              </router-link>
              <router-link to="/admin/monitoring/progress" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100 hover:text-indigo-600 transition">
                <span>📈</span> Progress Siswa
              </router-link>
            </div>
          </transition>
        </div>

        <!-- Laporan (Dropdown) -->
        <!-- Laporan - Satu Menu Langsung (Tanpa Dropdown) -->
<router-link 
  to="/admin/reports"
  class="flex items-center gap-3 px-3 py-3 rounded-xl transition-all duration-200 mb-1 group"
  :class="[
    $route.path.startsWith('/admin/reports') 
      ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md' 
      : 'text-gray-600 hover:bg-gray-100',
    sidebarCollapsed ? 'justify-center' : ''
  ]"
  :title="sidebarCollapsed ? 'Laporan' : ''"
>
  <DocumentChartBarIcon class="w-5 h-5 flex-shrink-0" />
  <span v-if="!sidebarCollapsed" class="text-sm font-medium">Laporan</span>
</router-link>

        <!-- Penilaian (Dropdown) -->
        <div class="mb-1">
          <button 
            @click="toggleMenu('assessment')"
            class="w-full flex items-center justify-between px-3 py-3 rounded-xl transition-all duration-200 group"
            :class="[
              openMenus.assessment ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-100',
              sidebarCollapsed ? 'justify-center' : ''
            ]"
            :title="sidebarCollapsed ? 'Penilaian' : ''"
          >
            <div class="flex items-center gap-3">
              <StarIcon class="w-5 h-5 flex-shrink-0" />
              <span v-if="!sidebarCollapsed" class="text-sm font-medium">Penilaian</span>
            </div>
            <ChevronDownIcon 
              v-if="!sidebarCollapsed"
              class="w-4 h-4 transition-transform duration-200" 
              :class="{ 'rotate-180': openMenus.assessment }" 
            />
          </button>
          <transition name="slide-down">
            <div v-show="openMenus.assessment && !sidebarCollapsed" class="ml-4 mt-1 space-y-1 border-l-2 border-indigo-200 pl-3">
              <router-link to="/admin/assessments" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100 hover:text-indigo-600 transition">
                <span>⭐</span> Nilai PKL
              </router-link>
            </div>
          </transition>
        </div>

        <!-- Pengaturan (Dropdown) -->
        <div class="mb-1">
          <button 
            @click="toggleMenu('settings')"
            class="w-full flex items-center justify-between px-3 py-3 rounded-xl transition-all duration-200 group"
            :class="[
              openMenus.settings ? 'bg-indigo-50 text-indigo-600' : 'text-gray-600 hover:bg-gray-100',
              sidebarCollapsed ? 'justify-center' : ''
            ]"
            :title="sidebarCollapsed ? 'Pengaturan' : ''"
          >
            <div class="flex items-center gap-3">
              <Cog6ToothIcon class="w-5 h-5 flex-shrink-0" />
              <span v-if="!sidebarCollapsed" class="text-sm font-medium">Pengaturan</span>
            </div>
            <ChevronDownIcon 
              v-if="!sidebarCollapsed"
              class="w-4 h-4 transition-transform duration-200" 
              :class="{ 'rotate-180': openMenus.settings }" 
            />
          </button>
          <transition name="slide-down">
            <div v-show="openMenus.settings && !sidebarCollapsed" class="ml-4 mt-1 space-y-1 border-l-2 border-indigo-200 pl-3">
              <router-link to="/admin/settings/general" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-100 hover:text-indigo-600 transition">
                <span>⚙️</span> Umum
              </router-link>
            </div>
          </transition>
        </div>
      </nav>

      <!-- Footer Menu Logout -->
      <div class="absolute bottom-0 left-0 right-0 p-3 border-t bg-white" :class="sidebarCollapsed ? 'p-2' : ''">
        <button 
          @click="logout"
          class="w-full flex items-center gap-3 px-3 py-3 rounded-xl text-red-600 hover:bg-red-50 transition-all duration-200 group"
          :class="sidebarCollapsed ? 'justify-center' : ''"
          :title="sidebarCollapsed ? 'Logout' : ''"
        >
          <ArrowRightOnRectangleIcon class="w-5 h-5 flex-shrink-0" />
          <span v-if="!sidebarCollapsed" class="text-sm font-medium">Logout</span>
        </button>
      </div>
    </aside>

    <!-- Overlay untuk mobile -->
    <div 
      v-if="isMobile && sidebarOpen" 
      @click="closeSidebar" 
      class="fixed inset-0 bg-black/50 z-30 transition-all duration-300"
    ></div>

    <!-- Main Content -->
    <div 
      class="transition-all duration-300 ease-in-out"
      :class="[
        !sidebarCollapsed && !isMobile ? 'lg:ml-72' : (isMobile ? 'ml-0' : 'lg:ml-20'),
        isMobile && sidebarOpen ? 'ml-72' : ''
      ]"
    >
      <!-- Top Navbar dengan Burger Menu -->
      <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-lg border-b border-gray-100">
        <div class="flex items-center justify-between px-6 py-4">
          <div class="flex items-center gap-4">
            <!-- Burger Menu Button - Responsif -->
            <button 
              @click="toggleSidebar" 
              class="p-2 rounded-lg hover:bg-gray-100 transition-all duration-200 focus:outline-none group"
              :class="{ 'bg-indigo-50': sidebarOpen }"
            >
              <!-- Icon berubah berdasarkan kondisi sidebar -->
              <svg 
                v-if="!sidebarCollapsed && !isMobile" 
                class="w-6 h-6 text-gray-600 group-hover:text-indigo-600 transition" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
              <svg 
                v-else-if="sidebarCollapsed && !isMobile" 
                class="w-6 h-6 text-gray-600 group-hover:text-indigo-600 transition" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" />
              </svg>
              <svg 
                v-else 
                class="w-6 h-6 text-gray-600 group-hover:text-indigo-600 transition" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
            </button>
            <div>
              <h2 class="text-xl font-bold text-gray-800">{{ pageTitle }}</h2>
              <p class="text-sm text-gray-500">{{ pageDescription }}</p>
            </div>
          </div>
          
          <div class="flex items-center gap-3">
            <!-- Search Bar -->
            <div class="hidden md:flex items-center bg-gray-100 rounded-xl px-4 py-2 transition-all focus-within:ring-2 focus-within:ring-indigo-500 focus-within:bg-white">
              <MagnifyingGlassIcon class="w-4 h-4 text-gray-400" />
              <input type="text" placeholder="Cari..." class="bg-transparent border-none focus:outline-none text-sm ml-2 w-48">
            </div>
            
            <!-- Notification Bell -->
            <NotificationBell />
            
            <!-- User Menu -->
            <div class="relative">
              <button @click="userMenuOpen = !userMenuOpen" class="flex items-center gap-2 p-1 rounded-xl hover:bg-gray-100 transition group">
                <div class="w-8 h-8 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg flex items-center justify-center text-white text-sm font-bold shadow-md">
                  {{ authStore.user?.name?.charAt(0) || 'A' }}
                </div>
                <ChevronDownIcon class="w-4 h-4 text-gray-500 hidden md:block transition-transform duration-200" :class="{ 'rotate-180': userMenuOpen }" />
              </button>
              
              <!-- Dropdown User Menu -->
              <transition name="fade-down">
                <div v-if="userMenuOpen" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border overflow-hidden z-50">
                  <div class="p-3 border-b bg-gradient-to-r from-indigo-50 to-purple-50">
                    <p class="font-semibold text-gray-800">{{ authStore.user?.name }}</p>
                    <p class="text-xs text-gray-500">{{ authStore.user?.email }}</p>
                  </div>
                  <router-link to="/admin/profile" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                    <UserIcon class="w-4 h-4 text-gray-500" /> Profile
                  </router-link>
                  <router-link to="/admin/settings/general" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                    <Cog6ToothIcon class="w-4 h-4 text-gray-500" /> Settings
                  </router-link>
                  <hr>
                  <button @click="logout" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-red-50 text-red-600 transition">
                    <ArrowRightOnRectangleIcon class="w-4 h-4" /> Logout
                  </button>
                </div>
              </transition>
            </div>
          </div>
        </div>
      </header>

      <!-- Page Content -->
      <main class="p-6 animate-fadeIn">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
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
  ChevronDownIcon,
  BookOpenIcon,
  StarIcon
} from '@heroicons/vue/24/outline'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

// State
const sidebarCollapsed = ref(false)
const sidebarOpen = ref(true)
const userMenuOpen = ref(false)
const windowWidth = ref(window.innerWidth)

// Check if mobile
const isMobile = computed(() => windowWidth.value < 1024)

// Dropdown menu states (default terbuka untuk Users dan Master)
const openMenus = ref({
  users: false,      // Default terbuka
  master: false,     // Default terbuka
  placement: false,
  monitoring: false,
  reports: false,
  assessment: false,
  settings: false
})

// Computed page title
const pageTitle = computed(() => {
  const path = route.path
  const titles = {
    '/admin/dashboard': 'Dashboard',
    '/admin/users': 'Manajemen User',
    '/admin/roles': 'Manajemen Role',
    '/admin/students': 'Manajemen Siswa',
    '/admin/teachers': 'Manajemen Guru',
    '/admin/companies': 'Manajemen Perusahaan',
    '/admin/classes': 'Manajemen Kelas',
    '/admin/placements': 'Penempatan PKL',
    '/admin/map': 'Peta Sebaran',
    '/admin/monitoring/attendance': 'Monitoring Absensi',
    '/admin/monitoring/logbook': 'Monitoring Logbook',
    '/admin/monitoring/progress': 'Progress Siswa',
    '/admin/assessments': 'Penilaian PKL',
    '/admin/settings/general': 'Pengaturan Umum',
    '/admin/settings/radius': 'Pengaturan Radius',
    '/admin/profile': 'Profile'
  }
  return titles[path] || 'Admin Panel'
})

const pageDescription = computed(() => {
  const desc = {
    '/admin/dashboard': 'Ringkasan data dan statistik PKL',
    '/admin/users': 'Kelola semua pengguna sistem',
    '/admin/students': 'Kelola data siswa peserta PKL',
    '/admin/teachers': 'Kelola data guru pembimbing',
    '/admin/companies': 'Kelola data perusahaan mitra',
    '/admin/placements': 'Kelola penempatan PKL siswa'
  }
  return desc[route.path] || ''
})

// Methods
const toggleMenu = (menu) => {
  if (!sidebarCollapsed.value) {
    openMenus.value[menu] = !openMenus.value[menu]
  }
}

const toggleSidebar = () => {
  if (isMobile.value) {
    sidebarOpen.value = !sidebarOpen.value
    sidebarCollapsed.value = !sidebarOpen.value
  } else {
    sidebarCollapsed.value = !sidebarCollapsed.value
    sidebarOpen.value = !sidebarCollapsed.value
  }
  localStorage.setItem('adminSidebarCollapsed', sidebarCollapsed.value)
}

const closeSidebar = () => {
  if (isMobile.value) {
    sidebarOpen.value = false
    sidebarCollapsed.value = true
  }
}

const handleResize = () => {
  windowWidth.value = window.innerWidth
  if (windowWidth.value >= 1024) {
    const savedState = localStorage.getItem('adminSidebarCollapsed')
    sidebarCollapsed.value = savedState === 'true'
    sidebarOpen.value = !sidebarCollapsed.value
  } else {
    sidebarCollapsed.value = true
    sidebarOpen.value = false
  }
}

const logout = async () => {
  await authStore.logout()
  router.push('/login')
}

// Watch route change untuk close dropdown
watch(() => route.path, () => {
  userMenuOpen.value = false
  if (isMobile.value) {
    sidebarOpen.value = false
    sidebarCollapsed.value = true
  }
})

// Lifecycle
onMounted(() => {
  handleResize()
  window.addEventListener('resize', handleResize)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})
</script>

<style scoped>
/* Scrollbar styling */
nav::-webkit-scrollbar {
  width: 4px;
}

nav::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

nav::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 10px;
}

nav::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Transition animations */
.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.2s ease-out;
}

.slide-down-enter-from,
.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}

.fade-down-enter-active,
.fade-down-leave-active {
  transition: all 0.15s ease-out;
}

.fade-down-enter-from,
.fade-down-leave-to {
  opacity: 0;
  transform: translateY(-5px);
}

.animate-fadeIn {
  animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Active route styling */
.router-link-active {
  background: linear-gradient(135deg, #4f46e5, #7c3aed);
  color: white;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

/* Hover effects */
.group:hover .group-hover\:text-indigo-600 {
  color: #4f46e5;
}
</style>