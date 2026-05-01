<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 p-8 text-white">
      <div class="relative z-10 flex items-center gap-4">
        <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
          <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
          </svg>
        </div>
        <div>
          <h1 class="text-3xl font-bold">Penilaian PKL</h1>
          <p class="mt-2 text-indigo-100">Kelola dan input nilai PKL siswa</p>
        </div>
      </div>
    </div>

    <!-- Stats Cards Premium -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-gradient-to-br from-blue-500 to-blue-700 rounded-2xl p-5 text-white shadow-lg hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm opacity-90">Total Siswa</p>
            <p class="text-3xl font-bold mt-1">{{ students.length }}</p>
          </div>
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
          </div>
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-green-500 to-green-700 rounded-2xl p-5 text-white shadow-lg hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm opacity-90">Sudah Dinilai</p>
            <p class="text-3xl font-bold mt-1">{{ assessedCount }}</p>
          </div>
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
        <div class="mt-2 text-xs opacity-75">{{ percentageAssessed }}% dari total</div>
      </div>
      
      <div class="bg-gradient-to-br from-yellow-500 to-yellow-700 rounded-2xl p-5 text-white shadow-lg hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm opacity-90">Belum Dinilai</p>
            <p class="text-3xl font-bold mt-1">{{ students.length - assessedCount }}</p>
          </div>
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
        </div>
      </div>
      
      <div class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-2xl p-5 text-white shadow-lg hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm opacity-90">Rata-rata Nilai</p>
            <p class="text-3xl font-bold mt-1">{{ averageGrade }}</p>
          </div>
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
            </svg>
          </div>
        </div>
        <div class="mt-2 text-xs opacity-75">Dari {{ assessedCount }} siswa</div>
      </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-2xl shadow-lg p-5 border border-gray-100">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-800">🔍 Filter Data</h3>
        <button @click="resetFilters" class="text-sm text-gray-500 hover:text-indigo-600 transition flex items-center gap-1">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          Reset
        </button>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">👨‍🏫 Guru Pembimbing</label>
          <select v-model="filters.teacher" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
            <option value="">Semua Guru</option>
            <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">{{ teacher.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">🏢 Perusahaan</label>
          <select v-model="filters.company" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
            <option value="">Semua Perusahaan</option>
            <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name }}</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">📊 Status Penilaian</label>
          <select v-model="filters.status" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
            <option value="">Semua Status</option>
            <option value="assessed">Sudah Dinilai</option>
            <option value="unassessed">Belum Dinilai</option>
          </select>
        </div>
      </div>
      
      <div class="mt-4 flex justify-end">
        <button @click="fetchStudents" class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
          Terapkan Filter
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white rounded-2xl shadow-sm p-12 text-center">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
      <p class="mt-4 text-gray-500">Memuat data siswa...</p>
    </div>

    <!-- Students Table -->
    <div v-else class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
      <div class="overflow-x-auto">
        <table class="min-w-[1000px] w-full">
          <thead>
            <tr class="bg-gradient-to-r from-indigo-600 to-purple-600">
              <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">No</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">NISN</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Nama Siswa</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Kelas</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Perusahaan</th>
              <th class="px-6 py-4 text-left text-xs font-semibold text-white uppercase">Guru Pembimbing</th>
              <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase">Nilai Guru</th>
              <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase">Nilai Perusahaan</th>
              <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase">Nilai Akhir</th>
              <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase">Status</th>
              <th class="px-6 py-4 text-center text-xs font-semibold text-white uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="(student, idx) in paginatedStudents" :key="student.id" class="hover:bg-gray-50 transition duration-200">
              <td class="px-6 py-4 text-sm text-gray-500">{{ idx + 1 + (currentPage - 1) * itemsPerPage }}</td>
              <td class="px-6 py-4 text-sm font-mono text-gray-600">{{ student.nisn || '-' }}</td>
              <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                  <div class="w-9 h-9 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-xl flex items-center justify-center text-indigo-600 font-bold">
                    {{ student.name?.charAt(0) || 'S' }}
                  </div>
                  <div>
                    <p class="font-semibold text-gray-800">{{ student.name }}</p>
                    <p class="text-xs text-gray-400">{{ student.email }}</p>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 text-sm text-gray-600">{{ student.class?.name || student.kelas || '-' }}</td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-xs font-medium">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                  {{ student.company?.name || '-' }}
                </span>
              </td>
              <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-100 text-purple-700 rounded-lg text-xs font-medium">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                  </svg>
                  {{ student.teacher?.name || '-' }}
                </span>
              </td>
              <td class="px-6 py-4 text-center">
                <span v-if="student.guru_score" :class="getScoreBadgeClass(student.guru_score)" class="inline-flex items-center justify-center w-12 h-12 rounded-full font-bold text-lg">
                  {{ student.guru_score }}
                </span>
                <span v-else class="text-gray-400 text-xs">-</span>
              </td>
              <td class="px-6 py-4 text-center">
                <span v-if="student.perusahaan_score" :class="getScoreBadgeClass(student.perusahaan_score)" class="inline-flex items-center justify-center w-12 h-12 rounded-full font-bold text-lg">
                  {{ student.perusahaan_score }}
                </span>
                <span v-else class="text-gray-400 text-xs">-</span>
              </td>
              <td class="px-6 py-4 text-center">
                <span v-if="student.final_score" :class="getScoreBadgeClass(student.final_score)" class="inline-flex items-center justify-center w-12 h-12 rounded-full font-bold text-lg shadow-sm">
                  {{ student.final_score }}
                </span>
                <span v-else class="text-gray-400 text-sm">Belum</span>
              </td>
              <td class="px-6 py-4 text-center">
                <span :class="getStatusBadgeClass(student)" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium">
                  <span class="w-1.5 h-1.5 rounded-full" :class="getStatusDotClass(student)"></span>
                  {{ getStatusText(student) }}
                </span>
              </td>
              <td class="px-6 py-4 text-center">
                <button 
                  @click="openAssessmentModal(student)" 
                  class="px-3 py-1.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg text-sm font-medium hover:shadow-lg transition-all duration-300 hover:scale-105"
                >
                  Input Nilai
                </button>
              </td>
            </tr>
            <tr v-if="filteredStudents.length === 0">
              <td :colspan="11" class="text-center py-12 text-gray-500">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p>Tidak ada data siswa</p>
                <p class="text-sm mt-1">Silakan coba filter yang lain</p>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="filteredStudents.length > 0" class="px-6 py-4 border-t border-gray-100 flex justify-between items-center">
        <div class="text-sm text-gray-500">
          Menampilkan {{ ((currentPage - 1) * itemsPerPage) + 1 }} - {{ Math.min(currentPage * itemsPerPage, filteredStudents.length) }} dari {{ filteredStudents.length }} siswa
        </div>
        <div class="flex gap-2">
          <button 
            @click="prevPage" 
            :disabled="currentPage === 1"
            class="px-3 py-1 border rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition"
          >
            Previous
          </button>
          <button 
            @click="nextPage" 
            :disabled="currentPage === totalPages"
            class="px-3 py-1 border rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition"
          >
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- Assessment Modal Premium -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50" @click.self="closeModal">
      <div class="bg-white rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto animate-fade-in-up shadow-2xl">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-gradient-to-r from-indigo-600 to-purple-600 p-5 text-white rounded-t-2xl">
          <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold">Input Nilai PKL</h3>
                <p class="text-sm text-indigo-100">{{ selectedStudent?.name }} ({{ selectedStudent?.nisn }})</p>
              </div>
            </div>
            <button @click="closeModal" class="text-white/80 hover:text-white transition">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
        
        <form @submit.prevent="submitAssessment" class="p-6 space-y-6">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Penilaian Guru -->
            <div class="border rounded-xl overflow-hidden">
              <div class="bg-blue-50 px-4 py-3 border-b">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                  </div>
                  <h4 class="font-semibold text-gray-800">Penilaian Guru Pembimbing</h4>
                </div>
              </div>
              <div class="p-4 space-y-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">📝 Nilai Kehadiran</label>
                  <input v-model="form.guru.attendance" type="number" min="0" max="100" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">📓 Nilai Logbook</label>
                  <input v-model="form.guru.logbook" type="number" min="0" max="100" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">📄 Nilai Laporan</label>
                  <input v-model="form.guru.report" type="number" min="0" max="100" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">⭐ Nilai Sikap</label>
                  <input v-model="form.guru.attitude" type="number" min="0" max="100" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">📌 Catatan Guru</label>
                  <textarea v-model="form.guru.notes" rows="2" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Catatan untuk siswa..."></textarea>
                </div>
              </div>
            </div>

            <!-- Penilaian Perusahaan -->
            <div class="border rounded-xl overflow-hidden">
              <div class="bg-purple-50 px-4 py-3 border-b">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                  </div>
                  <h4 class="font-semibold text-gray-800">Penilaian Perusahaan</h4>
                </div>
              </div>
              <div class="p-4 space-y-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">🎯 Nilai Kinerja</label>
                  <input v-model="form.perusahaan.performance" type="number" min="0" max="100" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">📋 Nilai Kedisiplinan</label>
                  <input v-model="form.perusahaan.discipline" type="number" min="0" max="100" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">🤝 Nilai Kerjasama</label>
                  <input v-model="form.perusahaan.cooperation" type="number" min="0" max="100" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">💡 Nilai Inisiatif</label>
                  <input v-model="form.perusahaan.initiative" type="number" min="0" max="100" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">📌 Catatan Perusahaan</label>
                  <textarea v-model="form.perusahaan.notes" rows="2" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-purple-500" placeholder="Catatan untuk siswa..."></textarea>
                </div>
              </div>
            </div>
          </div>

          <!-- Nilai Akhir -->
          <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl p-5">
            <div class="flex justify-between items-center mb-3">
              <span class="font-semibold text-gray-800">🎯 Nilai Akhir:</span>
              <span class="text-4xl font-bold text-indigo-600">{{ calculateFinalScore }}</span>
            </div>
            <div class="w-full h-3 bg-gray-200 rounded-full overflow-hidden">
              <div class="h-full bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full transition-all duration-500" :style="{ width: calculateFinalScore + '%' }"></div>
            </div>
            <div class="flex justify-between mt-2 text-xs text-gray-500">
              <span>0</span>
              <span>50</span>
              <span>100</span>
            </div>
            <p class="text-xs text-gray-500 mt-3">*Nilai akhir dihitung dari rata-rata semua komponen penilaian</p>
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="closeModal" class="px-5 py-2.5 border border-gray-300 rounded-xl hover:bg-gray-50 transition font-medium">Batal</button>
            <button type="submit" :disabled="saving" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium flex items-center gap-2 disabled:opacity-50">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              {{ saving ? 'Menyimpan...' : 'Simpan Penilaian' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from '../../plugins/axios'
import { useToast } from 'vue-toastification'

const toast = useToast()

// State
const loading = ref(false)
const saving = ref(false)
const showModal = ref(false)
const students = ref([])
const teachers = ref([])
const companies = ref([])
const selectedStudent = ref(null)
const currentPage = ref(1)
const itemsPerPage = ref(10)

// Filters
const filters = ref({
  teacher: '',
  company: '',
  status: ''
})

// Form data
const form = ref({
  guru: { attendance: '', logbook: '', report: '', attitude: '', notes: '' },
  perusahaan: { performance: '', discipline: '', cooperation: '', initiative: '', notes: '' }
})

// Computed
const assessedCount = computed(() => students.value.filter(s => s.guru_score || s.perusahaan_score).length)
const percentageAssessed = computed(() => {
  if (students.value.length === 0) return 0
  return Math.round((assessedCount.value / students.value.length) * 100)
})

const averageGrade = computed(() => {
  const scores = students.value.filter(s => s.final_score).map(s => s.final_score)
  if (scores.length === 0) return 0
  return Math.round(scores.reduce((a, b) => a + b, 0) / scores.length)
})

const filteredStudents = computed(() => {
  let result = [...students.value]
  if (filters.value.teacher) {
    result = result.filter(s => s.teacher_id === parseInt(filters.value.teacher))
  }
  if (filters.value.company) {
    result = result.filter(s => s.company_id === parseInt(filters.value.company))
  }
  if (filters.value.status === 'assessed') {
    result = result.filter(s => s.guru_score || s.perusahaan_score)
  } else if (filters.value.status === 'unassessed') {
    result = result.filter(s => !s.guru_score && !s.perusahaan_score)
  }
  return result
})

const totalPages = computed(() => Math.ceil(filteredStudents.value.length / itemsPerPage.value))
const paginatedStudents = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value
  return filteredStudents.value.slice(start, start + itemsPerPage.value)
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
  ].filter(v => v && !isNaN(v) && v !== '')
  
  if (allScores.length === 0) return 0
  const total = allScores.reduce((a, b) => a + Number(b), 0)
  return Math.round(total / allScores.length)
})

// Helper functions
const getScoreBadgeClass = (score) => {
  if (!score) return ''
  if (score >= 85) return 'bg-green-100 text-green-700 ring-2 ring-green-200'
  if (score >= 70) return 'bg-blue-100 text-blue-700 ring-2 ring-blue-200'
  if (score >= 60) return 'bg-yellow-100 text-yellow-700 ring-2 ring-yellow-200'
  return 'bg-red-100 text-red-700 ring-2 ring-red-200'
}

const getStatusBadgeClass = (student) => {
  if (student.guru_score && student.perusahaan_score) return 'bg-green-100 text-green-700'
  if (student.guru_score || student.perusahaan_score) return 'bg-yellow-100 text-yellow-700'
  return 'bg-gray-100 text-gray-500'
}

const getStatusDotClass = (student) => {
  if (student.guru_score && student.perusahaan_score) return 'bg-green-500'
  if (student.guru_score || student.perusahaan_score) return 'bg-yellow-500'
  return 'bg-gray-400'
}

const getStatusText = (student) => {
  if (student.guru_score && student.perusahaan_score) return 'Lengkap'
  if (student.guru_score || student.perusahaan_score) return 'Sebagian'
  return 'Belum Dinilai'
}

// API Calls
const fetchStudents = async () => {
  loading.value = true
  try {
    const res = await axios.get('/admin/students/assessments')
    console.log('Data dari API:', res.data) // Cek di console browser
    students.value = res.data || []
    currentPage.value = 1
  } catch (error) {
    console.error('Failed to fetch students:', error)
    toast.error('Gagal memuat data siswa')
    students.value = []
  } finally {
    loading.value = false
  }
}

const fetchTeachers = async () => {
  try {
    const res = await axios.get('/admin/teachers')
    teachers.value = res.data || []
  } catch (error) {
    console.error('Failed to fetch teachers:', error)
  }
}

const fetchCompanies = async () => {
  try {
    const res = await axios.get('/companies')
    companies.value = res.data || []
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
  currentPage.value = 1
}

const submitAssessment = async () => {
  saving.value = true
  try {
    const payload = {
      guru: {
        attendance: form.value.guru.attendance || null,
        logbook: form.value.guru.logbook || null,
        report: form.value.guru.report || null,
        attitude: form.value.guru.attitude || null,
        notes: form.value.guru.notes || null
      },
      perusahaan: {
        performance: form.value.perusahaan.performance || null,
        discipline: form.value.perusahaan.discipline || null,
        notes: form.value.perusahaan.notes || null
      },
      final_score: calculateFinalScore.value
    }
    
    console.log('Payload:', payload)
    
    await axios.post(`/admin/assessments/${selectedStudent.value.id}`, payload)
    toast.success('Penilaian berhasil disimpan')
    closeModal()
    await fetchStudents()
  } catch (error) {
    console.error('Save error:', error)
    toast.error(error.response?.data?.message || 'Gagal menyimpan penilaian')
  } finally {
    saving.value = false
  }
}

const resetFilters = () => {
  filters.value = { teacher: '', company: '', status: '' }
  fetchStudents()
}

const prevPage = () => { if (currentPage.value > 1) currentPage.value-- }
const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++ }

// Watch for filter changes to reset page
watch([() => filters.value.teacher, () => filters.value.company, () => filters.value.status], () => {
  currentPage.value = 1
})

onMounted(() => {
  fetchStudents()
  fetchTeachers()
  fetchCompanies()
})
</script>

<style scoped>
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fadeInUp 0.3s ease-out;
}
</style>