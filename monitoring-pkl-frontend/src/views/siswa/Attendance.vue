<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-gray-800">Absensi PKL</h1>
      <p class="text-gray-500 mt-1">Lakukan check-in/out sesuai lokasi PKL Anda</p>
    </div>

    <!-- Info Izin/Sakit -->
    <div v-if="isPermissionDay" class="bg-blue-50 border border-blue-200 rounded-xl p-4">
      <div class="flex items-center gap-3">
        <span class="text-3xl">{{ permissionType === 'sick' ? '🤒' : '📝' }}</span>
        <div>
          <p class="font-semibold text-blue-800">
            {{ permissionType === 'sick' ? 'Hari ini Anda sedang SAKIT' : 'Hari ini Anda sedang IZIN' }}
          </p>
          <p class="text-sm text-blue-600">Alasan: {{ permissionReason }}</p>
          <p class="text-xs text-blue-500 mt-1">Anda tidak perlu melakukan absensi pada hari ini.</p>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Status Card -->
      <div class="bg-white rounded-2xl shadow-sm p-6">
        <div class="text-center">
          <div class="text-6xl mb-4">{{ statusIcon }}</div>
          <p class="text-xl font-bold">{{ statusText }}</p>
          <p class="text-gray-500 mt-1">{{ currentDate }}</p>
          
          <div class="mt-6 space-y-3">
            <button 
              @click="checkIn" 
              :disabled="hasCheckedIn || loading || isPermissionDay"
              class="w-full bg-green-600 text-white py-3 rounded-xl hover:bg-green-700 transition disabled:opacity-50 font-semibold"
            >
              {{ loading ? 'Memproses...' : '📍 Check In' }}
            </button>
            <button 
              @click="checkOut" 
              :disabled="!hasCheckedIn || hasCheckedOut || loading || isPermissionDay"
              class="w-full bg-red-600 text-white py-3 rounded-xl hover:bg-red-700 transition disabled:opacity-50 font-semibold"
            >
              {{ loading ? 'Memproses...' : '🏁 Check Out' }}
            </button>
          </div>

          <!-- Info Perusahaan -->
          <div class="mt-6 p-4 bg-gray-50 rounded-xl text-left">
            <p class="text-sm font-semibold text-gray-700">Informasi Perusahaan</p>
            <p class="text-sm text-gray-600 mt-1">{{ companyName || 'Belum ada perusahaan' }}</p>
            <p class="text-xs text-gray-500 mt-1">Radius: {{ radius }} meter</p>
            <p class="text-xs text-gray-500">Jarak Anda: {{ distance }} meter</p>
            <div class="mt-2 h-1 bg-gray-200 rounded-full overflow-hidden">
              <div class="h-full bg-indigo-600 rounded-full" :style="{ width: distancePercentage + '%' }"></div>
            </div>
            <div v-if="!isWithinRadius && distance > 0" class="mt-2 text-xs text-red-500">
              ⚠️ Anda berada di luar radius! Jarak {{ distance }}m > {{ radius }}m
            </div>
          </div>
        </div>
      </div>

      <!-- Maps -->
      <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
          <h3 class="font-semibold">Lokasi PKL Anda</h3>
        </div>
        <div id="attendance-map" class="h-96 w-full"></div>
        <div class="p-4 bg-gray-50 text-sm text-gray-500">
          💡 Pastikan GPS menyala dan izinkan akses lokasi untuk melakukan absensi
        </div>
      </div>
    </div>

    <!-- Riwayat Absensi -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
      <div class="p-4 border-b">
        <h3 class="font-semibold">Riwayat Absensi</h3>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-semibold">Tanggal</th>
              <th class="px-4 py-3 text-left text-xs font-semibold">Check In</th>
              <th class="px-4 py-3 text-left text-xs font-semibold">Check Out</th>
              <th class="px-4 py-3 text-left text-xs font-semibold">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y">
            <tr v-for="item in history" :key="item.id">
              <td class="px-4 py-3 text-sm">{{ formatDate(item.date) }}</td>
              <td class="px-4 py-3 text-sm">{{ item.check_in || '-' }}</td>
              <td class="px-4 py-3 text-sm">{{ item.check_out || '-' }}</td>
              <td class="px-4 py-3">
                <span :class="getStatusClass(item.status)" class="px-2 py-1 rounded-full text-xs">
                  {{ getStatusText(item.status) }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()
const authStore = useAuthStore()

// State
const hasCheckedIn = ref(false)
const hasCheckedOut = ref(false)
const loading = ref(false)
const statusText = ref('Belum Absen')
const currentDate = ref(new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }))
const companyName = ref('')
const companyLat = ref(null)
const companyLng = ref(null)
const radius = ref(100)
const distance = ref(0)
const history = ref([])
const isPermissionDay = ref(false)
const permissionType = ref('')
const permissionReason = ref('')
let watchId = null
let map = null
let userMarker = null

// Computed
const statusIcon = computed(() => {
  if (hasCheckedOut.value) return '✅'
  if (hasCheckedIn.value) return '📍'
  if (isPermissionDay.value) return permissionType.value === 'sick' ? '🤒' : '📝'
  return '⭕'
})

const distancePercentage = computed(() => {
  if (distance.value >= radius.value) return 100
  if (radius.value === 0) return 0
  return (distance.value / radius.value) * 100
})

const isWithinRadius = computed(() => {
  return distance.value <= radius.value
})

// Helper functions
const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID')
}

const getStatusText = (status) => {
  const map = { present: 'Hadir', late: 'Terlambat', absent: 'Alpha', sick: 'Sakit', permit: 'Izin' }
  return map[status] || status
}

const getStatusClass = (status) => {
  const map = {
    present: 'bg-green-100 text-green-800',
    late: 'bg-yellow-100 text-yellow-800',
    absent: 'bg-red-100 text-red-800',
    sick: 'bg-blue-100 text-blue-800',
    permit: 'bg-purple-100 text-purple-800'
  }
  return map[status] || 'bg-gray-100 text-gray-800'
}

// Get position
const getPosition = () => {
  return new Promise((resolve, reject) => {
    if (!navigator.geolocation) {
      reject(new Error('Geolocation tidak didukung browser ini'))
    }
    navigator.geolocation.getCurrentPosition(resolve, reject, {
      enableHighAccuracy: true,
      timeout: 10000
    })
  })
}

// Load company info
const loadCompanyInfo = async () => {
  try {
    const response = await axios.get('/siswa/company')
    if (response.data && response.data.company) {
      const company = response.data.company
      companyName.value = company.name
      companyLat.value = company.latitude
      companyLng.value = company.longitude
      radius.value = company.radius || 100
      console.log('Company loaded:', companyName.value, 'Radius:', radius.value)
    }
  } catch (error) {
    console.error('Load company error:', error)
    const company = authStore.user?.company
    if (company) {
      companyName.value = company.name
      companyLat.value = company.latitude
      companyLng.value = company.longitude
      radius.value = company.radius || 100
    }
  }
}

// Load today status
const loadTodayStatus = async () => {
  try {
    const response = await axios.get('/siswa/attendance/today')
    console.log('Today status:', response.data)
    
    if (response.data.is_permission_day) {
      isPermissionDay.value = true
      permissionType.value = response.data.permission_type
      permissionReason.value = response.data.permission_reason
      statusText.value = permissionType.value === 'sick' ? '🤒 Sakit' : '📝 Izin'
      hasCheckedIn.value = false
      hasCheckedOut.value = false
      toast.info(`Hari ini Anda sedang ${permissionType.value === 'sick' ? 'SAKIT' : 'IZIN'}. Tidak perlu absen.`)
    } else {
      isPermissionDay.value = false
      hasCheckedIn.value = response.data.has_checked_in || false
      hasCheckedOut.value = response.data.has_checked_out || false
      if (hasCheckedOut.value) {
        statusText.value = 'Selesai'
      } else if (hasCheckedIn.value) {
        statusText.value = 'Sudah Check In'
      } else {
        statusText.value = 'Belum Absen'
      }
    }
  } catch (error) {
    console.error('Load today status error:', error)
  }
}

// Load history
const loadHistory = async () => {
  try {
    const response = await axios.get('/siswa/attendance/history')
    history.value = response.data.data || response.data || []
    console.log('History loaded:', history.value.length)
  } catch (error) {
    console.error('Load history error:', error)
  }
}

// Check In
const checkIn = async () => {
  if (hasCheckedIn.value) {
    toast.warning('Anda sudah check in hari ini')
    return
  }
  
  if (isPermissionDay.value) {
    toast.warning(`Anda sedang ${permissionType.value === 'sick' ? 'SAKIT' : 'IZIN'} hari ini. Tidak perlu absen.`)
    return
  }
  
  loading.value = true
  try {
    const position = await getPosition()
    const response = await axios.post('/siswa/attendance/check-in', {
      latitude: position.coords.latitude,
      longitude: position.coords.longitude
    })
    
    if (response.data.success) {
      toast.success(response.data.message || 'Check in berhasil!')
      await loadTodayStatus()
      await loadHistory()
    } else {
      toast.error(response.data.message || 'Check in gagal')
    }
  } catch (error) {
    console.error('Check in error:', error)
    toast.error(error.response?.data?.message || 'Check in gagal')
  } finally {
    loading.value = false
  }
}

// Check Out
const checkOut = async () => {
  if (!hasCheckedIn.value) {
    toast.warning('Anda belum check in')
    return
  }
  
  if (hasCheckedOut.value) {
    toast.warning('Anda sudah check out hari ini')
    return
  }
  
  if (isPermissionDay.value) {
    toast.warning(`Anda sedang ${permissionType.value === 'sick' ? 'SAKIT' : 'IZIN'} hari ini.`)
    return
  }
  
  loading.value = true
  try {
    const position = await getPosition()
    const response = await axios.post('/siswa/attendance/check-out', {
      latitude: position.coords.latitude,
      longitude: position.coords.longitude
    })
    
    if (response.data.success) {
      toast.success(response.data.message || 'Check out berhasil!')
      await loadTodayStatus()
      await loadHistory()
    } else {
      toast.error(response.data.message || 'Check out gagal')
    }
  } catch (error) {
    console.error('Check out error:', error)
    toast.error(error.response?.data?.message || 'Check out gagal')
  } finally {
    loading.value = false
  }
}

// Initialize map
const initMap = () => {
  if (!companyLat.value || !companyLng.value) {
    console.log('No company coordinates, waiting...')
    return
  }

  const mapContainer = document.getElementById('attendance-map')
  if (!mapContainer) {
    console.error('Map container not found')
    return
  }

  if (map) map.remove()

  map = L.map(mapContainer).setView([companyLat.value, companyLng.value], 16)
  
  L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>',
    subdomains: 'abcd',
    maxZoom: 19
  }).addTo(map)

  L.marker([companyLat.value, companyLng.value])
    .addTo(map)
    .bindPopup(`<b>${companyName.value}</b><br>Lokasi PKL Anda`)
    .openPopup()
  
  L.circle([companyLat.value, companyLng.value], {
    radius: radius.value,
    color: '#10b981',
    fillColor: '#10b981',
    fillOpacity: 0.1,
    weight: 2
  }).addTo(map)

  if (navigator.geolocation) {
    watchId = navigator.geolocation.watchPosition(
      (pos) => {
        const userLat = pos.coords.latitude
        const userLng = pos.coords.longitude
        const dist = calculateDistance(userLat, userLng, companyLat.value, companyLng.value)
        distance.value = Math.round(dist)
        
        if (userMarker) {
          userMarker.setLatLng([userLat, userLng])
        } else {
          userMarker = L.marker([userLat, userLng])
            .addTo(map)
            .bindPopup('Anda di sini')
        }
      },
      (err) => {
        console.error('Geolocation error:', err)
      },
      { enableHighAccuracy: true, maximumAge: 0 }
    )
  }
}

// Calculate distance
const calculateDistance = (lat1, lon1, lat2, lon2) => {
  const R = 6371000
  const φ1 = lat1 * Math.PI / 180
  const φ2 = lat2 * Math.PI / 180
  const Δφ = (lat2 - lat1) * Math.PI / 180
  const Δλ = (lon2 - lon1) * Math.PI / 180
  const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) + Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ/2) * Math.sin(Δλ/2)
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a))
  return R * c
}

// Load all data
const loadAllData = async () => {
  await loadCompanyInfo()
  await loadTodayStatus()
  await loadHistory()
  setTimeout(() => initMap(), 500)
}

onMounted(() => {
  loadAllData()
})

onUnmounted(() => {
  if (watchId) {
    navigator.geolocation.clearWatch(watchId)
  }
  if (map) {
    map.remove()
    map = null
  }
})
</script>