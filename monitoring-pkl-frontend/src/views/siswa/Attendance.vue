<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-gray-800">Absensi PKL</h1>
      <p class="text-gray-500 mt-1">Lakukan check-in/out sesuai lokasi PKL Anda</p>
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
              :disabled="hasCheckedIn || loading"
              class="w-full bg-green-600 text-white py-3 rounded-xl hover:bg-green-700 transition disabled:opacity-50 font-semibold"
            >
              {{ loading ? 'Memproses...' : '📍 Check In' }}
            </button>
            <button 
              @click="checkOut" 
              :disabled="!hasCheckedIn || hasCheckedOut || loading"
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
let watchId = null
let map = null
let userMarker = null
let companyMarker = null
let radiusCircle = null

// Status icon
const statusIcon = computed(() => {
  if (hasCheckedOut.value) return '✅'
  if (hasCheckedIn.value) return '📍'
  return '⭕'
})

// Distance percentage
const distancePercentage = computed(() => {
  if (distance.value >= radius.value) return 100
  return (distance.value / radius.value) * 100
})

// Check if within radius
const isWithinRadius = computed(() => {
  return distance.value <= radius.value
})

// Helper functions
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

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID')
}

// Calculate distance between two coordinates (Haversine formula)
const calculateDistance = (lat1, lon1, lat2, lon2) => {
  const R = 6371000 // Earth radius in meters
  const φ1 = lat1 * Math.PI / 180
  const φ2 = lat2 * Math.PI / 180
  const Δφ = (lat2 - lat1) * Math.PI / 180
  const Δλ = (lon2 - lon1) * Math.PI / 180
  const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) + Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ/2) * Math.sin(Δλ/2)
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a))
  return R * c
}

// Get current position
const getPosition = () => {
  return new Promise((resolve, reject) => {
    if (!navigator.geolocation) {
      reject(new Error('Geolocation tidak didukung browser ini'))
    }
    navigator.geolocation.getCurrentPosition(resolve, reject, {
      enableHighAccuracy: true,
      timeout: 10000,
      maximumAge: 0
    })
  })
}

// Load company data from API
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
    // Fallback ke auth store
    const company = authStore.user?.company
    if (company) {
      companyName.value = company.name
      companyLat.value = company.latitude
      companyLng.value = company.longitude
      radius.value = company.radius || 100
    }
  }
}

// Check In
const checkIn = async () => {
  if (loading.value) return
  
  loading.value = true
  try {
    const position = await getPosition()
    console.log('Position:', position.coords.latitude, position.coords.longitude)
    
    const response = await axios.post('/siswa/attendance/check-in', {
      latitude: position.coords.latitude,
      longitude: position.coords.longitude
    })
    
    console.log('Check-in response:', response.data)
    
    if (response.data.success || response.data.message) {
      toast.success(response.data.message || 'Check in berhasil!')
      await loadTodayStatus()
      await loadHistory()
    } else {
      toast.error(response.data.message || 'Check in gagal')
    }
  } catch (error) {
    console.error('Check in error:', error)
    const message = error.response?.data?.message || 'Check in gagal'
    toast.error(message)
  } finally {
    loading.value = false
  }
}

// Check Out
const checkOut = async () => {
  if (loading.value) return
  
  loading.value = true
  try {
    const position = await getPosition()
    console.log('Position:', position.coords.latitude, position.coords.longitude)
    
    const response = await axios.post('/siswa/attendance/check-out', {
      latitude: position.coords.latitude,
      longitude: position.coords.longitude
    })
    
    console.log('Check-out response:', response.data)
    
    if (response.data.success || response.data.message) {
      toast.success(response.data.message || 'Check out berhasil!')
      await loadTodayStatus()
      await loadHistory()
    } else {
      toast.error(response.data.message || 'Check out gagal')
    }
  } catch (error) {
    console.error('Check out error:', error)
    const message = error.response?.data?.message || 'Check out gagal'
    toast.error(message)
  } finally {
    loading.value = false
  }
}

// Load today status
const loadTodayStatus = async () => {
  try {
    const response = await axios.get('/siswa/attendance/today')
    console.log('Today status:', response.data)
    
    if (response.data.success) {
      hasCheckedIn.value = response.data.has_checked_in || false
      hasCheckedOut.value = response.data.has_checked_out || false
    } else {
      hasCheckedIn.value = response.data.has_checked_in || false
      hasCheckedOut.value = response.data.has_checked_out || false
    }
    
    if (hasCheckedOut.value) {
      statusText.value = 'Selesai'
    } else if (hasCheckedIn.value) {
      statusText.value = 'Sudah Check In'
    } else {
      statusText.value = 'Belum Absen'
    }
  } catch (error) {
    console.error('Load today status error:', error)
    hasCheckedIn.value = false
    hasCheckedOut.value = false
    statusText.value = 'Belum Absen'
  }
}

// Load history
const loadHistory = async () => {
  try {
    const response = await axios.get('/siswa/attendance/history')
    console.log('History response:', response.data)
    
    if (response.data.success) {
      history.value = response.data.data || []
    } else {
      history.value = response.data || []
    }
  } catch (error) {
    console.error('Load history error:', error)
    history.value = []
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

  // Load Leaflet dynamically
  if (!window.L) {
    const script = document.createElement('script')
    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js'
    script.onload = () => {
      const link = document.createElement('link')
      link.rel = 'stylesheet'
      link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'
      document.head.appendChild(link)
      setTimeout(() => createMap(), 100)
    }
    document.head.appendChild(script)
  } else {
    createMap()
  }
}

const createMap = () => {
  const mapContainer = document.getElementById('attendance-map')
  if (!mapContainer || !companyLat.value || !companyLng.value) return

  if (map) map.remove()

  map = window.L.map(mapContainer).setView([companyLat.value, companyLng.value], 16)
  
  window.L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>',
    subdomains: 'abcd',
    maxZoom: 19
  }).addTo(map)

  // Company marker
  companyMarker = window.L.marker([companyLat.value, companyLng.value])
    .addTo(map)
    .bindPopup(`<b>${companyName.value}</b><br>Lokasi PKL Anda`)
    .openPopup()
  
  // Radius circle
  radiusCircle = window.L.circle([companyLat.value, companyLng.value], {
    radius: radius.value,
    color: '#10b981',
    fillColor: '#10b981',
    fillOpacity: 0.1,
    weight: 2
  }).addTo(map)

  // Watch user position
  if (navigator.geolocation) {
    watchId = navigator.geolocation.watchPosition(
      (pos) => {
        const userLat = pos.coords.latitude
        const userLng = pos.coords.longitude
        
        // Calculate distance
        const dist = calculateDistance(userLat, userLng, companyLat.value, companyLng.value)
        distance.value = Math.round(dist)
        
        // Add or update user marker
        if (userMarker) {
          userMarker.setLatLng([userLat, userLng])
        } else {
          const customIcon = window.L.divIcon({
            html: '<div style="background-color: #3b82f6; width: 14px; height: 14px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 4px rgba(0,0,0,0.3);"></div>',
            iconSize: [18, 18],
            className: 'custom-marker'
          })
          userMarker = window.L.marker([userLat, userLng], { icon: customIcon })
            .addTo(map)
            .bindPopup('Anda di sini')
            .openPopup()
        }
        
        // Center map on user if not too far
        if (dist > radius.value * 2) {
          map.setView([userLat, userLng], 14)
        }
      },
      (err) => {
        console.error('Geolocation error:', err)
        if (err.code === 1) {
          toast.error('Izin lokasi ditolak. Aktifkan lokasi untuk absensi.')
        } else {
          toast.error('Gagal mendapatkan lokasi. Pastikan GPS aktif.')
        }
      },
      { enableHighAccuracy: true, maximumAge: 0, timeout: 5000 }
    )
  }
}

onMounted(async () => {
  await loadCompanyInfo()
  await loadTodayStatus()
  await loadHistory()
  setTimeout(() => initMap(), 500)
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

<style scoped>
/* Map container style */
#attendance-map {
  height: 400px;
  width: 100%;
  background: #f0f0f0;
}

/* Custom marker style */
:deep(.custom-marker) {
  background: transparent;
  border: none;
}
</style>