<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="fixed left-0 top-0 z-40 h-screen w-72 bg-white shadow-2xl transition-all duration-300"
      :class="sidebarCollapsed ? '-translate-x-full' : 'translate-x-0'">
      <!-- Logo -->
      <div class="flex items-center justify-between px-6 py-5 border-b">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 bg-gradient-to-br from-green-600 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
          </div>
          <div>
            <h1 class="text-xl font-bold text-gray-800">Siswa Panel</h1>
            <p class="text-xs text-gray-500">SMKN 1 Subang</p>
          </div>
        </div>
        <button @click="toggleSidebar" class="lg:hidden text-gray-500">
          <XMarkIcon class="w-6 h-6" />
        </button>
      </div>

      <!-- User Profile -->
      <div class="p-4 m-4 bg-gradient-to-r from-green-50 to-teal-50 rounded-xl">
        <div class="flex items-center gap-3">
          <div class="relative">
            <div class="w-12 h-12 bg-gradient-to-br from-green-600 to-teal-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md">
              {{ authStore.user?.name?.charAt(0) || 'S' }}
            </div>
            <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-green-500 rounded-full border-2 border-white"></div>
          </div>
          <div class="flex-1">
            <p class="font-semibold text-gray-800 text-sm">{{ authStore.user?.name }}</p>
            <p class="text-xs text-gray-500">Siswa PKL</p>
          </div>
        </div>
      </div>

      <!-- Navigation Menu -->
      <nav class="flex-1 px-4 py-4 overflow-y-auto h-[calc(100vh-200px)]">
        <div class="mb-6">
          <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">MAIN</p>
          <router-link to="/siswa/dashboard"
            class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200"
            :class="$route.path === '/siswa/dashboard' ? 'bg-gradient-to-r from-green-600 to-teal-600 text-white shadow-lg' : 'text-gray-600 hover:bg-gray-100'">
            <HomeIcon class="w-5 h-5" />
            <span class="text-sm font-medium">Dashboard</span>
          </router-link>
        </div>

        <div class="mb-6">
          <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">AKTIVITAS PKL</p>
          <router-link to="/siswa/report"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition mb-1">
            <DocumentArrowUpIcon class="w-5 h-5" />
            <span class="text-sm font-medium">Upload Laporan</span>
          </router-link>
          <router-link to="/siswa/attendance"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition mb-1">
            <MapPinIcon class="w-5 h-5" />
            <span class="text-sm font-medium">Absensi GPS</span>
          </router-link>
          <router-link to="/siswa/logbook"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition mb-1">
            <BookOpenIcon class="w-5 h-5" />
            <span class="text-sm font-medium">Logbook Harian</span>
          </router-link>
          <router-link to="/siswa/permission"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition">
            <DocumentTextIcon class="w-5 h-5" />
            <span class="text-sm font-medium">Pengajuan Izin</span>
          </router-link>
        </div>

        <div class="mb-6">
          <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">INFORMASI</p>
          <router-link to="/siswa/company"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition">
            <BuildingOffice2Icon class="w-5 h-5" />
            <span class="text-sm font-medium">Info Perusahaan</span>
          </router-link>
          <router-link to="/siswa/guide"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-100 transition">
            <QuestionMarkCircleIcon class="w-5 h-5" />
            <span class="text-sm font-medium">Panduan PKL</span>
          </router-link>
        </div>
      </nav>

      <!-- Footer -->
      <div class="absolute bottom-0 left-0 right-0 p-4 border-t bg-white">
        <button @click="logout"
          class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 hover:bg-red-50 transition">
          <ArrowRightOnRectangleIcon class="w-5 h-5" />
          <span class="text-sm font-medium">Logout</span>
        </button>
      </div>
    </aside>

    <!-- Overlay -->
    <div v-if="!sidebarCollapsed" @click="toggleSidebar" class="fixed inset-0 bg-black/50 z-30 lg:hidden"></div>

    <!-- Main Content -->
    <div class="transition-all duration-300 lg:ml-72">
      <!-- Top Navbar -->
      <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-lg border-b border-gray-100">
        <div class="flex items-center justify-between px-6 py-4">
          <div class="flex items-center gap-4">
            <button @click="toggleSidebar" class="p-2 rounded-lg hover:bg-gray-100 transition">
              <Bars3Icon class="w-5 h-5 text-gray-600" />
            </button>
            <div>
              <h2 class="text-xl font-bold text-gray-800">{{ pageTitle }}</h2>
              <p class="text-sm text-gray-500">{{ pageDescription }}</p>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <!-- Search Bar -->
            <div class="hidden md:flex items-center bg-gray-100 rounded-xl px-4 py-2">
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <input type="text" placeholder="Cari..." class="bg-transparent border-none focus:outline-none text-sm ml-2 w-48">
            </div>

            <!-- Notification Bell -->
            <div class="relative">
              <button @click="toggleNotif" class="relative p-2 rounded-xl hover:bg-gray-100 transition">
                <BellIcon class="w-5 h-5 text-gray-600" />
                <span v-if="unreadCount > 0" class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
              </button>
              
              <!-- Notification Dropdown -->
              <div v-if="showNotif" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border overflow-hidden z-50">
                <div class="p-3 border-b bg-gradient-to-r from-green-50 to-teal-50">
                  <p class="font-semibold text-gray-800">Notifikasi</p>
                </div>
                <div class="max-h-96 overflow-y-auto">
                  <div v-for="notif in notifications" :key="notif.id" class="p-3 border-b hover:bg-gray-50 transition cursor-pointer">
                    <p class="text-sm font-medium text-gray-800">{{ notif.title }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ notif.message }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ formatTime(notif.created_at) }}</p>
                  </div>
                  <div v-if="notifications.length === 0" class="p-6 text-center text-gray-500">
                    <BellIcon class="w-12 h-12 mx-auto text-gray-300 mb-2" />
                    <p class="text-sm">Tidak ada notifikasi</p>
                  </div>
                </div>
                <div class="p-2 border-t text-center">
                  <router-link to="/siswa/notifications" class="text-xs text-green-600 hover:underline">Lihat semua</router-link>
                </div>
              </div>
            </div>

            <!-- User Menu -->
            <div class="relative">
              <button @click="toggleUserMenu" class="flex items-center gap-2 p-1 rounded-xl hover:bg-gray-100 transition">
                <div class="w-8 h-8 bg-gradient-to-br from-green-600 to-teal-600 rounded-lg flex items-center justify-center text-white text-sm font-bold">
                  {{ authStore.user?.name?.charAt(0) || 'S' }}
                </div>
                <ChevronDownIcon class="w-4 h-4 text-gray-500" />
              </button>
              
              <!-- Dropdown User Menu -->
              <div v-if="userMenuOpen" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border overflow-hidden z-50">
                <div class="p-3 border-b bg-gradient-to-r from-green-50 to-teal-50">
                  <p class="font-semibold text-gray-800">{{ authStore.user?.name }}</p>
                  <p class="text-xs text-gray-500">{{ authStore.user?.email }}</p>
                </div>
                <router-link to="/siswa/profile" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                  <UserIcon class="w-4 h-4 text-gray-500" /> Profile
                </router-link>
                <router-link to="/siswa/notifications" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                  <BellIcon class="w-4 h-4 text-gray-500" /> Notifikasi
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

      <!-- Page Content -->
      <main class="p-6">
        <router-view />
      </main>
    </div>
  </div>
</template>

<script setup>
import { DocumentArrowUpIcon } from '@heroicons/vue/24/outline'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import axios from '../plugins/axios'
import {
  HomeIcon, MapPinIcon, BookOpenIcon, DocumentTextIcon, BuildingOffice2Icon,
  QuestionMarkCircleIcon, BellIcon, UserIcon, ArrowRightOnRectangleIcon,
  Bars3Icon, XMarkIcon, ChevronDownIcon
} from '@heroicons/vue/24/outline'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()

// State
const sidebarCollapsed = ref(false)
const userMenuOpen = ref(false)
const showNotif = ref(false)
const notifications = ref([])
const unreadCount = ref(0)

// Toggle functions
const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value
}

const toggleUserMenu = () => {
  userMenuOpen.value = !userMenuOpen.value
  if (showNotif.value) showNotif.value = false
}

const toggleNotif = () => {
  showNotif.value = !showNotif.value
  if (userMenuOpen.value) userMenuOpen.value = false
}

// Format time
const formatTime = (date) => {
  if (!date) return ''
  const now = new Date()
  const notifDate = new Date(date)
  const diff = Math.floor((now - notifDate) / 1000 / 60)
  
  if (diff < 1) return 'Baru saja'
  if (diff < 60) return `${diff} menit lalu`
  if (diff < 1440) return `${Math.floor(diff / 60)} jam lalu`
  return `${Math.floor(diff / 1440)} hari lalu`
}

// Fetch notifications
const fetchNotifications = async () => {
  try {
    const res = await axios.get('/notifications')
    notifications.value = res.data.data || res.data || []
    unreadCount.value = notifications.value.filter(n => !n.is_read).length
  } catch (error) {
    console.error('Failed to fetch notifications:', error)
  }
}

// Page title
const pageTitle = computed(() => {
  const titles = {
    '/siswa/dashboard': 'Dashboard',
    '/siswa/report': 'Upload Laporan',
    '/siswa/attendance': 'Absensi GPS',
    '/siswa/logbook': 'Logbook Harian',
    '/siswa/permission': 'Pengajuan Izin',
    '/siswa/company': 'Info Perusahaan',
    '/siswa/guide': 'Panduan PKL',
    '/siswa/profile': 'Profile',
    '/siswa/notifications': 'Notifikasi'
  }
  return titles[route.path] || 'Dashboard'
})

const pageDescription = computed(() => {
  const desc = {
    '/siswa/dashboard': 'Ringkasan aktivitas PKL Anda',
    '/siswa/attendance': 'Lakukan absensi dengan GPS sesuai lokasi PKL',
    '/siswa/logbook': 'Catat kegiatan harian Anda selama PKL',
    '/siswa/permission': 'Ajukan izin atau sakit jika tidak dapat hadir'
  }
  return desc[route.path] || ''
})

// Logout
const logout = async () => {
  await authStore.logout()
  router.push('/login')
}

// Click outside handler
const handleClickOutside = (e) => {
  if (!e.target.closest('.relative')) {
    userMenuOpen.value = false
    showNotif.value = false
  }
}

// Lifecycle
onMounted(() => {
  fetchNotifications()
  setInterval(fetchNotifications, 30000)
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
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
</style>