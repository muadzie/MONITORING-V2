<template>
  <div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-2xl font-bold text-gray-800">Pengaturan Sistem</h1>
      <p class="text-gray-500 mt-1">Konfigurasi sistem monitoring PKL</p>
    </div>

    <!-- Settings Tabs -->
    <div class="border-b">
      <div class="flex space-x-6">
        <button 
          v-for="tab in tabs" 
          :key="tab.value"
          @click="activeTab = tab.value"
          :class="activeTab === tab.value ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
          class="px-1 py-3 border-b-2 font-medium text-sm"
        >
          {{ tab.label }}
        </button>
      </div>
    </div>

    <!-- General Settings -->
    <div v-if="activeTab === 'general'" class="bg-white rounded-2xl shadow-sm p-6">
      <h3 class="text-lg font-semibold mb-4">Pengaturan Umum</h3>
      <form @submit.prevent="saveGeneralSettings" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Nama Aplikasi</label>
          <input v-model="settings.app_name" type="text" class="w-full px-4 py-2 border rounded-lg">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Periode PKL Mulai</label>
          <input v-model="settings.pkl_start_date" type="date" class="w-full px-4 py-2 border rounded-lg">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Periode PKL Berakhir</label>
          <input v-model="settings.pkl_end_date" type="date" class="w-full px-4 py-2 border rounded-lg">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Batas Waktu Check-in (Jam)</label>
          <input v-model="settings.checkin_deadline" type="time" class="w-full px-4 py-2 border rounded-lg">
        </div>
        <button type="submit" :disabled="saving" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
          {{ saving ? 'Menyimpan...' : 'Simpan Pengaturan' }}
        </button>
      </form>
    </div>

    <!-- PKL Rules -->
    <div v-if="activeTab === 'rules'" class="bg-white rounded-2xl shadow-sm p-6">
      <h3 class="text-lg font-semibold mb-4">Aturan PKL</h3>
      <form @submit.prevent="saveRules" class="space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Target Logbook per Bulan</label>
          <input v-model="rules.logbook_target" type="number" class="w-full px-4 py-2 border rounded-lg">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Minimal Kehadiran (%)</label>
          <input v-model="rules.min_attendance" type="number" class="w-full px-4 py-2 border rounded-lg">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Sanksi Keterlambatan</label>
          <textarea v-model="rules.late_penalty" rows="3" class="w-full px-4 py-2 border rounded-lg"></textarea>
        </div>
        <button type="submit" :disabled="saving" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
          {{ saving ? 'Menyimpan...' : 'Simpan Aturan' }}
        </button>
      </form>
    </div>

    <!-- Notification Settings -->
    <div v-if="activeTab === 'notifications'" class="bg-white rounded-2xl shadow-sm p-6">
      <h3 class="text-lg font-semibold mb-4">Pengaturan Notifikasi</h3>
      <div class="space-y-4">
        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium">Notifikasi Email</p>
            <p class="text-sm text-gray-500">Kirim notifikasi melalui email</p>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" v-model="notifications.email" class="sr-only peer">
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
          </label>
        </div>
        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium">Notifikasi WhatsApp</p>
            <p class="text-sm text-gray-500">Kirim notifikasi melalui WhatsApp</p>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" v-model="notifications.whatsapp" class="sr-only peer">
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
          </label>
        </div>
        <div class="flex items-center justify-between">
          <div>
            <p class="font-medium">Notifikasi Absensi</p>
            <p class="text-sm text-gray-500">Notifikasi saat siswa melakukan absensi</p>
          </div>
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" v-model="notifications.attendance" class="sr-only peer">
            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
          </label>
        </div>
      </div>
      <button @click="saveNotifications" :disabled="saving" class="mt-6 px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
        {{ saving ? 'Menyimpan...' : 'Simpan Pengaturan' }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useToast } from 'vue-toastification'
import axios from '../../plugins/axios'

const toast = useToast()
const saving = ref(false)
const activeTab = ref('general')

const tabs = [
  { label: 'Umum', value: 'general' },
  { label: 'Aturan PKL', value: 'rules' },
  { label: 'Notifikasi', value: 'notifications' }
]

const settings = ref({
  app_name: 'Monitoring PKL SMKN 1 Subang',
  pkl_start_date: '2026-01-01',
  pkl_end_date: '2026-03-31',
  checkin_deadline: '08:00'
})

const rules = ref({
  logbook_target: 30,
  min_attendance: 75,
  late_penalty: 'Peringatan tertulis untuk keterlambatan pertama, pemanggilan orang tua untuk keterlambatan berulang'
})

const notifications = ref({
  email: true,
  whatsapp: false,
  attendance: true
})

const saveGeneralSettings = async () => {
  saving.value = true
  try {
    await axios.post('/admin/settings/general', settings.value)
    toast.success('Pengaturan umum berhasil disimpan')
  } catch (error) {
    toast.error('Gagal menyimpan pengaturan')
  } finally {
    saving.value = false
  }
}

const saveRules = async () => {
  saving.value = true
  try {
    await axios.post('/admin/settings/rules', rules.value)
    toast.success('Aturan PKL berhasil disimpan')
  } catch (error) {
    toast.error('Gagal menyimpan aturan')
  } finally {
    saving.value = false
  }
}

const saveNotifications = async () => {
  saving.value = true
  try {
    await axios.post('/admin/settings/notifications', notifications.value)
    toast.success('Pengaturan notifikasi berhasil disimpan')
  } catch (error) {
    toast.error('Gagal menyimpan pengaturan notifikasi')
  } finally {
    saving.value = false
  }
}
</script>