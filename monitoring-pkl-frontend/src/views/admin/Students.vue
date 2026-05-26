<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
      <div>
        <h1 class="text-2xl font-bold text-gray-800">Manajemen Siswa</h1>
        <p class="text-gray-500 mt-1">Kelola data siswa peserta PKL</p>
      </div>
      <div class="flex items-center gap-3">
        <button 
          @click="downloadTemplate"
          class="bg-white border-2 border-indigo-600 text-indigo-600 px-4 py-2.5 rounded-xl hover:bg-indigo-50 transition-all duration-300 flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          Download Template
        </button>
        <button 
          @click="openImportModal"
          class="bg-white border-2 border-emerald-600 text-emerald-600 px-4 py-2.5 rounded-xl hover:bg-emerald-50 transition-all duration-300 flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
          </svg>
          Import Excel
        </button>
        <button 
          @click="openModal" 
          class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-5 py-2.5 rounded-xl hover:shadow-lg transition-all duration-300 hover:scale-105 flex items-center gap-2"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          Tambah Siswa
        </button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-xl p-5 text-white shadow-lg hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm opacity-90">Total Siswa</p>
            <p class="text-3xl font-bold mt-1">{{ students.length }}</p>
          </div>
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
          </div>
        </div>
      </div>
      <div class="bg-gradient-to-br from-green-500 to-green-700 rounded-xl p-5 text-white shadow-lg hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm opacity-90">Ditempatkan</p>
            <p class="text-3xl font-bold mt-1">{{ students.filter(s => s.company_id).length }}</p>
          </div>
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
          </div>
        </div>
      </div>
      <div class="bg-gradient-to-br from-yellow-500 to-yellow-700 rounded-xl p-5 text-white shadow-lg hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm opacity-90">Belum Ditempatkan</p>
            <p class="text-3xl font-bold mt-1">{{ students.filter(s => !s.company_id).length }}</p>
          </div>
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
          </div>
        </div>
      </div>
      <div class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl p-5 text-white shadow-lg hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-sm opacity-90">Aktif</p>
            <p class="text-3xl font-bold mt-1">{{ students.filter(s => s.is_active !== false).length }}</p>
          </div>
          <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
        </div>
      </div>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white rounded-xl shadow-md p-5 border border-gray-100">
      <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1 relative">
          <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
          </svg>
          <input 
            v-model="search" 
            type="text" 
            placeholder="Cari siswa (NISN, Nama, Email)..." 
            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
          >
        </div>
        <select v-model="filters.company" class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white">
          <option value="">📌 Semua Perusahaan</option>
          <option v-for="c in companies" :key="c.id" :value="c.id">🏢 {{ c.name }}</option>
        </select>
        <select v-model="filters.teacher" class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white">
          <option value="">👨‍🏫 Semua Guru Pembimbing</option>
          <option v-for="g in teachers" :key="g.id" :value="g.id">👨‍🏫 {{ g.name }} - {{ g.nip || '-' }}</option>
        </select>
        <button @click="resetFilters" class="px-4 py-2.5 text-gray-600 hover:bg-gray-100 rounded-lg transition flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          Reset
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center py-12">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto"></div>
        <p class="mt-4 text-gray-500">Memuat data siswa...</p>
      </div>
    </div>

    <!-- Table -->
    <div v-else-if="filteredStudents.length > 0" class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
      <div class="overflow-x-auto">
        <table class="min-w-[1000px] lg:min-w-full bg-white">
          <thead>
            <tr class="bg-gradient-to-r from-indigo-600 to-purple-600">
              <th class="px-4 py-3 text-center text-xs font-semibold text-white uppercase w-12">No</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase w-28">NISN</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase w-36">Nama</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase w-44">Email</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase w-40">Perusahaan</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-white uppercase w-40">Guru Pembimbing</th>
              <th class="px-4 py-3 text-center text-xs font-semibold text-white uppercase w-20">Status</th>
              <th class="px-4 py-3 text-center text-xs font-semibold text-white uppercase w-20">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="(student, index) in filteredStudents" :key="student.id" class="hover:bg-gray-50 transition duration-200">
              <td class="px-4 py-3 text-sm text-gray-500 text-center">{{ index + 1 }}</td>
              <td class="px-4 py-3 text-sm font-mono text-gray-600 whitespace-nowrap">{{ student.nisn }}</td>
              <td class="px-4 py-3 font-medium text-gray-800 whitespace-nowrap">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-sm shadow-sm">
                    {{ student.name?.charAt(0) || 'S' }}
                  </div>
                  {{ student.name }}
                </div>
              </td>
              <td class="px-4 py-3 text-sm text-gray-600 truncate max-w-xs">{{ student.email }}</td>
              <td class="px-4 py-3">
                <span v-if="student.company" class="inline-flex items-center gap-1 px-2 py-1 bg-indigo-100 text-indigo-800 rounded-lg text-xs font-medium">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                  </svg>
                  {{ student.company.name }}
                </span>
                <span v-else class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-500 rounded-lg text-xs">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Belum ditempatkan
                </span>
              </td>
              <td class="px-4 py-3">
                <span v-if="student.teacher" class="inline-flex items-center gap-1 px-2 py-1 bg-purple-100 text-purple-800 rounded-lg text-xs font-medium">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                  </svg>
                  {{ student.teacher.name }}
                </span>
                <span v-else class="inline-flex items-center gap-1 px-2 py-1 bg-gray-100 text-gray-500 rounded-lg text-xs">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Belum ada guru pembimbing
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium" :class="student.is_active !== false ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                  <span class="w-1.5 h-1.5 rounded-full" :class="student.is_active !== false ? 'bg-green-500' : 'bg-red-500'"></span>
                  {{ student.is_active !== false ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <div class="flex items-center justify-center gap-2">
                  <button @click="editStudent(student)" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Edit">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                  </button>
                  <button @click="deleteStudent(student)" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="filteredStudents.length === 0 && !loading">
              <td colspan="8" class="px-6 py-12 text-center">
                <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                <p class="text-gray-500 text-lg">Belum ada data siswa</p>
                <p class="text-sm text-gray-400 mt-1">Silakan tambahkan siswa baru</p>
                <button @click="openModal" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Tambah siswa pertama</button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Modal Tambah/Edit -->
    <div v-if="showModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50" @click.self="closeModal">
      <div class="bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto animate-fade-in-up shadow-2xl">
        <div class="sticky top-0 bg-white p-5 border-b rounded-t-2xl">
          <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-gray-800">{{ isEdit ? 'Edit Siswa' : 'Tambah Siswa' }}</h3>
                <p class="text-sm text-gray-500">{{ isEdit ? 'Ubah data siswa' : 'Masukkan data siswa baru' }}</p>
              </div>
            </div>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <form @submit.prevent="submitStudent" class="p-6 space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">NISN <span class="text-red-500">*</span></label>
              <input v-model="form.nisn" type="text" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="0012345678" required>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
              <input v-model="form.name" type="text" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Ahmad Fauzi" required>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
              <input v-model="form.email" type="email" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="siswa@example.com" required>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">No. Telepon</label>
              <input v-model="form.phone" @input="formatPhone($event)" type="tel" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="628123456789">
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Perusahaan PKL</label>
              <select v-model="form.company_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white">
                <option value="">-- Pilih Perusahaan --</option>
                <option v-for="c in companies" :key="c.id" :value="c.id">🏢 {{ c.name }}</option>
              </select>
              <p class="text-xs text-gray-500 mt-1 mt-1">* Kosongkan jika belum ditempatkan</p>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Guru Pembimbing</label>
              <select v-model="form.teacher_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white">
                <option value="">-- Pilih Guru Pembimbing --</option>
                <option v-for="g in teachers" :key="g.id" :value="g.id">👨‍🏫 {{ g.name }} {{ g.nip ? `(${g.nip})` : '' }}</option>
              </select>
              <p class="text-xs text-gray-500 mt-1">* Pilih guru yang membimbing siswa ini</p>
            </div>
          </div>

          <!-- Info tambahan jika perusahaan dan guru dipilih -->
          <div v-if="form.company_id && form.teacher_id" class="bg-green-50 border border-green-200 rounded-lg p-3">
            <div class="flex items-center gap-2 text-green-700 text-sm">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <span>Siswa akan ditempatkan di <strong>{{ getSelectedCompanyName() }}</strong> dengan guru pembimbing <strong>{{ getSelectedTeacherName() }}</strong></span>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Password <span v-if="!isEdit" class="text-red-500">*</span></label>
              <input v-model="form.password" type="password" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" :required="!isEdit" placeholder="Minimal 6 karakter">
              <p v-if="isEdit" class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah password</p>
            </div>
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-1">Konfirmasi Password <span v-if="!isEdit" class="text-red-500">*</span></label>
              <input v-model="form.password_confirmation" type="password" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" :required="!isEdit" placeholder="Ulangi password">
            </div>
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t">
            <button type="button" @click="closeModal" class="px-5 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium">Batal</button>
            <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg hover:shadow-lg transition-all font-medium" :disabled="saving">
              {{ saving ? 'Menyimpan...' : (isEdit ? 'Update' : 'Simpan') }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal Import Excel -->
    <div v-if="showImportModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50" @click.self="closeImportModal">
      <div class="bg-white rounded-2xl w-full max-w-md max-h-[90vh] overflow-y-auto animate-fade-in-up shadow-2xl">
        <div class="sticky top-0 bg-white p-5 border-b rounded-t-2xl">
          <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
              <div class="w-10 h-10 bg-gradient-to-br from-emerald-600 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-xl font-bold text-gray-800">Import Data Siswa</h3>
                <p class="text-sm text-gray-500">Upload file Excel data siswa</p>
              </div>
            </div>
            <button @click="closeImportModal" class="text-gray-400 hover:text-gray-600 transition">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>

        <div class="p-6 space-y-4">
          <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-4 text-sm text-emerald-800">
            <p class="font-semibold mb-1">📋 Petunjuk:</p>
            <ol class="list-decimal list-inside space-y-1 text-emerald-700">
              <li>Download template Excel terlebih dahulu</li>
              <li>Isi data siswa sesuai format template</li>
              <li>Upload file Excel yang sudah diisi</li>
              <li>Email akan digenerate otomatis jika dikosongkan</li>
              <li>Password default: <strong>siswa123</strong></li>
            </ol>
          </div>

          <form @submit.prevent="importExcel">
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-emerald-500 transition cursor-pointer" @click="$refs.fileInput.click()" @dragover.prevent @drop.prevent="handleDrop">
              <input ref="fileInput" type="file" accept=".xlsx,.xls,.csv" class="hidden" @change="handleFileChange">
              <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
              </svg>
              <p v-if="!selectedFile" class="text-gray-500">Klik untuk pilih file Excel</p>
              <p v-else class="text-emerald-600 font-semibold">{{ selectedFile.name }}</p>
              <p class="text-xs text-gray-400 mt-1">Format: .xlsx, .xls, .csv (Max 5MB)</p>
            </div>

            <div class="flex justify-end gap-3 pt-4">
              <button type="button" @click="closeImportModal" class="px-5 py-2.5 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium">Batal</button>
              <button type="submit" :disabled="importing || !selectedFile" class="px-6 py-2.5 bg-gradient-to-r from-emerald-600 to-green-600 text-white rounded-lg hover:shadow-lg transition-all font-medium disabled:opacity-50">
                {{ importing ? 'Mengimport...' : 'Import Data' }}
              </button>
            </div>
          </form>

          <!-- Import Result -->
          <div v-if="importResult" class="border rounded-lg p-4" :class="importResult.success ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'">
            <p class="font-semibold" :class="importResult.success ? 'text-green-800' : 'text-red-800'">{{ importResult.message }}</p>
            <div v-if="importResult.errors && importResult.errors.length > 0" class="mt-2 max-h-32 overflow-y-auto">
              <p class="text-sm font-medium text-red-700">Peringatan:</p>
              <ul class="text-xs text-red-600 list-disc list-inside">
                <li v-for="(err, i) in importResult.errors" :key="i">{{ err }}</li>
              </ul>
            </div>
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
import { useConfirm } from '../../composables/useConfirm'

const toast = useToast()
const { confirm } = useConfirm()
const students = ref([])
const companies = ref([])
const teachers = ref([])
const loading = ref(false)
const saving = ref(false)
const showModal = ref(false)
const isEdit = ref(false)
const search = ref('')
const filters = ref({ company: '', teacher: '' })

const form = ref({
  id: null,
  nisn: '',
  name: '',
  email: '',
  phone: '',
  company_id: '',
  teacher_id: '',
  password: '',
  password_confirmation: ''
})

// Helper functions
const formatPhone = (e) => {
  let val = e.target.value.replace(/[^0-9]/g, '')
  if (val && !val.startsWith('62')) {
    val = '62' + val.replace(/^0/, '')
  }
  form.value.phone = val
}

const getSelectedCompanyName = () => {
  const company = companies.value.find(c => c.id === parseInt(form.value.company_id))
  return company ? company.name : ''
}

const getSelectedTeacherName = () => {
  const teacher = teachers.value.find(t => t.id === parseInt(form.value.teacher_id))
  return teacher ? teacher.name : ''
}

const filteredStudents = computed(() => {
  let result = students.value
  
  if (search.value) {
    const searchLower = search.value.toLowerCase()
    result = result.filter(s => 
      s.nisn?.toLowerCase().includes(searchLower) ||
      s.name?.toLowerCase().includes(searchLower) ||
      s.email?.toLowerCase().includes(searchLower)
    )
  }
  
  if (filters.value.company) {
    result = result.filter(s => s.company_id === parseInt(filters.value.company))
  }
  
  if (filters.value.teacher) {
    result = result.filter(s => s.teacher_id === parseInt(filters.value.teacher))
  }
  
  return result
})

const load = async () => {
  loading.value = true
  try {
    const [studentsRes, companiesRes, teachersRes] = await Promise.all([
      axios.get('/admin/students'),
      axios.get('/admin/companies'),
      axios.get('/admin/teachers')  // ← UBAH INI: gunakan endpoint yang benar
    ])
    
    students.value = studentsRes.data.data || studentsRes.data || []
    companies.value = companiesRes.data.data || companiesRes.data || []
    
    // Handle response teachers
    if (teachersRes.data.data) {
      teachers.value = teachersRes.data.data
    } else if (Array.isArray(teachersRes.data)) {
      teachers.value = teachersRes.data
    } else {
      teachers.value = []
    }
    
    console.log('Teachers loaded:', teachers.value) // Debugging
    
  } catch (error) {
    console.error('Load error:', error)
    toast.error('Gagal memuat data: ' + (error.response?.data?.message || error.message))
  } finally {
    loading.value = false
  }
}

// Fungsi lainnya tetap sama (openModal, editStudent, closeModal, submitStudent, deleteStudent, resetFilters)

const openModal = () => {
  isEdit.value = false
  form.value = {
    id: null,
    nisn: '',
    name: '',
    email: '',
    phone: '',
    company_id: '',
    teacher_id: '',
    password: '',
    password_confirmation: ''
  }
  showModal.value = true
}

const editStudent = (student) => {
  isEdit.value = true
  form.value = {
    id: student.id,
    nisn: student.nisn || '',
    name: student.name,
    email: student.email,
    phone: student.phone || '',
    company_id: student.company_id || '',
    teacher_id: student.teacher_id || '',
    password: '',
    password_confirmation: ''
  }
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
}

const submitStudent = async () => {
  if (form.value.password !== form.value.password_confirmation) {
    toast.error('Password dan konfirmasi password tidak sama')
    return
  }
  
  if (!isEdit.value && !form.value.password) {
    toast.error('Password harus diisi')
    return
  }
  
  if (form.value.password && form.value.password.length < 6) {
    toast.error('Password minimal 6 karakter')
    return
  }
  
  saving.value = true
  try {
    if (isEdit.value) {
      await axios.put(`/admin/students/${form.value.id}`, form.value)
      toast.success('Data siswa berhasil diupdate')
    } else {
      await axios.post('/admin/students', form.value)
      toast.success('Siswa berhasil ditambahkan')
    }
    closeModal()
    await load()
  } catch (error) {
    console.error('Submit error:', error)
    const errorMsg = error.response?.data?.message || error.response?.data?.errors || 'Gagal menyimpan data'
    toast.error(typeof errorMsg === 'string' ? errorMsg : 'Gagal menyimpan data')
  } finally {
    saving.value = false
  }
}

const deleteStudent = async (student) => {
  const ok = await confirm({ title: 'Hapus Siswa', message: `Hapus siswa "${student.name}"?` })
  if (!ok) return
  try {
    await axios.delete(`/admin/students/${student.id}`)
    toast.success('Siswa berhasil dihapus')
    await load()
  } catch (error) {
    toast.error(error.response?.data?.message || 'Gagal menghapus siswa')
  }
}

const resetFilters = () => {
  search.value = ''
  filters.value.company = ''
  filters.value.teacher = ''
}

// Import functionality
const showImportModal = ref(false)
const selectedFile = ref(null)
const importing = ref(false)
const importResult = ref(null)

const openImportModal = () => {
  showImportModal.value = true
  selectedFile.value = null
  importResult.value = null
}

const closeImportModal = () => {
  showImportModal.value = false
}

const downloadTemplate = async () => {
  try {
    const response = await axios.get('/admin/import/template-siswa', {
      responseType: 'blob'
    })
    const url = window.URL.createObjectURL(new Blob([response.data]))
    const link = document.createElement('a')
    link.href = url
    link.setAttribute('download', 'Template_Import_Siswa_PKL.xlsx')
    document.body.appendChild(link)
    link.click()
    link.remove()
    window.URL.revokeObjectURL(url)
    toast.success('Template berhasil di download')
  } catch (error) {
    toast.error('Gagal download template')
  }
}

const handleDrop = (e) => {
  const file = e.dataTransfer.files[0]
  if (file) validateAndSetFile(file)
}

const handleFileChange = (e) => {
  const file = e.target.files[0]
  if (file) validateAndSetFile(file)
}

const validateAndSetFile = (file) => {
  const allowedTypes = ['.xlsx', '.xls', '.csv']
  const ext = '.' + file.name.split('.').pop().toLowerCase()
  if (!allowedTypes.includes(ext)) {
    toast.error('Format file harus .xlsx, .xls, atau .csv')
    return
  }
  if (file.size > 5 * 1024 * 1024) {
    toast.error('File maksimal 5MB')
    return
  }
  selectedFile.value = file
}

const importExcel = async () => {
  if (!selectedFile.value) return

  importing.value = true
  importResult.value = null
  try {
    const formData = new FormData()
    formData.append('file', selectedFile.value)
    const response = await axios.post('/admin/import/siswa', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    importResult.value = response.data
    if (response.data.success) {
      toast.success(response.data.message)
      await load()
    }
  } catch (error) {
    const msg = error.response?.data?.message || 'Gagal mengimport data'
    importResult.value = { success: false, message: msg }
    toast.error(msg)
  } finally {
    importing.value = false
  }
}

onMounted(() => {
  load()
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