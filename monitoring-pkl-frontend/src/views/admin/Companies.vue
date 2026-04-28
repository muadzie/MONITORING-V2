<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Perusahaan</h1>
        <p class="text-gray-500 mt-1">Kelola data perusahaan tempat PKL siswa</p>
      </div>
      <button 
        @click="openModal" 
        class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2.5 rounded-xl hover:shadow-lg transition-all duration-300 hover:scale-105 flex items-center gap-2"
      >
        <PlusIcon class="w-5 h-5" />
        Tambah Perusahaan
      </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Total Perusahaan</p>
        <p class="text-2xl font-bold">{{ companies.length }}</p>
      </div>
      <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Aktif</p>
        <p class="text-2xl font-bold">{{ companies.filter(c => c.is_active !== false).length }}</p>
      </div>
      <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Total Siswa Magang</p>
        <p class="text-2xl font-bold">{{ totalStudents }}</p>
      </div>
      <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white">
        <p class="text-sm opacity-90">Radius Rata-rata</p>
        <p class="text-2xl font-bold">{{ averageRadius }} m</p>
      </div>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-xl shadow-sm p-4">
      <div class="relative">
        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <input 
          v-model="search" 
          type="text" 
          placeholder="Cari perusahaan (Nama, Alamat)..."
          class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
        >
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
        <p class="mt-4 text-gray-500">Memuat data perusahaan...</p>
      </div>
    </div>

    <!-- Grid List Perusahaan -->
    <div v-else-if="filteredCompanies.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="company in filteredCompanies" :key="company.id" class="bg-white rounded-xl shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group">
        <div class="h-32 bg-gradient-to-r from-indigo-500 to-purple-600 relative">
          <div class="absolute inset-0 bg-black/20 group-hover:bg-black/30 transition"></div>
          <div class="absolute bottom-0 left-0 right-0 p-4 bg-gradient-to-t from-black/70 to-transparent">
            <h3 class="text-white font-bold text-lg">{{ company.name }}</h3>
          </div>
        </div>
        <div class="p-4">
          <p class="text-gray-600 text-sm flex items-start gap-2 mb-3">
            <MapPinIcon class="w-4 h-4 text-gray-400 mt-0.5 flex-shrink-0" />
            <span class="line-clamp-2">{{ company.address }}</span>
          </p>
          <div class="flex justify-between items-center mb-3">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
              </div>
              <div>
                <p class="text-xs text-gray-500">Radius</p>
                <p class="text-sm font-semibold text-indigo-600">{{ company.radius }} m</p>
              </div>
            </div>
            <div class="flex gap-1">
              <button @click="editCompany(company)" class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Edit">
                <PencilSquareIcon class="w-4 h-4" />
              </button>
              <button @click="deleteCompany(company)" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                <TrashIcon class="w-4 h-4" />
              </button>
            </div>
          </div>
          <div class="pt-3 border-t border-gray-100">
            <div class="flex justify-between text-xs text-gray-400">
              <span>Lat: {{ formatCoordinate(company.latitude) }}</span>
              <span>Lng: {{ formatCoordinate(company.longitude) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!loading && filteredCompanies.length === 0" class="bg-white rounded-xl p-12 text-center">
      <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
      </svg>
      <p class="text-gray-500">Belum ada data perusahaan</p>
      <button @click="openModal" class="mt-3 text-indigo-600 hover:underline">Tambah perusahaan pertama</button>
    </div>

    <!-- Modal Form Tambah/Edit Perusahaan -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50" @click.self="closeModal">
      <div class="bg-white rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white p-5 border-b">
          <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                <BuildingOffice2Icon class="w-5 h-5 text-indigo-600" />
              </div>
              <div>
                <h2 class="text-xl font-bold text-gray-800">{{ isEdit ? 'Edit Perusahaan' : 'Tambah Perusahaan' }}</h2>
                <p class="text-sm text-gray-500">{{ isEdit ? 'Ubah data perusahaan' : 'Masukkan data perusahaan baru' }}</p>
              </div>
            </div>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition">
              <XMarkIcon class="w-6 h-6" />
            </button>
          </div>
        </div>
        
        <form @submit.prevent="saveCompany" class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Kolom Kiri -->
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan</label>
                <input v-model="form.name" type="text" class="w-full px-4 py-2.5 border rounded-lg" required>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea v-model="form.address" rows="3" class="w-full px-4 py-2.5 border rounded-lg" required></textarea>
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                  <input v-model="form.latitude" type="number" step="any" class="w-full px-4 py-2.5 border rounded-lg bg-gray-50" readonly>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                  <input v-model="form.longitude" type="number" step="any" class="w-full px-4 py-2.5 border rounded-lg bg-gray-50" readonly>
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Radius (meter)</label>
                <input v-model="form.radius" type="number" class="w-full px-4 py-2.5 border rounded-lg" required>
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                  <input v-model="form.phone" type="tel" class="w-full px-4 py-2.5 border rounded-lg">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                  <input v-model="form.email" type="email" class="w-full px-4 py-2.5 border rounded-lg">
                </div>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea v-model="form.description" rows="2" class="w-full px-4 py-2.5 border rounded-lg"></textarea>
              </div>
            </div>
            
            <!-- Kolom Kanan - Maps -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Lokasi di Maps</label>
              <div ref="mapContainer" class="h-96 w-full rounded-lg border overflow-hidden bg-gray-100"></div>
              <div class="mt-3 flex gap-2">
                <input v-model="searchAddress" type="text" placeholder="Cari alamat..." class="flex-1 px-3 py-2 border rounded-lg text-sm" @keyup.enter="searchLocation">
                <button type="button" @click="searchLocation" class="px-3 py-2 bg-gray-100 rounded-lg">🔍 Cari</button>
                <button type="button" @click="getCurrentLocation" class="px-3 py-2 bg-indigo-100 text-indigo-600 rounded-lg">📍 Lokasi Saya</button>
              </div>
            </div>
          </div>

          <div class="flex justify-end gap-3 mt-6 pt-4 border-t">
            <button type="button" @click="closeModal" class="px-5 py-2.5 border rounded-lg">Batal</button>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg" :disabled="saving">
              {{ saving ? 'Menyimpan...' : (isEdit ? 'Update' : 'Simpan') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'
import { PlusIcon, PencilSquareIcon, TrashIcon, MapPinIcon, XMarkIcon, BuildingOffice2Icon } from '@heroicons/vue/24/outline'
import L from 'leaflet'

// Fix Leaflet icon
delete L.Icon.Default.prototype._getIconUrl
L.Icon.Default.mergeOptions({
  iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
  iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
})

const toast = useToast()
const companies = ref([])
const loading = ref(true)
const showModal = ref(false)
const isEdit = ref(false)
const saving = ref(false)
const search = ref('')
const searchAddress = ref('')

const form = ref({
  id: null,
  name: '',
  address: '',
  latitude: -6.5696,
  longitude: 107.7583,
  radius: 100,
  phone: '',
  email: '',
  description: ''
})

// Map variables
let map = null
let marker = null
let circle = null
const mapContainer = ref(null)

// Helper functions
const formatCoordinate = (value) => {
  if (!value) return '0.000000'
  const num = typeof value === 'string' ? parseFloat(value) : value
  return isNaN(num) ? '0.000000' : num.toFixed(6)
}

// Computed
const totalStudents = computed(() => {
  if (!companies.value.length) return 0
  return companies.value.reduce((sum, c) => sum + (c.students_count || 0), 0)
})

const averageRadius = computed(() => {
  if (!companies.value.length) return 0
  const sum = companies.value.reduce((sum, c) => sum + (c.radius || 0), 0)
  return Math.round(sum / companies.value.length)
})

const filteredCompanies = computed(() => {
  if (!companies.value.length) return []
  if (!search.value) return companies.value
  const searchLower = search.value.toLowerCase()
  return companies.value.filter(c => 
    c.name?.toLowerCase().includes(searchLower) ||
    c.address?.toLowerCase().includes(searchLower)
  )
})

// Initialize Map
const initMap = async () => {
  await nextTick()
  if (!mapContainer.value) return
  
  if (map) map.remove()
  
  const lat = typeof form.value.latitude === 'string' ? parseFloat(form.value.latitude) : form.value.latitude
  const lng = typeof form.value.longitude === 'string' ? parseFloat(form.value.longitude) : form.value.longitude
  
  map = L.map(mapContainer.value).setView([lat, lng], 15)
  
  L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; OpenStreetMap',
    subdomains: 'abcd',
    maxZoom: 19
  }).addTo(map)
  
  marker = L.marker([lat, lng], { draggable: true }).addTo(map)
  marker.on('dragend', (e) => {
    const pos = e.target.getLatLng()
    form.value.latitude = pos.lat
    form.value.longitude = pos.lng
    updateCircle()
    reverseGeocode(pos.lat, pos.lng)
  })
  
  updateCircle()
  
  map.on('click', (e) => {
    form.value.latitude = e.latlng.lat
    form.value.longitude = e.latlng.lng
    marker.setLatLng(e.latlng)
    updateCircle()
    reverseGeocode(e.latlng.lat, e.latlng.lng)
  })
}

const updateCircle = () => {
  if (circle) map.removeLayer(circle)
  const lat = typeof form.value.latitude === 'string' ? parseFloat(form.value.latitude) : form.value.latitude
  const lng = typeof form.value.longitude === 'string' ? parseFloat(form.value.longitude) : form.value.longitude
  circle = L.circle([lat, lng], {
    radius: form.value.radius,
    color: '#4F46E5',
    fillColor: '#4F46E5',
    fillOpacity: 0.1,
    weight: 2
  }).addTo(map)
}

const reverseGeocode = async (lat, lng) => {
  try {
    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`)
    const data = await response.json()
    if (data.display_name) form.value.address = data.display_name
  } catch (error) {
    console.error('Reverse geocoding error:', error)
  }
}

const searchLocation = async () => {
  if (!searchAddress.value) {
    toast.warning('Masukkan alamat yang ingin dicari')
    return
  }
  
  try {
    const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(searchAddress.value)}&limit=1`)
    const data = await response.json()
    
    if (data.length > 0) {
      form.value.latitude = parseFloat(data[0].lat)
      form.value.longitude = parseFloat(data[0].lon)
      form.value.address = data[0].display_name
      map.setView([form.value.latitude, form.value.longitude], 15)
      marker.setLatLng([form.value.latitude, form.value.longitude])
      updateCircle()
      toast.success('Lokasi ditemukan')
    } else {
      toast.warning('Lokasi tidak ditemukan')
    }
  } catch (error) {
    toast.error('Gagal mencari lokasi')
  }
}

const getCurrentLocation = () => {
  if (!navigator.geolocation) {
    toast.error('Browser tidak mendukung geolocation')
    return
  }
  
  navigator.geolocation.getCurrentPosition(
    (pos) => {
      form.value.latitude = pos.coords.latitude
      form.value.longitude = pos.coords.longitude
      map.setView([pos.coords.latitude, pos.coords.longitude], 15)
      marker.setLatLng([pos.coords.latitude, pos.coords.longitude])
      updateCircle()
      reverseGeocode(pos.coords.latitude, pos.coords.longitude)
      toast.success('Lokasi berhasil diambil')
    },
    () => toast.error('Gagal mengambil lokasi')
  )
}

// CRUD Operations
const fetchCompanies = async () => {
  loading.value = true
  try {
    const res = await axios.get('/companies')
    companies.value = Array.isArray(res.data) ? res.data : (res.data.data || [])
  } catch (error) {
    toast.error('Gagal memuat data perusahaan')
    companies.value = []
  } finally {
    loading.value = false
  }
}

const openModal = () => {
  console.log('Open modal - Add mode')
  isEdit.value = false
  form.value = {
    id: null,
    name: '',
    address: '',
    latitude: -6.5696,
    longitude: 107.7583,
    radius: 100,
    phone: '',
    email: '',
    description: ''
  }
  searchAddress.value = ''
  showModal.value = true
  
  setTimeout(() => initMap(), 300)
}

const editCompany = (company) => {
  console.log('Open modal - Edit mode', company)
  isEdit.value = true
  form.value = {
    id: company.id,
    name: company.name || '',
    address: company.address || '',
    latitude: typeof company.latitude === 'string' ? parseFloat(company.latitude) : (company.latitude || -6.5696),
    longitude: typeof company.longitude === 'string' ? parseFloat(company.longitude) : (company.longitude || 107.7583),
    radius: company.radius || 100,
    phone: company.phone || '',
    email: company.email || '',
    description: company.description || ''
  }
  searchAddress.value = ''
  showModal.value = true
  
  setTimeout(() => initMap(), 300)
}

const closeModal = () => {
  showModal.value = false
  if (map) {
    map.remove()
    map = null
  }
}

const saveCompany = async () => {
  saving.value = true
  try {
    const dataToSend = {
      ...form.value,
      latitude: parseFloat(form.value.latitude),
      longitude: parseFloat(form.value.longitude)
    }
    
    if (isEdit.value) {
      await axios.put(`/companies/${dataToSend.id}`, dataToSend)
      toast.success('Perusahaan berhasil diupdate')
    } else {
      await axios.post('/companies', dataToSend)
      toast.success('Perusahaan berhasil ditambahkan')
    }
    closeModal()
    await fetchCompanies()
  } catch (error) {
    toast.error(error.response?.data?.message || 'Gagal menyimpan perusahaan')
  } finally {
    saving.value = false
  }
}

const deleteCompany = async (company) => {
  if (confirm(`Hapus perusahaan "${company.name}"?`)) {
    try {
      await axios.delete(`/companies/${company.id}`)
      toast.success('Perusahaan berhasil dihapus')
      await fetchCompanies()
    } catch (error) {
      toast.error('Gagal menghapus perusahaan')
    }
  }
}

onMounted(() => {
  fetchCompanies()
})

onUnmounted(() => {
  if (map) {
    map.remove()
    map = null
  }
})
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>