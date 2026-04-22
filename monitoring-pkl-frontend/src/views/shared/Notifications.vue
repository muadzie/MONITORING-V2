<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Notifikasi</h1>
        <p class="text-gray-500 mt-1">Semua notifikasi Anda</p>
      </div>
      <button 
        @click="markAllAsRead"
        v-if="unreadCount > 0"
        class="px-4 py-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition"
      >
        Tandai semua sudah dibaca
      </button>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl p-4 shadow-sm">
        <p class="text-gray-500 text-sm">Total Notifikasi</p>
        <p class="text-2xl font-bold text-gray-800">{{ notifications.length }}</p>
      </div>
      <div class="bg-white rounded-xl p-4 shadow-sm">
        <p class="text-gray-500 text-sm">Belum Dibaca</p>
        <p class="text-2xl font-bold text-yellow-600">{{ unreadCount }}</p>
      </div>
      <div class="bg-white rounded-xl p-4 shadow-sm">
        <p class="text-gray-500 text-sm">Sudah Dibaca</p>
        <p class="text-2xl font-bold text-green-600">{{ notifications.length - unreadCount }}</p>
      </div>
    </div>

    <!-- Filter Tabs -->
    <div class="border-b">
      <div class="flex space-x-6">
        <button 
          v-for="tab in tabs" 
          :key="tab.value"
          @click="activeTab = tab.value"
          :class="activeTab === tab.value ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500'"
          class="px-1 py-3 border-b-2 font-medium text-sm"
        >
          {{ tab.label }}
        </button>
      </div>
    </div>

    <!-- Notifications List -->
    <div class="space-y-3">
      <div 
        v-for="notif in filteredNotifications" 
        :key="notif.id" 
        @click="markAsRead(notif)"
        class="bg-white rounded-xl shadow-sm p-4 hover:shadow-md transition cursor-pointer"
        :class="{ 'border-l-4 border-indigo-500': !notif.is_read, 'opacity-75': notif.is_read }"
      >
        <div class="flex items-start gap-4">
          <div class="w-10 h-10 rounded-full flex items-center justify-center" :class="getNotifClass(notif.type)">
            <component :is="getNotifIcon(notif.type)" class="w-5 h-5" />
          </div>
          <div class="flex-1">
            <div class="flex justify-between items-start">
              <div>
                <h3 class="font-semibold text-gray-800">{{ notif.title }}</h3>
                <p class="text-gray-600 mt-1">{{ notif.message }}</p>
              </div>
              <span class="text-xs text-gray-400">{{ formatTime(notif.created_at) }}</span>
            </div>
            <div class="flex items-center gap-4 mt-2">
              <span class="text-xs px-2 py-1 rounded-full" :class="getTypeClass(notif.type)">
                {{ notif.type === 'info' ? 'Informasi' : notif.type === 'success' ? 'Sukses' : notif.type === 'warning' ? 'Peringatan' : 'Error' }}
              </span>
              <a v-if="notif.url" :href="notif.url" class="text-xs text-indigo-600 hover:underline">Lihat detail →</a>
            </div>
          </div>
          <div v-if="!notif.is_read" class="w-2 h-2 bg-indigo-600 rounded-full mt-2"></div>
        </div>
      </div>

      <div v-if="!filteredNotifications.length" class="bg-white rounded-xl p-12 text-center text-gray-500">
        <BellIcon class="w-16 h-16 mx-auto text-gray-300 mb-3" />
        <p>Tidak ada notifikasi</p>
        <p class="text-sm mt-1">Notifikasi akan muncul di sini saat ada aktivitas</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'
import { BellIcon, CheckCircleIcon, ExclamationCircleIcon, InformationCircleIcon, XCircleIcon } from '@heroicons/vue/24/outline'

const toast = useToast()
const notifications = ref([])
const activeTab = ref('all')

const tabs = [
  { label: 'Semua', value: 'all' },
  { label: 'Belum Dibaca', value: 'unread' },
  { label: 'Sudah Dibaca', value: 'read' }
]

const unreadCount = computed(() => notifications.value.filter(n => !n.is_read).length)

const filteredNotifications = computed(() => {
  if (activeTab.value === 'unread') {
    return notifications.value.filter(n => !n.is_read)
  }
  if (activeTab.value === 'read') {
    return notifications.value.filter(n => n.is_read)
  }
  return notifications.value
})

const getNotifIcon = (type) => {
  const icons = {
    success: CheckCircleIcon,
    warning: ExclamationCircleIcon,
    error: XCircleIcon,
    info: InformationCircleIcon
  }
  return icons[type] || InformationCircleIcon
}

const getNotifClass = (type) => {
  const classes = {
    success: 'bg-green-100 text-green-600',
    warning: 'bg-yellow-100 text-yellow-600',
    error: 'bg-red-100 text-red-600',
    info: 'bg-blue-100 text-blue-600'
  }
  return classes[type] || 'bg-gray-100 text-gray-600'
}

const getTypeClass = (type) => {
  const classes = {
    success: 'bg-green-100 text-green-800',
    warning: 'bg-yellow-100 text-yellow-800',
    error: 'bg-red-100 text-red-800',
    info: 'bg-blue-100 text-blue-800'
  }
  return classes[type] || 'bg-gray-100 text-gray-800'
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
    notifications.value = res.data.notifications || res.data
  } catch (error) {
    console.error('Failed to fetch notifications:', error)
  }
}

const markAsRead = async (notif) => {
  if (!notif.is_read) {
    try {
      await axios.put(`/notifications/${notif.id}/read`)
      notif.is_read = true
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
    toast.success('Semua notifikasi ditandai sudah dibaca')
  } catch (error) {
    toast.error('Gagal menandai notifikasi')
  }
}

onMounted(() => {
  fetchNotifications()
})
</script>