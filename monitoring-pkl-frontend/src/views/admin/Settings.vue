<template>
  <div class="space-y-6">
    <!-- Header Premium -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-8 text-white">
      <div class="relative z-10 flex items-center gap-4">
        <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </div>
        <div>
          <h1 class="text-3xl font-bold">Pengaturan Sistem</h1>
          <p class="mt-2 text-indigo-100">Konfigurasi sistem monitoring PKL SMKN 1 Subang</p>
        </div>
      </div>
    </div>

    <!-- Settings Tabs Premium -->
    <div class="bg-white rounded-2xl shadow-lg p-1 flex flex-wrap gap-1">
      <button 
        v-for="tab in tabs" 
        :key="tab.value"
        @click="activeTab = tab.value"
        class="px-6 py-3 rounded-xl font-medium transition-all duration-200 flex items-center gap-2"
        :class="activeTab === tab.value 
          ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-md' 
          : 'text-gray-600 hover:bg-gray-100'"
      >
        <span class="text-lg">{{ tab.icon }}</span> {{ tab.label }}
      </button>
    </div>

    <!-- Content Panels -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
      
      <!-- Tab: Pengaturan Umum -->
      <div v-if="activeTab === 'general'" class="p-6">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b">
          <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-800">Pengaturan Umum</h3>
            <p class="text-sm text-gray-500">Atur konfigurasi dasar aplikasi</p>
          </div>
        </div>
        
        <form @submit.prevent="saveGeneralSettings" class="space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">🏷️ Nama Aplikasi</label>
            <input v-model="settings.app_name" type="text" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">📅 Periode PKL Mulai</label>
              <input v-model="settings.pkl_start_date" type="date" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">📅 Periode PKL Berakhir</label>
              <input v-model="settings.pkl_end_date" type="date" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
            </div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">⏰ Batas Waktu Check-in</label>
            <div class="relative">
              <input v-model="settings.checkin_deadline" type="time" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
              <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm"></span>
            </div>
            <p class="text-xs text-gray-400 mt-1">*Siswa yang check-in melebihi batas waktu akan dianggap terlambat</p>
          </div>
          
          <div class="flex justify-end pt-4">
            <button type="submit" :disabled="saving" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 flex items-center gap-2 disabled:opacity-50">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              {{ saving ? 'Menyimpan...' : 'Simpan Pengaturan' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Tab: Aturan PKL -->
      <div v-if="activeTab === 'rules'" class="p-6">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b">
          <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-800">Aturan PKL</h3>
            <p class="text-sm text-gray-500">Konfigurasi aturan dan target PKL</p>
          </div>
        </div>
        
        <form @submit.prevent="saveRules" class="space-y-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">📝 Target Logbook per Bulan</label>
            <div class="flex items-center gap-3">
              <input v-model="rules.logbook_target" type="number" class="w-32 px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500">
              <span class="text-gray-500">logbook/bulan</span>
            </div>
            <p class="text-xs text-gray-400 mt-1">*Target minimal logbook yang harus dibuat siswa per bulan</p>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">📊 Minimal Kehadiran</label>
            <div class="flex items-center gap-3">
              <input v-model="rules.min_attendance" type="number" class="w-32 px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500">
              <span class="text-gray-500">%</span>
            </div>
            <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
              <div class="bg-green-500 h-2 rounded-full" :style="{ width: rules.min_attendance + '%' }"></div>
            </div>
            <p class="text-xs text-gray-400 mt-1">*Minimal persentase kehadiran siswa selama PKL</p>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">⚠️ Sanksi Keterlambatan</label>
            <textarea v-model="rules.late_penalty" rows="4" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500" placeholder="Tuliskan sanksi untuk siswa yang terlambat..."></textarea>
          </div>
          
          <div class="flex justify-end pt-4">
            <button type="submit" :disabled="saving" class="px-6 py-2.5 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 flex items-center gap-2 disabled:opacity-50">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
              </svg>
              {{ saving ? 'Menyimpan...' : 'Simpan Aturan' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Tab: Notifikasi -->
      <div v-if="activeTab === 'notifications'" class="p-6">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b">
          <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
          </div>
          <div>
            <h3 class="text-lg font-semibold text-gray-800">Pengaturan Notifikasi</h3>
            <p class="text-sm text-gray-500">Atur metode dan jenis notifikasi yang dikirim</p>
          </div>
        </div>
        
        <div class="space-y-4">
          <!-- Email Notification -->
          <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
              </div>
              <div>
                <p class="font-medium text-gray-800">Notifikasi Email</p>
                <p class="text-sm text-gray-500">Kirim notifikasi melalui email ke pengguna</p>
              </div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" v-model="notifications.email" class="sr-only peer">
              <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
            </label>
          </div>
          
          <!-- WhatsApp Notification -->
          <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
              </div>
              <div>
                <p class="font-medium text-gray-800">Notifikasi WhatsApp</p>
                <p class="text-sm text-gray-500">Kirim notifikasi melalui WhatsApp (experimental)</p>
              </div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" v-model="notifications.whatsapp" class="sr-only peer">
              <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-600"></div>
            </label>
          </div>
          
          <!-- Attendance Notification -->
          <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
              <div>
                <p class="font-medium text-gray-800">Notifikasi Absensi</p>
                <p class="text-sm text-gray-500">Notifikasi saat siswa melakukan absensi</p>
              </div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" v-model="notifications.attendance" class="sr-only peer">
              <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-yellow-600"></div>
            </label>
          </div>
          
          <!-- Logbook Notification -->
          <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
              </div>
              <div>
                <p class="font-medium text-gray-800">Notifikasi Logbook</p>
                <p class="text-sm text-gray-500">Notifikasi saat siswa mengirim logbook baru</p>
              </div>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
              <input type="checkbox" v-model="notifications.logbook" class="sr-only peer">
              <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
            </label>
          </div>
        </div>
        
        <div class="flex justify-end pt-6 mt-4 border-t">
          <button @click="saveNotifications" :disabled="saving" class="px-6 py-2.5 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 flex items-center gap-2 disabled:opacity-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ saving ? 'Menyimpan...' : 'Simpan Pengaturan' }}
          </button>
        </div>
      </div>
      
    </div>

    <!-- Info Footer -->
    <div class="bg-indigo-50 rounded-2xl p-5 border border-indigo-100">
      <div class="flex items-start gap-4">
        <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
          <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
        </div>
        <div>
          <p class="text-sm font-semibold text-indigo-800">Informasi Pengaturan</p>
          <p class="text-xs text-indigo-600">• Perubahan pengaturan akan langsung berlaku</p>
          <p class="text-xs text-indigo-600">• Beberapa pengaturan mungkin memerlukan refresh halaman</p>
          <p class="text-xs text-indigo-600">• Notifikasi WhatsApp masih dalam tahap pengembangan</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useToast } from 'vue-toastification'
import axios from '../../plugins/axios'

const toast = useToast()
const saving = ref(false)
const activeTab = ref('general')

const tabs = [
  { label: 'Umum', value: 'general', icon: '⚙️' },
  { label: 'Aturan PKL', value: 'rules', icon: '📋' },
  { label: 'Notifikasi', value: 'notifications', icon: '🔔' }
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
  attendance: true,
  logbook: true
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

const loadSettings = async () => {
  try {
    const [generalRes, rulesRes, notifRes] = await Promise.all([
      axios.get('/admin/settings/general').catch(() => ({ data: {} })),
      axios.get('/admin/settings/rules').catch(() => ({ data: {} })),
      axios.get('/admin/settings/notifications').catch(() => ({ data: {} }))
    ])
    
    if (generalRes.data) {
      settings.value = { ...settings.value, ...generalRes.data }
    }
    if (rulesRes.data) {
      rules.value = { ...rules.value, ...rulesRes.data }
    }
    if (notifRes.data) {
      notifications.value = { ...notifications.value, ...notifRes.data }
    }
  } catch (error) {
    console.error('Failed to load settings:', error)
  }
}

onMounted(() => {
  loadSettings()
})
</script>

<style scoped>
/* Smooth transitions */
input, select, textarea {
  transition: all 0.2s ease;
}

input:focus, select:focus, textarea:focus {
  outline: none;
}
</style>