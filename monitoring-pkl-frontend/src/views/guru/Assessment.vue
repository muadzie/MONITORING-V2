<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-2xl font-bold text-gray-800">Penilaian PKL</h1>
      <p class="text-gray-500 mt-1">Input nilai untuk siswa bimbingan Anda</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white rounded-xl p-4 shadow-sm">
        <p class="text-gray-500 text-sm">Total Siswa</p>
        <p class="text-2xl font-bold text-indigo-600">{{ students.length }}</p>
      </div>
      <div class="bg-white rounded-xl p-4 shadow-sm">
        <p class="text-gray-500 text-sm">Sudah Dinilai</p>
        <p class="text-2xl font-bold text-green-600">{{ assessedCount }}</p>
      </div>
      <div class="bg-white rounded-xl p-4 shadow-sm">
        <p class="text-gray-500 text-sm">Belum Dinilai</p>
        <p class="text-2xl font-bold text-yellow-600">{{ students.length - assessedCount }}</p>
      </div>
    </div>

    <!-- Students List -->
    <div class="space-y-4">
      <div v-for="student in students" :key="student.id" class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
          <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold">
              {{ student.name.charAt(0) }}
            </div>
            <div>
              <h3 class="font-semibold text-gray-800">{{ student.name }}</h3>
              <p class="text-sm text-gray-500">NISN: {{ student.nisn }} | {{ student.company?.name || 'Belum ditempatkan' }}</p>
            </div>
          </div>
          <span :class="student.assessment ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'" class="px-2 py-1 rounded-full text-xs">
            {{ student.assessment ? 'Sudah Dinilai' : 'Belum Dinilai' }}
          </span>
        </div>
        
        <div class="p-4">
          <form @submit.prevent="submitAssessment(student)">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Kehadiran</label>
                <input type="number" v-model="student.form.attendance_score" min="0" max="100" class="w-full px-3 py-2 border rounded-lg">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Logbook</label>
                <input type="number" v-model="student.form.logbook_score" min="0" max="100" class="w-full px-3 py-2 border rounded-lg">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Laporan</label>
                <input type="number" v-model="student.form.report_score" min="0" max="100" class="w-full px-3 py-2 border rounded-lg">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Sikap</label>
                <input type="number" v-model="student.form.attitude_score" min="0" max="100" class="w-full px-3 py-2 border rounded-lg">
              </div>
            </div>
            <div class="mt-3">
              <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
              <textarea v-model="student.form.notes" rows="2" class="w-full px-3 py-2 border rounded-lg" placeholder="Catatan untuk siswa..."></textarea>
            </div>
            <div class="mt-3 flex justify-end">
              <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Simpan Penilaian
              </button>
            </div>
          </form>
          
          <!-- Tampilkan nilai jika sudah ada -->
          <div v-if="student.assessment" class="mt-3 p-3 bg-gray-50 rounded-lg">
            <p class="text-sm font-semibold">Nilai Terakhir:</p>
            <div class="grid grid-cols-4 gap-2 mt-1 text-sm">
              <div>Kehadiran: <span class="font-semibold">{{ student.assessment.attendance_score || '-' }}</span></div>
              <div>Logbook: <span class="font-semibold">{{ student.assessment.logbook_score || '-' }}</span></div>
              <div>Laporan: <span class="font-semibold">{{ student.assessment.report_score || '-' }}</span></div>
              <div>Sikap: <span class="font-semibold">{{ student.assessment.attitude_score || '-' }}</span></div>
            </div>
            <p class="text-sm mt-1">Nilai Akhir: <span class="font-bold text-indigo-600">{{ student.assessment.final_score || '-' }}</span></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()
const students = ref([])

const assessedCount = computed(() => students.value.filter(s => s.assessment).length)

const fetchStudents = async () => {
  try {
    const res = await axios.get('/guru/students')
    students.value = res.data.map(s => ({
      ...s,
      form: {
        attendance_score: s.assessment?.attendance_score || '',
        logbook_score: s.assessment?.logbook_score || '',
        report_score: s.assessment?.report_score || '',
        attitude_score: s.assessment?.attitude_score || '',
        notes: s.assessment?.notes || ''
      }
    }))
  } catch (error) {
    console.error('Failed to fetch students:', error)
  }
}

const submitAssessment = async (student) => {
  try {
    await axios.post('/assessments', {
      student_id: student.id,
      attendance_score: student.form.attendance_score,
      logbook_score: student.form.logbook_score,
      report_score: student.form.report_score,
      attitude_score: student.form.attitude_score,
      notes: student.form.notes
    })
    toast.success('Penilaian berhasil disimpan')
    await fetchStudents()
  } catch (error) {
    toast.error('Gagal menyimpan penilaian')
  }
}

onMounted(() => fetchStudents())
</script>