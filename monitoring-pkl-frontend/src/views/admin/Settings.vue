<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-2xl font-bold text-gray-800">Pengaturan Sistem</h1>
      <p class="text-gray-500 mt-1">Konfigurasi sistem monitoring PKL</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6">
      <div class="space-y-4">
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Nama Sekolah</label>
            <input v-model="settings.school_name" class="w-full px-3 py-2 border rounded-lg">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Nama Aplikasi</label>
            <input v-model="settings.app_name" class="w-full px-3 py-2 border rounded-lg">
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-1">Periode PKL Mulai</label>
            <input v-model="settings.pkl_start" type="date" class="w-full px-3 py-2 border rounded-lg">
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Periode PKL Selesai</label>
            <input v-model="settings.pkl_end" type="date" class="w-full px-3 py-2 border rounded-lg">
          </div>
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Batas Waktu Check-in</label>
          <input v-model="settings.checkin_limit" type="time" class="w-full px-3 py-2 border rounded-lg">
        </div>
        <div>
          <label class="block text-sm font-medium mb-1">Target Logbook per Bulan</label>
          <input v-model="settings.logbook_target" type="number" class="w-full px-3 py-2 border rounded-lg">
        </div>
        <button @click="saveSettings" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">Simpan Pengaturan</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useToast } from 'vue-toastification'
import axios from '../../plugins/axios'

const toast = useToast()
const settings = ref({ school_name: '', app_name: '', pkl_start: '', pkl_end: '', checkin_limit: '08:00', logbook_target: 30 })

const saveSettings = async () => {
  try { await axios.post('/settings', settings.value); toast.success('Pengaturan disimpan') }
  catch(e) { toast.error('Gagal menyimpan') }
}
</script>