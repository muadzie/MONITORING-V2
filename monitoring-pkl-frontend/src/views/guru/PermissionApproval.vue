<template>
  <div class="space-y-6">
    <div><h1 class="text-2xl font-bold">Approval Izin Siswa</h1><p class="text-gray-500">Setujui atau tolak pengajuan izin/sakit siswa</p></div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="p-4 border-b bg-yellow-50"><h3 class="font-semibold">Menunggu Persetujuan</h3></div>
        <div v-for="item in pendingPermissions" :key="item.id" class="p-4 border-b hover:bg-gray-50">
          <div class="flex justify-between items-start">
            <div><p class="font-medium">{{ item.user_name }}</p><p class="text-sm text-gray-500">{{ item.type === 'sick' ? 'Sakit' : 'Izin' }} - {{ item.date }}</p><p class="text-sm mt-1">{{ item.reason }}</p></div>
            <div class="flex gap-2"><button @click="approve(item.id)" class="bg-green-600 text-white px-3 py-1 rounded text-sm">Setujui</button><button @click="reject(item.id)" class="bg-red-600 text-white px-3 py-1 rounded text-sm">Tolak</button></div>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="p-4 border-b bg-green-50"><h3 class="font-semibold">Riwayat Persetujuan</h3></div>
        <div v-for="item in history" :key="item.id" class="p-4 border-b hover:bg-gray-50">
          <div class="flex justify-between"><div><p class="font-medium">{{ item.user_name }}</p><p class="text-sm text-gray-500">{{ item.type === 'sick' ? 'Sakit' : 'Izin' }} - {{ item.date }}</p></div><span :class="item.status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 py-1 rounded-full text-xs">{{ item.status === 'approved' ? 'Disetujui' : 'Ditolak' }}</span></div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()
const pendingPermissions = ref([])
const history = ref([])

const loadData = async () => {
  try {
    const res1 = await axios.get('/guru/permissions/pending')
    pendingPermissions.value = res1.data
    const res2 = await axios.get('/guru/permissions/history')
    history.value = res2.data
  } catch(e) { console.error(e) }
}

const approve = async (id) => {
  try { await axios.put(`/guru/permissions/${id}/approve`); toast.success('Izin disetujui'); loadData() } catch(e) { toast.error('Gagal') }
}

const reject = async (id) => {
  try { await axios.put(`/guru/permissions/${id}/reject`); toast.success('Izin ditolak'); loadData() } catch(e) { toast.error('Gagal') }
}

onMounted(() => loadData())
</script>