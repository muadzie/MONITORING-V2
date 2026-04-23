<template>
  <div class="space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-gray-800">Info Perusahaan PKL</h1>
      <p class="text-gray-500 mt-1">Detail perusahaan tempat Anda melaksanakan PKL</p>
    </div>

    <!-- Company Card -->
    <div v-if="company" class="bg-white rounded-2xl shadow-sm overflow-hidden">
      <div class="bg-gradient-to-r from-green-600 to-teal-600 px-6 py-4">
        <h2 class="text-xl font-bold text-white">{{ company.name }}</h2>
      </div>
      <div class="p-6 space-y-4">
        <div class="flex items-start gap-3">
          <MapPinIcon class="w-5 h-5 text-gray-400 mt-0.5" />
          <div>
            <p class="text-sm font-medium text-gray-700">Alamat</p>
            <p class="text-gray-600">{{ company.address }}</p>
          </div>
        </div>
        <div class="flex items-start gap-3">
          <RadioIcon class="w-5 h-5 text-gray-400 mt-0.5" />
          <div>
            <p class="text-sm font-medium text-gray-700">Radius Absensi</p>
            <p class="text-gray-600">{{ company.radius }} meter</p>
            <p class="text-xs text-gray-500 mt-1">Anda harus berada dalam radius ini untuk melakukan absensi</p>
          </div>
        </div>
        <div class="flex items-start gap-3">
          <MapIcon class="w-5 h-5 text-gray-400 mt-0.5" />
          <div>
            <p class="text-sm font-medium text-gray-700">Koordinat</p>
            <p class="text-gray-600">Latitude: {{ company.latitude }}, Longitude: {{ company.longitude }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Map Preview -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
      <div class="px-6 py-4 border-b">
        <h3 class="font-semibold text-gray-800">Lokasi Perusahaan</h3>
      </div>
      <div ref="mapContainer" class="h-96 w-full"></div>
    </div>

    <!-- Pembimbing -->
    <div class="bg-white rounded-2xl shadow-sm p-6">
      <h3 class="font-semibold text-gray-800 mb-4">Pembimbing Perusahaan</h3>
      <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
          <UserIcon class="w-6 h-6 text-purple-600" />
        </div>
        <div>
          <p class="font-medium">{{ pembimbing?.name || 'Belum ditentukan' }}</p>
          <p class="text-sm text-gray-500">{{ pembimbing?.email || '-' }}</p>
          <p class="text-sm text-gray-500">{{ pembimbing?.phone || '-' }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import { MapPinIcon, BuildingOffice2Icon, GlobeAltIcon, UserIcon } from '@heroicons/vue/24/outline'
import L from 'leaflet'


const authStore = useAuthStore()
const company = ref(null)
const pembimbing = ref(null)
const mapContainer = ref(null)
let map = null

onMounted(async () => {
  company.value = authStore.user?.company
  pembimbing.value = authStore.user?.teacher
  
  if (company.value && mapContainer.value) {
    // Initialize map
    map = L.map(mapContainer.value).setView([company.value.latitude, company.value.longitude], 15)
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a>'
    }).addTo(map)
    
    // Add marker
    L.marker([company.value.latitude, company.value.longitude]).addTo(map)
    
    // Add circle radius
    L.circle([company.value.latitude, company.value.longitude], {
      radius: company.value.radius,
      color: '#10b981',
      fillColor: '#10b981',
      fillOpacity: 0.1
    }).addTo(map)
  }
})

onUnmounted(() => {
  if (map) map.remove()
})
</script>