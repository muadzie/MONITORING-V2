<template>
  <div class="space-y-6">
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-2xl font-bold">Penempatan PKL</h1>
        <p class="text-gray-500">Kelola penempatan siswa ke perusahaan</p>
      </div>
      <button class="btn-primary">+ Tambah Penempatan</button>
    </div>
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <table class="w-full">
        <thead class="bg-gray-50">
          <tr><th class="px-6 py-3 text-left">Siswa</th><th class="px-6 py-3 text-left">Perusahaan</th><th class="px-6 py-3 text-left">Periode</th><th class="px-6 py-3 text-left">Status</th><th class="px-6 py-3 text-left">Aksi</th></tr></thead>
          <tbody><tr><td colspan="5" class="text-center py-8 text-gray-500">Belum ada data penempatan</td></tr></tbody>
        </table>
      </div>
    </div>
  </template>