<template>
  <div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-green-600 to-teal-600 p-8 text-white">
      <div class="relative z-10">
        <h1 class="text-2xl font-bold">Selamat Datang, {{ authStore.user?.name }}!</h1>
        <p class="mt-2 text-green-100">Semangat menjalani PKL! Catat kegiatanmu setiap hari.</p>
        <div class="flex gap-4 mt-4">
          <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
            <p class="text-xs opacity-75">Perusahaan PKL</p>
            <p class="font-semibold">{{ authStore.user?.company?.name || 'Belum ditempatkan' }}</p>
          </div>
          <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2">
            <p class="text-xs opacity-75">Periode PKL</p>
            <p class="font-semibold">Januari - Maret 2026</p>
          </div>
        </div>
      </div>
      <div class="absolute top-0 right-0 w-64 h-64 bg-white/10 rounded-full -mr-32 -mt-32"></div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Kehadiran</p>
            <p class="text-3xl font-bold text-green-600">{{ stats.attendance }}%</p>
          </div>
          <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
            <CheckCircleIcon class="w-6 h-6 text-green-600" />
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Total Logbook</p>
            <p class="text-3xl font-bold text-blue-600">{{ stats.logbooks }}</p>
          </div>
          <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
            <BookOpenIcon class="w-6 h-6 text-blue-600" />
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Rata-rata Nilai</p>
            <p class="text-3xl font-bold text-purple-600">{{ stats.grade }}</p>
          </div>
          <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
            <StarIcon class="w-6 h-6 text-purple-600" />
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500 text-sm">Target Logbook</p>
            <p class="text-3xl font-bold text-yellow-600">{{ stats.logbooks }}/30</p>
          </div>
          <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
            <ChartBarIcon class="w-6 h-6 text-yellow-600" />
          </div>
        </div>
        <div class="mt-3 h-1 bg-gray-200 rounded-full overflow-hidden">
          <div class="h-full bg-yellow-500 rounded-full" :style="{ width: (stats.logbooks / 30 * 100) + '%' }"></div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <router-link to="/siswa/attendance" class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition group">
        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
          <MapPinIcon class="w-5 h-5 text-green-600" />
        </div>
        <div>
          <p class="font-semibold text-gray-800">Absensi Hari Ini</p>
          <p class="text-xs text-gray-500">Lakukan check-in/out</p>
        </div>
      </router-link>
      <router-link to="/siswa/logbook" class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition group">
        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
          <BookOpenIcon class="w-5 h-5 text-blue-600" />
        </div>
        <div>
          <p class="font-semibold text-gray-800">Buat Logbook</p>
          <p class="text-xs text-gray-500">Catat kegiatan hari ini</p>
        </div>
      </router-link>
      <router-link to="/siswa/permission" class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition group">
        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center group-hover:scale-110 transition">
          <DocumentTextIcon class="w-5 h-5 text-yellow-600" />
        </div>
        <div>
          <p class="font-semibold text-gray-800">Ajukan Izin</p>
          <p class="text-xs text-gray-500">Izin sakit atau keperluan lain</p>
        </div>
      </router-link>
    </div>

    <!-- Recent Logbook -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
      <div class="p-6 border-b">
        <h3 class="text-lg font-semibold">Logbook Terbaru</h3>
      </div>
      <div class="divide-y">
        <div v-for="log in recentLogbooks" :key="log.id" class="p-6 hover:bg-gray-50">
          <div class="flex justify-between items-start">
            <div>
              <p class="font-semibold">{{ log.activity }}</p>
              <p class="text-sm text-gray-500 mt-1">{{ formatDate(log.date) }}</p>
              <p class="text-gray-600 mt-2">{{ log.description }}</p>
            </div>
            <span :class="log.status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'" class="px-2 py-1 rounded-full text-xs">
              {{ log.status === 'approved' ? 'Disetujui' : 'Menunggu' }}
            </span>
          </div>
        </div>
        <div v-if="!recentLogbooks.length" class="p-12 text-center text-gray-500">
          <BookOpenIcon class="w-12 h-12 mx-auto text-gray-300 mb-3" />
          <p>Belum ada logbook</p>
          <router-link to="/siswa/logbook" class="text-green-600 text-sm mt-2 inline-block">Buat logbook pertama →</router-link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../../stores/auth'
import axios from '../../plugins/axios'
import { CheckCircleIcon, BookOpenIcon, StarIcon, ChartBarIcon, MapPinIcon, DocumentTextIcon } from '@heroicons/vue/24/outline'

const authStore = useAuthStore()
const stats = ref({ attendance: 0, logbooks: 0, grade: 0 })
const recentLogbooks = ref([])

const formatDate = (date) => new Date(date).toLocaleDateString('id-ID')

onMounted(async () => {
  try {
    const res = await axios.get('/siswa/dashboard/stats')
    stats.value = res.data
    const logRes = await axios.get('/siswa/logbooks/recent')
    recentLogbooks.value = logRes.data
  } catch (error) {
    console.error('Failed to load dashboard data:', error)
  }
})
</script>