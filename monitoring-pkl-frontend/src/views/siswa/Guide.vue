<template>
  <div class="space-y-6">
    <div><h1 class="text-2xl font-bold">Panduan PKL</h1><p class="text-gray-500">Panduan penggunaan sistem untuk siswa PKL</p></div>
    <div class="grid md:grid-cols-2 gap-6">
      <div v-for="(guide, idx) in guides" :key="idx" class="bg-white rounded-2xl p-6 shadow-sm">
        <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-4"><span class="text-2xl">{{ guide.icon }}</span></div>
        <h3 class="text-lg font-semibold">{{ guide.title }}</h3>
        <p class="text-gray-500 mt-2">{{ guide.description }}</p>
        <router-link :to="guide.link" class="text-green-600 text-sm mt-3 inline-block">Pelajari →</router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
const guides = [
  { icon: '📍', title: 'Absensi GPS', description: 'Lakukan absensi setiap hari dengan GPS sesuai lokasi PKL Anda.', link: '/siswa/attendance' },
  { icon: '📝', title: 'Logbook Harian', description: 'Catat kegiatan harian Anda selama PKL.', link: '/siswa/logbook' },
  { icon: '📄', title: 'Pengajuan Izin', description: 'Ajukan izin jika tidak dapat hadir.', link: '/siswa/permission' },
  { icon: '🏢', title: 'Info Perusahaan', description: 'Lihat detail perusahaan PKL Anda.', link: '/siswa/company' }
]
</script>