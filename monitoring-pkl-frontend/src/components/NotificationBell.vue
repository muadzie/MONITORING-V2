<template>
  <div class="relative">
    <button 
      @click="toggleDropdown" 
      class="relative p-2 rounded-xl hover:bg-gray-100 transition"
    >
      <BellIcon class="w-5 h-5 text-gray-600" />
      <span v-if="unreadCount > 0" class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
    </button>

    <!-- Dropdown Notifikasi -->
    <div v-if="isOpen" class="absolute right-0 mt-2 w-96 bg-white rounded-xl shadow-xl border overflow-hidden z-50">
      <div class="p-3 border-b flex justify-between items-center">
        <h3 class="font-semibold text-gray-800">Notifikasi</h3>
        <button @click="markAllAsRead" class="text-xs text-indigo-600 hover:underline">Tandai semua sudah dibaca</button>
      </div>
      
      <div class="max-h-96 overflow-y-auto">
        <div v-for="notif in notifications" :key="notif.id" 
          @click="markAsRead(notif)"
          class="p-3 border-b hover:bg-gray-50 cursor-pointer transition"
          :class="{ 'bg-indigo-50': !notif.is_read }"
        >
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-full flex items-center justify-center" :class="getNotifIconClass(notif.type)">
              <component :is="getNotifIcon(notif.type)" class="w-4 h-4" />
            </div>
            <div class="flex-1">
              <p class="text-sm font-medium text-gray-800">{{ notif.title }}</p>
              <p class="text-xs text-gray-500 mt-1">{{ notif.message }}</p>
              <p class="text-xs text-gray-400 mt-1">{{ formatTime(notif.created_at) }}</p>
            </div>
            <div v-if="!notif.is_read" class="w-2 h-2 bg-indigo-600 rounded-full mt-2"></div>
          </div>
        </div>
        
        <div v-if="!notifications.length" class="p-8 text-center text-gray-500">
          <BellIcon class="w-12 h-12 mx-auto text-gray-300 mb-2" />
          <p class="text-sm">Tidak ada notifikasi</p>
        </div>
      </div>
      
      <div class="p-2 border-t">
        <router-link to="/notifications" class="block text-center text-sm text-indigo-600 py-2 hover:bg-gray-50 rounded-lg">
          Lihat semua notifikasi
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import axios from '../plugins/axios'
import { BellIcon, CheckCircleIcon, ExclamationCircleIcon, InformationCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline'

const isOpen = ref(false)
const notifications = ref([])
const unreadCount = ref(0)

const getNotifIcon = (type) => {
  const icons = {
    success: CheckCircleIcon,
    warning: ExclamationCircleIcon,
    error: XCircleIcon,
    info: InformationCircleIcon
  }
  return icons[type] || InformationCircleIcon
}

const getNotifIconClass = (type) => {
  const classes = {
    success: 'bg-green-100 text-green-600',
    warning: 'bg-yellow-100 text-yellow-600',
    error: 'bg-red-100 text-red-600',
    info: 'bg-blue-100 text-blue-600'
  }
  return classes[type] || 'bg-gray-100 text-gray-600'
}

const formatTime = (date) => {
  const diff = new Date() - new Date(date)
  const minutes = Math.floor(diff / 60000)
  const hours = Math.floor(diff / 3600000)
  const days = Math.floor(diff / 86400000)
  
  if (minutes < 1) return 'Baru saja'
  if (minutes < 60) return `${minutes} menit lalu`
  if (hours < 24) return `${hours} jam lalu`
  return `${days} hari lalu`
}

const fetchNotifications = async () => {
  try {
    const res = await axios.get('/notifications')
    notifications.value = res.data.notifications
    unreadCount.value = res.data.unread_count
  } catch (error) {
    console.error('Failed to fetch notifications:', error)
  }
}

const markAsRead = async (notif) => {
  if (!notif.is_read) {
    try {
      await axios.put(`/notifications/${notif.id}/read`)
      notif.is_read = true
      unreadCount.value--
      if (notif.url) {
        window.location.href = notif.url
      }
    } catch (error) {
      console.error('Failed to mark as read:', error)
    }
  } else if (notif.url) {
    window.location.href = notif.url
  }
}

const markAllAsRead = async () => {
  try {
    await axios.put('/notifications/read-all')
    notifications.value.forEach(n => n.is_read = true)
    unreadCount.value = 0
  } catch (error) {
    console.error('Failed to mark all as read:', error)
  }
}

const toggleDropdown = () => {
  isOpen.value = !isOpen.value
  if (isOpen.value) {
    fetchNotifications()
  }
}

// Close dropdown when clicking outside
const handleClickOutside = (e) => {
  if (!e.target.closest('.relative')) {
    isOpen.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
  // Fetch every 30 seconds
  setInterval(fetchNotifications, 30000)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>