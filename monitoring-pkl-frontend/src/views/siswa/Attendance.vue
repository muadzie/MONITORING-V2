<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-gray-800">Absensi PKL + Logbook</h1>
      <p class="text-gray-500 mt-1">Scan foto, absensi GPS, dan catat kegiatan harian</p>
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

    <!-- Info Hari Libur Perusahaan -->
    <div v-if="isHoliday" class="bg-amber-50 border border-amber-200 rounded-xl p-4">
      <div class="flex items-center gap-3">
        <span class="text-3xl">🎉</span>
        <div>
          <p class="font-semibold text-amber-800">Hari ini adalah hari libur!</p>
          <p class="text-sm text-amber-600">{{ holidayDescription || 'Libur perusahaan' }}</p>
          <p class="text-xs text-amber-500 mt-1">Selamat berlibur! Tidak perlu melakukan absensi.</p>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Left Column: Camera & Status -->
      <div class="lg:col-span-1 space-y-6">
        <!-- Camera / Selfie Card -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
          <div class="p-4 border-b bg-gray-50 flex items-center justify-between">
            <h3 class="font-semibold text-gray-700 flex items-center gap-2">
              <CameraIcon class="w-5 h-5 text-emerald-600" />
              Scan Foto Selfie
            </h3>
            <span v-if="capturedPhoto" class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full">✓ Terfoto</span>
          </div>
          <div class="p-4">
            <!-- Video / Camera Preview -->
            <div v-if="!capturedPhoto" class="relative">
              <video ref="videoRef" autoplay playsinline class="w-full h-64 bg-gray-900 rounded-xl object-cover"></video>
              <div v-if="!cameraReady" class="absolute inset-0 flex items-center justify-center bg-gray-900/80 rounded-xl">
                <div class="text-center text-white">
                  <CameraIcon class="w-12 h-12 mx-auto mb-2 animate-pulse" />
                  <p class="text-sm">Menyiapkan kamera...</p>
                </div>
              </div>
              <button @click="capturePhoto" :disabled="!cameraReady"
                class="absolute bottom-4 left-1/2 -translate-x-1/2 w-14 h-14 bg-white rounded-full flex items-center justify-center shadow-lg hover:scale-105 transition border-4 border-emerald-500">
                <div class="w-10 h-10 bg-emerald-500 rounded-full"></div>
              </button>
            </div>
            <!-- Captured Photo Preview -->
            <div v-else class="relative">
              <img :src="capturedPhoto" class="w-full h-64 rounded-xl object-cover" />
              <button @click="retakePhoto"
                class="absolute top-2 right-2 bg-white/90 rounded-full p-2 shadow hover:bg-white transition">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
              </button>
            </div>
            <p class="text-xs text-gray-500 mt-2 text-center">
              {{ capturedPhoto ? 'Foto siap dikirim saat absensi' : 'Ambil foto selfie untuk bukti absensi' }}
            </p>
          </div>
        </div>

        <!-- Status Card -->
        <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
          <div class="text-6xl mb-4">{{ statusIcon }}</div>
          <p class="text-xl font-bold">{{ statusText }}</p>
          <p class="text-gray-500 mt-1">{{ currentDate }}</p>
          
          <div class="mt-6 space-y-3">
            <button 
              @click="checkIn" 
              :disabled="hasCheckedIn || loading || isPermissionDay || isHoliday || !cameraReady"
              class="w-full bg-gradient-to-r from-emerald-500 to-teal-600 text-white py-3 rounded-xl hover:shadow-lg transition-all disabled:opacity-50 font-semibold flex items-center justify-center gap-2"
            >
              <CameraIcon v-if="!hasCheckedIn" class="w-5 h-5" />
              {{ loading ? 'Memproses...' : (hasCheckedIn ? 'Sudah Check In ✓' : '📍 Check In + Foto') }}
            </button>
            <button 
              @click="checkOut" 
              :disabled="!hasCheckedIn || hasCheckedOut || loading || isPermissionDay || isHoliday"
              class="w-full bg-gradient-to-r from-red-500 to-red-600 text-white py-3 rounded-xl hover:shadow-lg transition-all disabled:opacity-50 font-semibold"
            >
              {{ loading ? 'Memproses...' : (hasCheckedOut ? 'Selesai ✓' : '🏁 Check Out + Foto') }}
            </button>
          </div>

          <!-- Company Info -->
          <div class="mt-4 p-3 bg-gray-50 rounded-xl text-left text-sm">
            <div class="flex justify-between">
              <span class="text-gray-500">Perusahaan:</span>
              <span class="font-medium">{{ companyName || '-' }}</span>
            </div>
            <div class="flex justify-between mt-1">
              <span class="text-gray-500">Jarak:</span>
              <span class="font-medium" :class="isWithinRadius ? 'text-green-600' : 'text-red-600'">{{ distance }}m / {{ radius }}m</span>
            </div>
            <div class="mt-1 h-1.5 bg-gray-200 rounded-full overflow-hidden">
              <div class="h-full rounded-full transition-all" :class="isWithinRadius ? 'bg-green-500' : 'bg-red-500'" :style="{ width: distancePercentage + '%' }"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Middle & Right: Map + Logbook -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Map -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
          <div class="p-4 border-b bg-gray-50 flex items-center justify-between">
            <h3 class="font-semibold flex items-center gap-2">
              <MapPinIcon class="w-5 h-5 text-emerald-600" />
              Lokasi PKL
            </h3>
            <span class="text-xs text-gray-500">{{ isWithinRadius ? '📍 Dalam radius' : '⚠️ Luar radius' }}</span>
          </div>
          <div id="attendance-map" class="h-64 w-full"></div>
        </div>

        <!-- Logbook Entry (combined with attendance) -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
          <div class="p-4 border-b bg-gray-50 flex items-center justify-between">
            <h3 class="font-semibold flex items-center gap-2">
              <BookOpenIcon class="w-5 h-5 text-emerald-600" />
              Catat Kegiatan Hari Ini
            </h3>
            <span class="text-xs text-gray-400">Opsional, isi saat check in</span>
          </div>
          <div class="p-4">
            <div class="space-y-3">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kegiatan</label>
                <input v-model="logbookActivity" type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Contoh: Membantu installasi jaringan" :disabled="hasCheckedOut">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi (opsional)</label>
                <textarea v-model="logbookDescription" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-transparent" placeholder="Deskripsi kegiatan hari ini..." :disabled="hasCheckedOut"></textarea>
              </div>
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
                  <th class="px-4 py-3 text-left text-xs font-semibold">Foto</th>
                </tr>
              </thead>
              <tbody class="divide-y">
                <tr v-for="item in history" :key="item.id">
                  <td class="px-4 py-3 text-sm">{{ formatDate(item.date) }}</td>
                  <td class="px-4 py-3 text-sm">{{ formatTime(item.check_in) || '-' }}</td>
                  <td class="px-4 py-3 text-sm">{{ formatTime(item.check_out) || '-' }}</td>
                  <td class="px-4 py-3">
                    <span :class="getStatusClass(item.status)" class="px-2 py-1 rounded-full text-xs">
                      {{ getStatusText(item.status) }}
                    </span>
                  </td>
                  <td class="px-4 py-3">
                    <button v-if="item.photo" @click="previewPhoto(item.photo, 'Foto Check In')" class="text-emerald-600 hover:text-emerald-800 text-xs underline mr-2">
                      Check In
                    </button>
                    <button v-if="item.photo_out" @click="previewPhoto(item.photo_out, 'Foto Check Out')" class="text-blue-600 hover:text-blue-800 text-xs underline">
                      Check Out
                    </button>
                    <span v-if="!item.photo && !item.photo_out" class="text-gray-400 text-xs">-</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Photo Preview Modal -->
    <div v-if="showPhotoPreview" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50" @click.self="showPhotoPreview = false">
      <div class="max-w-lg w-full mx-4">
        <p class="text-white text-sm text-center mb-2">{{ previewPhotoLabel }}</p>
        <img :src="previewPhotoUrl" class="w-full rounded-xl shadow-2xl" />
        <button @click="showPhotoPreview = false" class="mt-4 w-full bg-white/20 text-white py-2 rounded-xl hover:bg-white/30 transition">Tutup</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'
import { CameraIcon, MapPinIcon, BookOpenIcon } from '@heroicons/vue/24/outline'

const toast = useToast()
const authStore = useAuthStore()

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
const isHoliday = ref(false)
const holidayDescription = ref('')

// Camera
const videoRef = ref(null)
const cameraReady = ref(false)
const capturedPhoto = ref(null)
let cameraStream = null

// Logbook
const logbookActivity = ref('')
const logbookDescription = ref('')

// Photo preview
const showPhotoPreview = ref(false)
const previewPhotoUrl = ref('')
const previewPhotoLabel = ref('')

let map = null
let watchId = null
let userMarker = null

const previewPhoto = (photo, label = '') => {
  if (!photo) return
  previewPhotoLabel.value = label
  if (photo.startsWith('http')) {
    previewPhotoUrl.value = photo
  } else if (photo.startsWith('/storage/')) {
    previewPhotoUrl.value = photo
  } else if (photo.startsWith('data:')) {
    previewPhotoUrl.value = photo
  } else {
    previewPhotoUrl.value = '/storage/' + photo
  }
  showPhotoPreview.value = true
}

const statusIcon = computed(() => {
  if (isPermissionDay.value) return permissionType.value === 'sick' ? '🤒' : '📝'
  if (isHoliday.value) return '🎉'
  if (hasCheckedOut.value) return '✅'
  if (hasCheckedIn.value) return '📍'
  return '⏳'
})

const isWithinRadius = computed(() => distance.value <= radius.value)

const distancePercentage = computed(() => {
  if (radius.value <= 0) return 0
  return Math.min(100, Math.round((distance.value / radius.value) * 100))
})

const formatDate = (date) => {
  if (!date) return '-'
  const d = new Date(date)
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

const formatTime = (time) => {
  if (!time) return null
  if (typeof time === 'string' && time.includes(':')) {
    return time.substring(0, 5)
  }
  return new Date(time).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })
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

const getPosition = () => {
  return new Promise((resolve, reject) => {
    if (!navigator.geolocation) {
      reject(new Error('Geolocation tidak didukung browser ini'))
    }
    navigator.geolocation.getCurrentPosition(resolve, reject, {
      enableHighAccuracy: true,
      timeout: 15000,
      maximumAge: 30000
    })
  })
}

const getBase64FromDataUrl = (dataUrl) => {
  return dataUrl.split(',')[1] || null
}

const startCamera = async () => {
  try {
    cameraStream = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: 'user', width: { ideal: 320 }, height: { ideal: 240 } },
      audio: false
    })
    if (videoRef.value) {
      videoRef.value.srcObject = cameraStream
      cameraReady.value = true
    }
  } catch (err) {
    console.error('Camera error:', err)
    cameraReady.value = false
  }
}

const capturePhoto = () => {
  if (!videoRef.value || !cameraReady.value) {
    toast.warning('Kamera belum siap')
    return
  }
  const canvas = document.createElement('canvas')
  canvas.width = 320
  canvas.height = 240
  const ctx = canvas.getContext('2d')
  ctx.drawImage(videoRef.value, 0, 0, 320, 240)
  capturedPhoto.value = canvas.toDataURL('image/jpeg', 0.5)
}

const stopCamera = () => {
  if (cameraStream) {
    cameraStream.getTracks().forEach(track => track.stop())
    cameraStream = null
  }
  cameraReady.value = false
}

const retakePhoto = () => {
  capturedPhoto.value = null
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
    const today = new Date().toISOString().split('T')[0]

    if (response.data.is_holiday) {
      isHoliday.value = true
      isPermissionDay.value = false
      holidayDescription.value = response.data.holiday_description || ''
      statusText.value = '🎉 Libur'
    } else if (response.data.is_permission_day) {
      isHoliday.value = false
      if (response.data.permission_date && response.data.permission_date !== today) {
        isPermissionDay.value = false
      } else {
        isPermissionDay.value = true
        permissionType.value = response.data.permission_type
        permissionReason.value = response.data.permission_reason
        statusText.value = permissionType.value === 'sick' ? '🤒 Sakit' : '📝 Izin'
      }
    } else {
      isHoliday.value = false
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
  } catch (error) {
    console.error('Load history error:', error)
  }
}

const captureFrame = () => {
  if (!videoRef.value || !cameraReady.value) return null
  const canvas = document.createElement('canvas')
  canvas.width = 320
  canvas.height = 240
  const ctx = canvas.getContext('2d')
  ctx.drawImage(videoRef.value, 0, 0, 320, 240)
  return canvas.toDataURL('image/jpeg', 0.5)
}

// Check In
const checkIn = async () => {
  if (hasCheckedIn.value) {
    toast.warning('Anda sudah check in hari ini')
    return
  }

  loading.value = true
  try {
    const position = await getPosition()
    const payload = {
      latitude: position.coords.latitude,
      longitude: position.coords.longitude,
    }

    const frame = capturedPhoto.value || (cameraReady.value ? captureFrame() : null)
    if (frame) {
      payload.photo = getBase64FromDataUrl(frame)
    }
    capturedPhoto.value = null

    if (logbookActivity.value) {
      payload.logbook_activity = logbookActivity.value
      payload.logbook_description = logbookDescription.value
    }

    const response = await axios.post('/siswa/attendance/check-in', payload, { timeout: 30000 })

    if (response.data.success) {
      toast.success(response.data.message || 'Check in berhasil!')
      await loadTodayStatus()
      await loadHistory()
    } else {
      toast.error(response.data.message || 'Check in gagal')
    }
  } catch (error) {
    console.error('Check in error:', error)
    const msg = error.response?.data?.message || error.message || 'Check in gagal'
    toast.error(msg)
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

  loading.value = true
  try {
    const position = await getPosition()
    const payload = {
      latitude: position.coords.latitude,
      longitude: position.coords.longitude,
    }

    const frame = capturedPhoto.value || (cameraReady.value ? captureFrame() : null)
    if (frame) {
      payload.photo = getBase64FromDataUrl(frame)
    }
    capturedPhoto.value = null

    const response = await axios.post('/siswa/attendance/check-out', payload, { timeout: 30000 })

    if (response.data.success) {
      toast.success(response.data.message || 'Check out berhasil!')
      await loadTodayStatus()
      await loadHistory()
    } else {
      toast.error(response.data.message || 'Check out gagal')
    }
  } catch (error) {
    console.error('Check out error:', error)
    const msg = error.response?.data?.message || error.message || 'Check out gagal'
    toast.error(msg)
  } finally {
    loading.value = false
  }
}

// Initialize map
const initMap = () => {
  if (!companyLat.value || !companyLng.value) return

  const mapContainer = document.getElementById('attendance-map')
  if (!mapContainer) return

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
          userMarker = L.marker([userLat, userLng]).addTo(map).bindPopup('Anda di sini')
        }
      },
      (err) => console.error('Geolocation error:', err),
      { enableHighAccuracy: true, maximumAge: 0 }
    )
  }
}

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

const loadAllData = async () => {
  await Promise.all([
    loadCompanyInfo(),
    loadTodayStatus(),
    loadHistory(),
  ])
  setTimeout(() => initMap(), 500)
}

onMounted(() => {
  loadAllData()
  startCamera()
})

onUnmounted(() => {
  stopCamera()
  if (watchId) navigator.geolocation.clearWatch(watchId)
  if (map) { map.remove(); map = null }
})
</script>
