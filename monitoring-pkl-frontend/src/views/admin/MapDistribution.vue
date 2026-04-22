<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-8 text-white">
      <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
      <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/10 rounded-full -ml-24 -mb-24"></div>
      <div class="relative z-10">
        <div class="flex justify-between items-start">
          <div>
            <h1 class="text-3xl font-bold">Peta Sebaran PKL</h1>
            <p class="mt-2 text-indigo-100">Visualisasi lokasi siswa magang di peta interaktif</p>
            <div class="flex gap-4 mt-4">
              <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                <p class="text-xs opacity-75">Periode PKL</p>
                <p class="font-semibold">Januari - Juni 2026</p>
              </div>
              <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
                <p class="text-xs opacity-75">Total Perusahaan</p>
                <p class="font-semibold">{{ markers.length }}</p>
              </div>
            </div>
          </div>
          <div class="flex gap-2">
            <button 
              @click="refreshData" 
              class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-xl hover:bg-white/30 transition flex items-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              Refresh
            </button>
            <router-link 
              to="/admin/placements" 
              class="px-4 py-2 bg-white text-indigo-600 rounded-xl hover:shadow-lg transition flex items-center gap-2 font-medium"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
              </svg>
              Kelola Penempatan
            </router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Total Perusahaan</p>
            <p class="text-3xl font-bold text-gray-800">{{ markers.length }}</p>
            <p class="text-xs text-green-600 mt-1">Mitra PKL Aktif</p>
          </div>
          <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
            <BuildingOffice2Icon class="w-6 h-6 text-indigo-600" />
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Total Siswa Magang</p>
            <p class="text-3xl font-bold text-gray-800">{{ totalStudents }}</p>
            <p class="text-xs text-green-600 mt-1">Sedang PKL Aktif</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
            <UserGroupIcon class="w-6 h-6 text-green-600" />
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Kota/Kabupaten</p>
            <p class="text-3xl font-bold text-gray-800">{{ uniqueCities }}</p>
            <p class="text-xs text-blue-600 mt-1">Tersebar di berbagai daerah</p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
            <MapPinIcon class="w-6 h-6 text-blue-600" />
          </div>
        </div>
      </div>

      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition group">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Radius Rata-rata</p>
            <p class="text-3xl font-bold text-gray-800">{{ avgRadius }} m</p>
            <p class="text-xs text-purple-600 mt-1">Zona Absensi</p>
          </div>
          <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center group-hover:scale-110 transition">
            <RadioIcon class="w-6 h-6 text-purple-600" />
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content: Daftar Perusahaan + Maps -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Daftar Perusahaan -->
      <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="p-4 border-b bg-gray-50">
          <div class="flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">Daftar Perusahaan</h3>
            <span class="text-xs text-gray-500">{{ markers.length }} Perusahaan</span>
          </div>
          <div class="relative mt-3">
            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="Cari perusahaan..."
              class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
            >
          </div>
        </div>
        <div class="max-h-[500px] overflow-y-auto divide-y">
          <div 
            v-for="marker in filteredMarkersList" 
            :key="marker.id"
            class="p-4 hover:bg-indigo-50 cursor-pointer transition group"
            :class="selectedMarker?.id === marker.id ? 'bg-indigo-50 border-l-4 border-indigo-600' : ''"
          >
            <div class="flex items-start gap-3" @click="flyToLocation(marker)">
              <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" :class="getRadiusColorClass(marker.radius)">
                <BuildingOffice2Icon class="w-5 h-5" :class="getRadiusIconColor(marker.radius)" />
              </div>
              <div class="flex-1 min-w-0">
                <h4 class="font-semibold text-gray-800 truncate">{{ marker.company_name || marker.name }}</h4>
                <p class="text-xs text-gray-500 truncate">{{ marker.address }}</p>
                <div class="flex items-center gap-3 mt-1">
                  <span class="text-xs text-indigo-600">📍 Radius: {{ marker.radius }}m</span>
                  <span class="text-xs text-green-600">👥 {{ marker.students_count || 0 }} siswa</span>
                </div>
              </div>
              <button 
                @click.stop="showDetailModal(marker)"
                class="p-1 text-gray-400 hover:text-indigo-600 transition"
                title="Detail"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </button>
            </div>
          </div>
          <div v-if="filteredMarkersList.length === 0" class="p-8 text-center text-gray-500">
            <BuildingOffice2Icon class="w-12 h-12 mx-auto text-gray-300 mb-2" />
            <p>Tidak ada perusahaan ditemukan</p>
          </div>
        </div>
      </div>

      <!-- Maps -->
      <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="p-3 border-b bg-gray-50 flex justify-between items-center">
          <div class="flex items-center gap-2">
            <div class="w-3 h-3 bg-green-500 rounded-full"></div>
            <span class="text-xs">Radius &lt; 100m</span>
            <div class="w-3 h-3 bg-indigo-500 rounded-full ml-2"></div>
            <span class="text-xs">Radius 100-200m</span>
            <div class="w-3 h-3 bg-red-500 rounded-full ml-2"></div>
            <span class="text-xs">Radius &gt; 200m</span>
          </div>
          <div class="text-xs text-gray-500">
            📍 {{ markers.length }} titik lokasi
          </div>
        </div>
        <div ref="mapContainer" class="h-[550px] w-full"></div>
      </div>
    </div>

    <!-- Legend Card -->
    <div class="bg-white rounded-2xl shadow-sm p-4">
      <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex flex-wrap gap-6">
          <div class="flex items-center gap-2">
            <div class="w-4 h-4 bg-indigo-500 rounded-full shadow-sm"></div>
            <span class="text-sm text-gray-600">Lokasi Perusahaan</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-4 h-4 border-2 border-indigo-500 rounded-full"></div>
            <span class="text-sm text-gray-600">Radius Absensi</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-4 h-4 bg-green-500 rounded-full"></div>
            <span class="text-sm text-gray-600">Radius Kecil (&lt;100m)</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-4 h-4 bg-indigo-500 rounded-full"></div>
            <span class="text-sm text-gray-600">Radius Sedang (100-200m)</span>
          </div>
          <div class="flex items-center gap-2">
            <div class="w-4 h-4 bg-red-500 rounded-full"></div>
            <span class="text-sm text-gray-600">Radius Besar (&gt;200m)</span>
          </div>
        </div>
        <div class="text-sm text-gray-400">
          💡 Klik marker atau nama perusahaan untuk melihat detail
        </div>
      </div>
    </div>

    <!-- Modal Detail Perusahaan -->
    <div v-if="showModal" class="fixed inset-0 z-[9999] flex items-center justify-center" @click.self="closeModal">
      <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
      <div class="relative bg-white rounded-2xl w-full max-w-md mx-4 overflow-hidden shadow-2xl animate-fade-in-up z-[10000]">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4 text-white">
          <div class="flex justify-between items-center">
            <h3 class="text-xl font-bold">Detail Perusahaan</h3>
            <button @click="closeModal" class="text-white/80 hover:text-white transition text-2xl leading-none">
              ✕
            </button>
          </div>
        </div>
        <div class="p-6 space-y-4">
          <div class="flex items-start gap-3">
            <BuildingOffice2Icon class="w-5 h-5 text-indigo-600 mt-0.5 flex-shrink-0" />
            <div>
              <p class="text-sm text-gray-500">Nama Perusahaan</p>
              <p class="font-semibold text-gray-800">{{ selectedMarker?.company_name }}</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <MapPinIcon class="w-5 h-5 text-indigo-600 mt-0.5 flex-shrink-0" />
            <div>
              <p class="text-sm text-gray-500">Alamat</p>
              <p class="text-sm text-gray-600">{{ selectedMarker?.address }}</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <RadioIcon class="w-5 h-5 text-indigo-600 mt-0.5 flex-shrink-0" />
            <div>
              <p class="text-sm text-gray-500">Radius Absensi</p>
              <p class="text-sm font-semibold text-indigo-600">{{ selectedMarker?.radius }} meter</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <UserGroupIcon class="w-5 h-5 text-indigo-600 mt-0.5 flex-shrink-0" />
            <div>
              <p class="text-sm text-gray-500">Jumlah Siswa Magang</p>
              <p class="text-2xl font-bold text-indigo-600">{{ selectedMarker?.students_count || 0 }}</p>
            </div>
          </div>
        </div>
        <div class="p-4 border-t bg-gray-50 flex justify-end gap-2">
          <button @click="flyToLocationAndClose" class="px-4 py-2 border border-indigo-600 text-indigo-600 rounded-lg hover:bg-indigo-50 transition">
            🗺️ Lihat di Peta
          </button>
          <router-link 
            :to="`/admin/placements?company=${selectedMarker?.id}`" 
            class="px-5 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition font-medium"
            @click="closeModal"
          >
            Lihat Penempatan
          </router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'
import { BuildingOffice2Icon, UserGroupIcon, MapPinIcon, RadioIcon } from '@heroicons/vue/24/outline'
import L from 'leaflet'

const toast = useToast()
const markers = ref([])
const loading = ref(true)
const selectedMarker = ref(null)
const showModal = ref(false)
const mapContainer = ref(null)
const searchQuery = ref('')
let map = null

// Computed properties
const totalStudents = computed(() => {
  return markers.value.reduce((sum, m) => sum + (m.students_count || 0), 0)
})

const uniqueCities = computed(() => {
  const cities = new Set()
  markers.value.forEach(m => {
    if (m.address) {
      const parts = m.address.split(',')
      const city = parts.length > 1 ? parts[1]?.trim() : parts[0]?.trim()
      if (city) cities.add(city)
    }
  })
  return cities.size
})

const avgRadius = computed(() => {
  if (!markers.value.length) return 0
  const sum = markers.value.reduce((sum, m) => sum + (m.radius || 0), 0)
  return Math.round(sum / markers.value.length)
})

const filteredMarkersList = computed(() => {
  if (!searchQuery.value) return markers.value
  const query = searchQuery.value.toLowerCase()
  return markers.value.filter(m => 
    m.company_name?.toLowerCase().includes(query) ||
    m.address?.toLowerCase().includes(query)
  )
})

// Helper functions
const getRadiusColorClass = (radius) => {
  if (radius < 100) return 'bg-green-100'
  if (radius <= 200) return 'bg-indigo-100'
  return 'bg-red-100'
}

const getRadiusIconColor = (radius) => {
  if (radius < 100) return 'text-green-600'
  if (radius <= 200) return 'text-indigo-600'
  return 'text-red-600'
}

// Fix Leaflet icon
delete L.Icon.Default.prototype._getIconUrl
L.Icon.Default.mergeOptions({
  iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
  iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
})

// Map functions
const initMap = () => {
  if (!mapContainer.value) return
  if (map) map.remove()
  
  map = L.map(mapContainer.value).setView([-6.5696, 107.7583], 11)
  
  L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>',
    subdomains: 'abcd',
    maxZoom: 19
  }).addTo(map)
  
  addMarkersToMap()
}

const addMarkersToMap = () => {
  if (!map) return
  
  markers.value.forEach(marker => {
    let markerColor = '#4F46E5'
    let radiusColor = '#4F46E5'
    
    if (marker.radius < 100) {
      markerColor = '#10B981'
      radiusColor = '#10B981'
    } else if (marker.radius > 200) {
      markerColor = '#EF4444'
      radiusColor = '#EF4444'
    }
    
    const customIcon = L.divIcon({
      html: `<div style="background-color: ${markerColor}; width: 14px; height: 14px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3); cursor: pointer;"></div>`,
      iconSize: [20, 20],
      className: 'custom-marker'
    })
    
    const markerLayer = L.marker([marker.latitude, marker.longitude], { icon: customIcon }).addTo(map)
    markerLayer.bindTooltip(`
      <div style="padding: 8px; max-width: 200px;">
        <strong>${marker.company_name}</strong><br>
        Radius: ${marker.radius}m<br>
        Siswa: ${marker.students_count || 0}
      </div>
    `, { sticky: true })
    
    markerLayer.on('click', () => {
      showDetailModal(marker)
    })
    
    L.circle([marker.latitude, marker.longitude], {
      radius: marker.radius,
      color: radiusColor,
      fillColor: radiusColor,
      fillOpacity: 0.1,
      weight: 2
    }).addTo(map)
  })
}

const flyToLocation = (marker) => {
  if (!map) return
  map.flyTo([marker.latitude, marker.longitude], 15, {
    duration: 1.5,
    easeLinearity: 0.25
  })
  selectedMarker.value = marker
  toast.success(`Membuka lokasi: ${marker.company_name}`)
}

const flyToLocationAndClose = () => {
  if (selectedMarker.value) {
    flyToLocation(selectedMarker.value)
    closeModal()
  }
}

const showDetailModal = (marker) => {
  selectedMarker.value = marker
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedMarker.value = null
}

const fetchMapData = async () => {
  loading.value = true
  try {
    const res = await axios.get('/admin/map/companies')
    markers.value = res.data
    console.log('Map data loaded:', markers.value.length)
    
    if (map) {
      map.eachLayer(layer => {
        if (layer instanceof L.Marker || layer instanceof L.Circle) {
          map.removeLayer(layer)
        }
      })
      addMarkersToMap()
    } else {
      initMap()
    }
  } catch (error) {
    console.error('Fetch map error:', error)
    toast.error('Gagal memuat data peta')
  } finally {
    loading.value = false
  }
}

const refreshData = () => {
  fetchMapData()
  toast.success('Data peta diperbarui')
}

onMounted(() => {
  fetchMapData()
})

onUnmounted(() => {
  if (map) {
    map.remove()
    map = null
  }
})
</script>

<style scoped>
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fadeInUp 0.3s ease-out;
}

.custom-marker {
  background: transparent;
  border: none;
}

.max-h-\[500px\]::-webkit-scrollbar {
  width: 6px;
}

.max-h-\[500px\]::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.max-h-\[500px\]::-webkit-scrollbar-thumb {
  background: #c7d2fe;
  border-radius: 3px;
}

.max-h-\[500px\]::-webkit-scrollbar-thumb:hover {
  background: #818cf8;
}
</style>