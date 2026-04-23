<template>
  <div class="space-y-6">
    <div>
      <h1 class="text-2xl font-bold text-gray-800">Penilaian PKL</h1>
      <p class="text-gray-500 mt-1">Kelola nilai PKL siswa</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
      <div class="bg-white rounded-xl p-4 shadow-sm">
        <p class="text-gray-500 text-sm">Rata-rata Nilai</p>
        <p class="text-2xl font-bold text-purple-600">{{ averageGrade }}</p>
      </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-2xl shadow-sm p-4">
      <div class="flex flex-wrap gap-4">
        <select v-model="filters.teacher" class="px-4 py-2 border rounded-lg">
          <option value="">Semua Guru</option>
          <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">{{ teacher.name }}</option>
        </select>
        <select v-model="filters.company" class="px-4 py-2 border rounded-lg">
          <option value="">Semua Perusahaan</option>
          <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name }}</option>
        </select>
        <select v-model="filters.status" class="px-4 py-2 border rounded-lg">
          <option value="">Semua Status</option>
          <option value="assessed">Sudah Dinilai</option>
          <option value="unassessed">Belum Dinilai</option>
        </select>
        <button @click="fetchStudents" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Filter</button>
      </div>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">No</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">NISN</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Nama Siswa</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Perusahaan</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Guru Pembimbing</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Nilai Guru</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Nilai Perusahaan</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Nilai Akhir</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="(student, idx) in filteredStudents" :key="student.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 text-sm">{{ idx + 1 }}</td>
              <td class="px-6 py-4 text-sm">{{ student.nisn }}</td>
              <td class="px-6 py-4 font-medium">{{ student.name }}</td>
              <td class="px-6 py-4 text-sm">{{ student.company?.name || '-' }}</td>
              <td class="px-6 py-4 text-sm">{{ student.teacher?.name || '-' }}</td>
              <td class="px-6 py-4 text-sm font-semibold" :class="getScoreClass(student.guru_score)">{{ student.guru_score || '-' }}</td>
              <td class="px-6 py-4 text-sm font-semibold" :class="getScoreClass(student.perusahaan_score)">{{ student.perusahaan_score || '-' }}</td>
              <td class="px-6 py-4 text-sm font-bold text-indigo-600">{{ student.final_score || '-' }}</td>
              <td class="px-6 py-4">
                <button @click="openAssessmentModal(student)" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                  Input Nilai
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Assessment Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white p-6 border-b">
          <div class="flex justify-between items-center">
            <h3 class="text-xl font-bold">Input Nilai PKL</h3>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600">
              <XMarkIcon class="w-6 h-6" />
            </button>
          </div>
          <p class="text-gray-500 mt-1">Siswa: {{ selectedStudent?.name }}</p>
        </div>
        
        <form @submit.prevent="submitAssessment" class="p-6 space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Penilaian Guru -->
            <div class="border rounded-xl p-4">
              <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <UserIcon class="w-5 h-5 text-blue-600" />
                Penilaian Guru
              </h4>
              <div class="space-y-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Kehadiran</label>
                  <input v-model="form.guru.attendance" type="number" min="0" max="100" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Logbook</label>
                  <input v-model="form.guru.logbook" type="number" min="0" max="100" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Laporan</label>
                  <input v-model="form.guru.report" type="number" min="0" max="100" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Sikap</label>
                  <input v-model="form.guru.attitude" type="number" min="0" max="100" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Guru</label>
                  <textarea v-model="form.guru.notes" rows="2" class="w-full px-3 py-2 border rounded-lg" placeholder="Catatan untuk siswa..."></textarea>
                </div>
              </div>
            </div>

            <!-- Penilaian Perusahaan -->
            <div class="border rounded-xl p-4">
              <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <BuildingOffice2Icon class="w-5 h-5 text-purple-600" />
                Penilaian Perusahaan
              </h4>
              <div class="space-y-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Kinerja</label>
                  <input v-model="form.perusahaan.performance" type="number" min="0" max="100" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Kedisiplinan</label>
                  <input v-model="form.perusahaan.discipline" type="number" min="0" max="100" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Kerjasama</label>
                  <input v-model="form.perusahaan.cooperation" type="number" min="0" max="100" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Inisiatif</label>
                  <input v-model="form.perusahaan.initiative" type="number" min="0" max="100" class="w-full px-3 py-2 border rounded-lg">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Perusahaan</label>
                  <textarea v-model="form.perusahaan.notes" rows="2" class="w-full px-3 py-2 border rounded-lg" placeholder="Catatan untuk siswa..."></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- Nilai Akhir -->
          <div class="bg-gray-50 rounded-xl p-4">
            <div class="flex justify-between items-center">
              <span class="font-semibold text-gray-800">Nilai Akhir:</span>
              <span class="text-3xl font-bold text-indigo-600">{{ calculateFinalScore }}</span>
            </div>
            <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
              <div class="h-full bg-indigo-600 rounded-full" :style="{ width: calculateFinalScore + '%' }"></div>
            </div>
            <p class="text-xs text-gray-500 mt-2">*Nilai akhir dihitung dari rata-rata semua komponen</p>
          </div>

          <div class="flex justify-end gap-3 pt-4">
            <button type="button" @click="closeModal" class="px-4 py-2 border rounded-lg hover:bg-gray-50">Batal</button>
            <button type="submit" :disabled="saving" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50">
              {{ saving ? 'Menyimpan...' : 'Simpan Penilaian' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'
import { XMarkIcon, UserIcon, BuildingOffice2Icon } from '@heroicons/vue/24/outline'

const toast = useToast()
const loading = ref(false)
const saving = ref(false)
const showModal = ref(false)
const students = ref([])
const teachers = ref([])
const companies = ref([])
const selectedStudent = ref(null)

const filters = ref({
  teacher: '',
  company: '',
  status: ''
})

const form = ref({
  guru: { attendance: '', logbook: '', report: '', attitude: '', notes: '' },
  perusahaan: { performance: '', discipline: '', cooperation: '', initiative: '', notes: '' }
})

const assessedCount = computed(() => students.value.filter(s => s.guru_score || s.perusahaan_score).length)
const averageGrade = computed(() => {
  const scores = students.value.filter(s => s.final_score).map(s => s.final_score)
  if (!scores.length) return 0
  return Math.round(scores.reduce((a, b) => a + b, 0) / scores.length)
})

const filteredStudents = computed(() => {
  let result = students.value
  if (filters.value.teacher) {
    result = result.filter(s => s.teacher_id === filters.value.teacher)
  }
  if (filters.value.company) {
    result = result.filter(s => s.company_id === filters.value.company)
  }
  if (filters.value.status === 'assessed') {
    result = result.filter(s => s.guru_score || s.perusahaan_score)
  } else if (filters.value.status === 'unassessed') {
    result = result.filter(s => !s.guru_score && !s.perusahaan_score)
  }
  return result
})

const calculateFinalScore = computed(() => {
  const allScores = [
    form.value.guru.attendance,
    form.value.guru.logbook,
    form.value.guru.report,
    form.value.guru.attitude,
    form.value.perusahaan.performance,
    form.value.perusahaan.discipline,
    form.value.perusahaan.cooperation,
    form.value.perusahaan.initiative
  ].filter(v => v && !isNaN(v))
  
  if (!allScores.length) return 0
  return Math.round(allScores.reduce((a, b) => a + Number(b), 0) / allScores.length)
})

const getScoreClass = (score) => {
  if (!score) return 'text-gray-400'
  if (score >= 85) return 'text-green-600'
  if (score >= 70) return 'text-blue-600'
  if (score >= 60) return 'text-yellow-600'
  return 'text-red-600'
}

const fetchStudents = async () => {
  loading.value = true
  try {
    const res = await axios.get('/admin/students/assessments')
    students.value = res.data
  } catch (error) {
    console.error('Failed to fetch students:', error)
  } finally {
    loading.value = false
  }
}

const fetchTeachers = async () => {
  try {
    const res = await axios.get('/admin/teachers')
    teachers.value = res.data
  } catch (error) {
    console.error('Failed to fetch teachers:', error)
  }
}

const fetchCompanies = async () => {
  try {
    const res = await axios.get('/companies')
    companies.value = res.data
  } catch (error) {
    console.error('Failed to fetch companies:', error)
  }
}

const openAssessmentModal = (student) => {
  selectedStudent.value = student
  form.value = {
    guru: {
      attendance: student.guru_attendance || '',
      logbook: student.guru_logbook || '',
      report: student.guru_report || '',
      attitude: student.guru_attitude || '',
      notes: student.guru_notes || ''
    },
    perusahaan: {
      performance: student.perusahaan_performance || '',
      discipline: student.perusahaan_discipline || '',
      cooperation: student.perusahaan_cooperation || '',
      initiative: student.perusahaan_initiative || '',
      notes: student.perusahaan_notes || ''
    }
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  selectedStudent.value = null
}

const submitAssessment = async () => {
  saving.value = true
  try {
    await axios.post(`/admin/assessments/${selectedStudent.value.id}`, {
      guru: form.value.guru,
      perusahaan: form.value.perusahaan,
      final_score: calculateFinalScore.value
    })
    toast.success('Penilaian berhasil disimpan')
    closeModal()
    fetchStudents()
  } catch (error) {
    toast.error('Gagal menyimpan penilaian')
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  fetchStudents()
  fetchTeachers()
  fetchCompanies()
})
</script>