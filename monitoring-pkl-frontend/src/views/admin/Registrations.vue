<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-2xl font-bold text-gray-800">Verifikasi Pendaftaran</h1>
      <p class="text-gray-500 mt-1">Kelola pendaftaran pengguna baru</p>
    </div>

    <!-- Tabs -->
    <div class="border-b">
      <div class="flex space-x-6">
        <button 
          v-for="tab in tabs" 
          :key="tab.value"
          @click="activeTab = tab.value"
          :class="activeTab === tab.value ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500'"
          class="px-1 py-3 border-b-2 font-medium text-sm"
        >
          {{ tab.label }}
          <span class="ml-2 text-xs bg-gray-100 px-2 py-0.5 rounded-full">{{ getCount(tab.value) }}</span>
        </button>
      </div>
    </div>

    <!-- Pending Registrations -->
    <div v-if="activeTab === 'pending'" class="space-y-4">
      <div v-for="reg in pendingRegistrations" :key="reg.id" class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex justify-between items-start">
          <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-full flex items-center justify-center" :class="getRoleColor(reg.role?.name)">
              <span class="text-lg">{{ getRoleIcon(reg.role?.name) }}</span>
            </div>
            <div>
              <h3 class="font-semibold text-gray-800">{{ reg.name }}</h3>
              <p class="text-sm text-gray-500">{{ reg.email }}</p>
              <div class="flex flex-wrap gap-3 mt-2 text-sm">
                <span v-if="reg.nisn" class="text-gray-500">NISN: {{ reg.nisn }}</span>
                <span v-if="reg.nomor_induk" class="text-gray-500">NIP: {{ reg.nomor_induk }}</span>
                <span v-if="reg.phone" class="text-gray-500">Telp: {{ reg.phone }}</span>
              </div>
              <span class="inline-block mt-2 px-2 py-1 rounded-full text-xs" :class="getRoleBadge(reg.role?.name)">
                {{ reg.role?.name }}
              </span>
            </div>
          </div>
          <div class="flex gap-2">
            <button @click="approve(reg.id)" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
              Setujui
            </button>
            <button @click="openRejectModal(reg)" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
              Tolak
            </button>
          </div>
        </div>
      </div>
      <div v-if="!pendingRegistrations.length" class="bg-white rounded-2xl p-12 text-center text-gray-500">
        <CheckCircleIcon class="w-16 h-16 mx-auto text-gray-300 mb-3" />
        <p>Tidak ada pendaftaran yang menunggu verifikasi</p>
      </div>
    </div>

    <!-- History Tab -->
    <div v-if="activeTab === 'history'" class="space-y-4">
      <div v-for="reg in registrationHistory" :key="reg.id" class="bg-white rounded-2xl shadow-sm p-6">
        <div class="flex justify-between items-start">
          <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-full flex items-center justify-center" :class="getRoleColor(reg.role?.name)">
              <span class="text-lg">{{ getRoleIcon(reg.role?.name) }}</span>
            </div>
            <div>
              <h3 class="font-semibold text-gray-800">{{ reg.name }}</h3>
              <p class="text-sm text-gray-500">{{ reg.email }}</p>
              <span class="inline-block mt-2 px-2 py-1 rounded-full text-xs" :class="reg.registration_status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                {{ reg.registration_status === 'approved' ? 'Disetujui' : 'Ditolak' }}
              </span>
              <p v-if="reg.rejection_reason" class="text-sm text-red-600 mt-2">Alasan: {{ reg.rejection_reason }}</p>
            </div>
          </div>
          <div class="text-right text-sm text-gray-500">
            <p>Diproses: {{ formatDate(reg.approved_at) }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Reject Modal -->
    <div v-if="showRejectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl p-6 w-full max-w-md">
        <h3 class="text-xl font-bold mb-4">Tolak Pendaftaran</h3>
        <textarea v-model="rejectReason" rows="4" class="w-full px-4 py-2 border rounded-lg" placeholder="Masukkan alasan penolakan..."></textarea>
        <div class="flex justify-end gap-3 mt-4">
          <button @click="closeRejectModal" class="px-4 py-2 border rounded-lg">Batal</button>
          <button @click="confirmReject" class="px-4 py-2 bg-red-600 text-white rounded-lg">Kirim</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'
import { CheckCircleIcon } from '@heroicons/vue/24/outline'

const toast = useToast()
const activeTab = ref('pending')
const pendingRegistrations = ref([])
const registrationHistory = ref([])
const showRejectModal = ref(false)
const selectedRegistration = ref(null)
const rejectReason = ref('')

const tabs = [
  { label: 'Menunggu Verifikasi', value: 'pending' },
  { label: 'Riwayat', value: 'history' }
]

const getRoleColor = (role) => {
  const colors = { Admin: 'bg-red-100', Guru: 'bg-blue-100', Siswa: 'bg-green-100', Perusahaan: 'bg-purple-100' }
  return colors[role] || 'bg-gray-100'
}

const getRoleIcon = (role) => {
  const icons = { Admin: '👑', Guru: '👨‍🏫', Siswa: '🎓', Perusahaan: '🏢' }
  return icons[role] || '👤'
}

const getRoleBadge = (role) => {
  const badges = { Admin: 'bg-red-100 text-red-800', Guru: 'bg-blue-100 text-blue-800', Siswa: 'bg-green-100 text-green-800', Perusahaan: 'bg-purple-100 text-purple-800' }
  return badges[role] || 'bg-gray-100 text-gray-800'
}

const getCount = (tab) => {
  if (tab === 'pending') return pendingRegistrations.value.length
  return registrationHistory.value.length
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID')
}

const fetchPending = async () => {
  try {
    const res = await axios.get('/admin/registrations')
    pendingRegistrations.value = res.data
  } catch (error) {
    console.error('Failed to fetch pending registrations:', error)
  }
}

const fetchHistory = async () => {
  try {
    const res = await axios.get('/admin/registrations/history')
    registrationHistory.value = res.data
  } catch (error) {
    console.error('Failed to fetch registration history:', error)
  }
}

const approve = async (id) => {
  try {
    await axios.post(`/admin/registrations/${id}/approve`)
    toast.success('Pendaftaran disetujui')
    fetchPending()
    fetchHistory()
  } catch (error) {
    toast.error('Gagal menyetujui pendaftaran')
  }
}

const openRejectModal = (reg) => {
  selectedRegistration.value = reg
  rejectReason.value = ''
  showRejectModal.value = true
}

const closeRejectModal = () => {
  showRejectModal.value = false
  selectedRegistration.value = null
  rejectReason.value = ''
}

const confirmReject = async () => {
  if (!rejectReason.value) {
    toast.error('Alasan penolakan harus diisi')
    return
  }
  try {
    await axios.post(`/admin/registrations/${selectedRegistration.value.id}/reject`, {
      reason: rejectReason.value
    })
    toast.success('Pendaftaran ditolak')
    closeRejectModal()
    fetchPending()
    fetchHistory()
  } catch (error) {
    toast.error('Gagal menolak pendaftaran')
  }
}

onMounted(() => {
  fetchPending()
  fetchHistory()
})
</script>