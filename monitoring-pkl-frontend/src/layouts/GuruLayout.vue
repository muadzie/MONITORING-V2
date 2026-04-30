<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside 
      class="fixed left-0 top-0 z-40 h-screen bg-white shadow-2xl transition-all duration-300"
      :class="[
        sidebarCollapsed ? '-translate-x-full lg:w-20' : 'translate-x-0 w-72',
        'lg:translate-x-0'
      ]"
    >
      <!-- Logo Section -->
      <div class="flex items-center justify-between px-6 py-5 border-b" :class="sidebarCollapsed ? 'justify-center px-3' : ''">
        <div class="flex items-center gap-3" :class="sidebarCollapsed ? 'flex-col' : ''">
          <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
          </div>
          <div v-if="!sidebarCollapsed">
            <h1 class="text-xl font-bold text-gray-800">Guru Panel</h1>
            <p class="text-xs text-gray-500">SMKN 1 Subang</p>
          </div>
        </div>
        <button 
          @click="toggleSidebar" 
          class="lg:hidden text-gray-500 hover:text-gray-700 transition"
          :class="sidebarCollapsed ? 'absolute right-2 top-5' : ''"
        >
          <XMarkIcon class="w-6 h-6" />
        </button>
      </div>

      <!-- User Profile -->
      <div class="p-4 m-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl" :class="sidebarCollapsed ? 'mx-2 p-2' : ''">
        <div class="flex items-center gap-3" :class="sidebarCollapsed ? 'flex-col text-center' : ''">
          <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-md flex-shrink-0">
            {{ authStore.user?.name?.charAt(0) || 'G' }}
          </div>
          <div v-if="!sidebarCollapsed" class="flex-1 min-w-0">
            <p class="font-semibold text-gray-800 text-sm truncate">{{ authStore.user?.name }}</p>
            <p class="text-xs text-gray-500">Guru Pembimbing</p>
          </div>
        </div>
      </div>

      <!-- Navigation Menu -->
      <nav class="flex-1 px-4 py-4 overflow-y-auto h-[calc(100vh-200px)]" :class="sidebarCollapsed ? 'px-2' : ''">
        <router-link 
          to="/guru/dashboard" 
          class="flex items-center gap-3 px-4 py-3 rounded-xl mb-1 transition-all duration-200"
          :class="[
            $route.path === '/guru/dashboard' 
              ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-md' 
              : 'text-gray-600 hover:bg-gray-100',
            sidebarCollapsed ? 'justify-center px-2' : ''
          ]"
          :title="sidebarCollapsed ? 'Dashboard' : ''"
        >
          <HomeIcon class="w-5 h-5 flex-shrink-0" />
          <span v-if="!sidebarCollapsed">Dashboard</span>
        </router-link>

        <router-link 
          to="/guru/monitoring" 
          class="flex items-center gap-3 px-4 py-3 rounded-xl mb-1 transition-all duration-200"
          :class="[
            $route.path === '/guru/monitoring' 
              ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-md' 
              : 'text-gray-600 hover:bg-gray-100',
            sidebarCollapsed ? 'justify-center px-2' : ''
          ]"
          :title="sidebarCollapsed ? 'Monitoring Siswa' : ''"
        >
          <UserGroupIcon class="w-5 h-5 flex-shrink-0" />
          <span v-if="!sidebarCollapsed">Monitoring Siswa</span>
        </router-link>

        <router-link 
          to="/guru/attendance-monitoring" 
          class="flex items-center gap-3 px-4 py-3 rounded-xl mb-1 transition-all duration-200"
          :class="[
            $route.path === '/guru/attendance-monitoring' 
              ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-md' 
              : 'text-gray-600 hover:bg-gray-100',
            sidebarCollapsed ? 'justify-center px-2' : ''
          ]"
          :title="sidebarCollapsed ? 'Monitoring Absensi' : ''"
        >
          <ClipboardDocumentListIcon class="w-5 h-5 flex-shrink-0" />
          <span v-if="!sidebarCollapsed">Monitoring Absensi</span>
        </router-link>

        <router-link 
          to="/guru/logbook-review" 
          class="flex items-center gap-3 px-4 py-3 rounded-xl mb-1 transition-all duration-200"
          :class="[
            $route.path === '/guru/logbook-review' 
              ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-md' 
              : 'text-gray-600 hover:bg-gray-100',
            sidebarCollapsed ? 'justify-center px-2' : ''
          ]"
          :title="sidebarCollapsed ? 'Review Logbook' : ''"
        >
          <BookOpenIcon class="w-5 h-5 flex-shrink-0" />
          <span v-if="!sidebarCollapsed">Review Logbook</span>
        </router-link>

        <router-link 
          to="/guru/permissions" 
          class="flex items-center gap-3 px-4 py-3 rounded-xl mb-1 transition-all duration-200"
          :class="[
            $route.path === '/guru/permissions' 
              ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-md' 
              : 'text-gray-600 hover:bg-gray-100',
            sidebarCollapsed ? 'justify-center px-2' : ''
          ]"
          :title="sidebarCollapsed ? 'Approval Izin' : ''"
        >
          <DocumentTextIcon class="w-5 h-5 flex-shrink-0" />
          <span v-if="!sidebarCollapsed">Approval Izin</span>
        </router-link>

        <router-link 
          to="/guru/report-review" 
          class="flex items-center gap-3 px-4 py-3 rounded-xl mb-1 transition-all duration-200"
          :class="[
            $route.path === '/guru/report-review' 
              ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-md' 
              : 'text-gray-600 hover:bg-gray-100',
            sidebarCollapsed ? 'justify-center px-2' : ''
          ]"
          :title="sidebarCollapsed ? 'Review Laporan PKL' : ''"
        >
          <DocumentChartBarIcon class="w-5 h-5 flex-shrink-0" />
          <span v-if="!sidebarCollapsed">Review Laporan PKL</span>
        </router-link>

        <router-link 
          to="/guru/assessment" 
          class="flex items-center gap-3 px-4 py-3 rounded-xl mb-1 transition-all duration-200"
          :class="[
            $route.path === '/guru/assessment' 
              ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-md' 
              : 'text-gray-600 hover:bg-gray-100',
            sidebarCollapsed ? 'justify-center px-2' : ''
          ]"
          :title="sidebarCollapsed ? 'Penilaian PKL' : ''"
        >
          <ClipboardDocumentListIcon class="w-5 h-5 flex-shrink-0" />
          <span v-if="!sidebarCollapsed">Penilaian PKL</span>
        </router-link>
        
        <router-link 
          to="/guru/rekap-absensi" 
          class="flex items-center gap-3 px-4 py-3 rounded-xl mb-1 transition-all duration-200"
          :class="[
            $route.path === '/guru/rekap-absensi' 
              ? 'bg-gradient-to-r from-blue-600 to-cyan-600 text-white shadow-md' 
              : 'text-gray-600 hover:bg-gray-100',
            sidebarCollapsed ? 'justify-center px-2' : ''
          ]"
          :title="sidebarCollapsed ? 'Rekap Absensi' : ''"
        >
          <DocumentChartBarIcon class="w-5 h-5 flex-shrink-0" />
          <span v-if="!sidebarCollapsed">Rekap Absensi</span>
        </router-link>
      </nav>

      <!-- Footer -->
      <div class="absolute bottom-0 left-0 right-0 p-4 border-t bg-white" :class="sidebarCollapsed ? 'p-2' : ''">
        <button 
          @click="logout" 
          class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-red-600 hover:bg-red-50 transition"
          :class="sidebarCollapsed ? 'justify-center px-2' : ''"
          :title="sidebarCollapsed ? 'Logout' : ''"
        >
          <ArrowRightOnRectangleIcon class="w-5 h-5 flex-shrink-0" />
          <span v-if="!sidebarCollapsed">Logout</span>
        </button>
      </div>
    </aside>

    <!-- Overlay untuk mobile -->
    <div 
      v-if="sidebarCollapsed && windowWidth < 1024" 
      @click="toggleSidebar" 
      class="fixed inset-0 bg-black/50 z-30 lg:hidden"
    ></div>

    <!-- Main Content -->
    <div 
      class="transition-all duration-300"
      :class="[
        !sidebarCollapsed ? 'lg:ml-72' : 'lg:ml-20'
      ]"
    >
      <!-- Top Navbar -->
      <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-lg border-b border-gray-100">
        <div class="flex items-center justify-between px-6 py-4">
          <div class="flex items-center gap-4">
            <!-- Burger Menu Button -->
            <button 
              @click="toggleSidebar" 
              class="p-2 rounded-lg hover:bg-gray-100 transition-all duration-200 focus:outline-none"
            >
              <svg 
                v-if="sidebarCollapsed" 
                class="w-6 h-6 text-gray-600" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
              </svg>
              <svg 
                v-else 
                class="w-6 h-6 text-gray-600" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
            <div>
              <h2 class="text-xl font-bold text-gray-800">{{ pageTitle }}</h2>
              <p class="text-sm text-gray-500">{{ pageDescription }}</p>
            </div>
          </div>
          
          <div class="flex items-center gap-3">
            <!-- Search Bar (Desktop) -->
            <div class="hidden md:flex items-center bg-gray-100 rounded-xl px-4 py-2">
              <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
              <input type="text" placeholder="Cari..." class="bg-transparent border-none focus:outline-none text-sm ml-2 w-48">
            </div>
            
            <!-- Notification Bell -->
            <div class="relative">
              <button @click="toggleNotif" class="relative p-2 rounded-xl hover:bg-gray-100 transition">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <span v-if="unreadCount > 0" class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
              </button>
              
              <!-- Notification Dropdown -->
              <div v-if="showNotif" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border overflow-hidden z-50">
                <div class="p-3 border-b bg-gradient-to-r from-blue-50 to-cyan-50">
                  <p class="font-semibold text-gray-800">Notifikasi</p>
                </div>
                <div class="max-h-96 overflow-y-auto">
                  <div v-for="notif in notifications" :key="notif.id" class="p-3 border-b hover:bg-gray-50 transition cursor-pointer">
                    <p class="text-sm font-medium text-gray-800">{{ notif.title }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ notif.message }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ formatTime(notif.created_at) }}</p>
                  </div>
                  <div v-if="notifications.length === 0" class="p-6 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <p class="text-sm">Tidak ada notifikasi</p>
                  </div>
                </div>
                <div class="p-2 border-t text-center">
                  <router-link to="/guru/notifications" class="text-xs text-blue-600 hover:underline">Lihat semua</router-link>
                </div>
              </div>
            </div>
            
            <!-- User Menu -->
            <div class="relative">
              <button @click="toggleUserMenu" class="flex items-center gap-2 p-1 rounded-xl hover:bg-gray-100 transition group">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-cyan-600 rounded-lg flex items-center justify-center text-white text-sm font-bold shadow-md">
                  {{ authStore.user?.name?.charAt(0) || 'G' }}
                </div>
                <ChevronDownIcon class="w-4 h-4 text-gray-500 hidden md:block transition-transform duration-200" :class="{ 'rotate-180': userMenuOpen }" />
              </button>
              
              <!-- Dropdown User Menu -->
              <transition name="fade-down">
                <div v-if="userMenuOpen" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border overflow-hidden z-50">
                  <div class="p-3 border-b bg-gradient-to-r from-blue-50 to-cyan-50">
                    <p class="font-semibold text-gray-800">{{ authStore.user?.name }}</p>
                    <p class="text-xs text-gray-500">{{ authStore.user?.email }}</p>
                  </div>
                  <router-link to="/guru/profile" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    Profile
                  </router-link>
                  <router-link to="/guru/settings" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Settings
                  </router-link>
                  <router-link to="/guru/notifications" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 transition">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    Notifikasi
                  </router-link>
                  <hr>
                  <button @click="logout" class="w-full flex items-center gap-3 px-4 py-3 hover:bg-red-50 text-red-600 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Logout
                  </button>
                </div>
              </transition>
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
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { 
  HomeIcon, 
  UserGroupIcon, 
  BookOpenIcon, 
  DocumentTextIcon, 
  DocumentChartBarIcon, 
  ClipboardDocumentListIcon, 
  ArrowRightOnRectangleIcon,
  XMarkIcon,
  ChevronDownIcon
} from '@heroicons/vue/24/outline'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

// State
const sidebarCollapsed = ref(false)
const windowWidth = ref(window.innerWidth)
const userMenuOpen = ref(false)
const showNotif = ref(false)
const notifications = ref([])
const unreadCount = ref(0)

// Fungsi toggle sidebar
const toggleSidebar = () => {
  sidebarCollapsed.value = !sidebarCollapsed.value
  localStorage.setItem('sidebarCollapsed', sidebarCollapsed.value)
}

const toggleUserMenu = () => {
  userMenuOpen.value = !userMenuOpen.value
  if (showNotif.value) showNotif.value = false
}

const toggleNotif = () => {
  showNotif.value = !showNotif.value
  if (userMenuOpen.value) userMenuOpen.value = false
}

// Format waktu
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

// Fetch notifikasi
const fetchNotifications = async () => {
  try {
    const { data } = await axios.get('/notifications')
    notifications.value = data.data || []
    unreadCount.value = notifications.value.filter(n => !n.is_read).length
  } catch (error) {
    console.error('Failed to fetch notifications:', error)
  }
}

// Handle resize
const handleResize = () => {
  windowWidth.value = window.innerWidth
  if (windowWidth.value >= 1024) {
    const savedState = localStorage.getItem('sidebarCollapsed')
    sidebarCollapsed.value = savedState === 'true'
  } else {
    sidebarCollapsed.value = true
  }
}

// Page title
const pageTitle = computed(() => {
  const titles = {
    '/guru/dashboard': 'Dashboard',
    '/guru/monitoring': 'Monitoring Siswa',
    '/guru/attendance-monitoring': 'Monitoring Absensi',
    '/guru/logbook-review': 'Review Logbook',
    '/guru/permissions': 'Approval Izin',
    '/guru/report-review': 'Review Laporan PKL',
    '/guru/assessment': 'Penilaian PKL',
    '/guru/rekap-absensi': 'Rekap Absensi',
    '/guru/profile': 'Profile',
    '/guru/settings': 'Pengaturan',
    '/guru/notifications': 'Notifikasi'
  }
  return titles[route.path] || 'Dashboard'
})

const pageDescription = computed(() => {
  const desc = {
    '/guru/dashboard': 'Ringkasan aktivitas bimbingan PKL',
    '/guru/monitoring': 'Pantau aktivitas dan progres siswa',
    '/guru/attendance-monitoring': 'Pantau kehadiran siswa PKL',
    '/guru/report-review': 'Review dan download laporan PKL siswa'
  }
  return desc[route.path] || ''
})

// Logout
const logout = async () => {
  await authStore.logout()
  router.push('/login')
}

// Lifecycle
onMounted(() => {
  handleResize()
  window.addEventListener('resize', handleResize)
  fetchNotifications()
  // Refresh notifikasi setiap 30 detik
  setInterval(fetchNotifications, 30000)
})

onUnmounted(() => {
  window.removeEventListener('resize', handleResize)
})

// Click outside handler
onMounted(() => {
  document.addEventListener('click', (e) => {
    if (!e.target.closest('.relative')) {
      userMenuOpen.value = false
      showNotif.value = false
    }
  })
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

/* Transisi */
aside {
  transition: all 0.3s ease-in-out;
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
</style>