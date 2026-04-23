<template>
  <div class="min-h-screen bg-white">
    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-500" :class="scrolled ? 'bg-white/95 backdrop-blur-xl shadow-2xl' : 'bg-transparent'">
      <div class="container mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
          <!-- Logo -->
          <div class="flex items-center gap-3 group cursor-pointer" @click="scrollTo('home')">
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center shadow-lg transition-all duration-300 group-hover:scale-110 group-hover:shadow-2xl">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
              </svg>
            </div>
            <div>
              <span class="text-xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Monitoring PKL</span>
              <span class="block text-xs text-gray-500">SMKN 1 Subang</span>
            </div>
          </div>

          <!-- Desktop Menu -->
          <div class="hidden lg:flex items-center gap-8">
            <button v-for="item in menuItems" :key="item.href" @click="scrollTo(item.href)" class="text-gray-600 hover:text-indigo-600 transition-all duration-300 font-medium hover:scale-105 cursor-pointer">
              {{ item.label }}
            </button>
          </div>

          <div class="flex items-center gap-4">
            <router-link 
              to="/register"
              class="hidden md:flex px-4 py-2 text-indigo-600 font-semibold hover:bg-indigo-50 rounded-xl transition"
            >
              Daftar
            </router-link>
            <router-link 
              to="/login"
              class="hidden md:flex px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:shadow-xl transition-all duration-300 hover:scale-105 font-semibold"
            >
              Masuk
            </router-link>
            <!-- Mobile Menu Button -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 rounded-lg hover:bg-gray-100">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
              </svg>
            </button>
          </div>
        </div>

        <!-- Mobile Menu -->
        <div v-if="mobileMenuOpen" class="lg:hidden mt-4 py-4 bg-white rounded-xl shadow-xl">
          <button v-for="item in menuItems" :key="item.href" @click="scrollTo(item.href)" class="block w-full text-left px-4 py-3 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 transition">
            {{ item.label }}
          </button>
          <router-link to="/register" class="block px-4 py-3 text-indigo-600 font-semibold hover:bg-indigo-50 transition" @click="mobileMenuOpen = false">Daftar</router-link>
          <router-link to="/login" class="block px-4 py-3 text-indigo-600 font-semibold hover:bg-indigo-50 transition" @click="mobileMenuOpen = false">Masuk</router-link>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="relative min-h-screen flex items-center overflow-hidden pt-20">
      <!-- Background -->
      <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 via-white to-purple-50"></div>
      <div class="absolute top-20 left-10 w-96 h-96 bg-indigo-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
      <div class="absolute top-40 right-10 w-96 h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
      <div class="absolute bottom-20 left-1/3 w-96 h-96 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>

      <div class="container mx-auto px-6 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
          <!-- Left Content -->
          <div class="text-center lg:text-left">
            <div class="inline-flex items-center gap-2 bg-indigo-100 px-4 py-2 rounded-full mb-6 animate-fade-in-up">
              <span class="w-2 h-2 bg-indigo-600 rounded-full animate-pulse"></span>
              <span class="text-sm text-indigo-600 font-semibold">✨ Sistem Informasi PKL Digital</span>
            </div>
            <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold leading-tight animate-fade-in-up animation-delay-100">
              <span class="bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent">Monitoring PKL</span>
              <br>
              <span class="text-gray-800">Revolusi Digital</span>
            </h1>
            <p class="text-xl text-gray-600 mt-6 leading-relaxed animate-fade-in-up animation-delay-200">
              Platform terintegrasi untuk memonitoring kegiatan Praktik Kerja Lapangan (PKL) secara real-time, efisien, dan modern.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 mt-8 justify-center lg:justify-start animate-fade-in-up animation-delay-300">
              <router-link to="/register" class="group px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-2xl transition-all duration-300 hover:scale-105 text-center flex items-center justify-center gap-2">
                Mulai Sekarang
                <svg class="w-5 h-5 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
              </router-link>
              <button @click="scrollTo('features')" class="px-8 py-3 bg-white text-gray-700 rounded-xl font-semibold border-2 border-gray-200 hover:border-indigo-600 hover:shadow-xl transition-all duration-300 text-center cursor-pointer">
                Lihat Fitur
              </button>
            </div>

            <!-- Trusted By -->
            <div class="mt-12 pt-8 border-t border-gray-200 animate-fade-in-up animation-delay-400">
              <p class="text-sm text-gray-500 mb-4">DIPERCAYA OLEH</p>
              <div class="flex flex-wrap gap-8 justify-center lg:justify-start opacity-60">
                <span class="text-gray-400 font-semibold">SMKN 1 Subang</span>
                <span class="text-gray-400 font-semibold">SMKN 2 Subang</span>
                <span class="text-gray-400 font-semibold">SMKN 3 Subang</span>
              </div>
            </div>
          </div>

          <!-- Right Illustration -->
          <div class="relative animate-fade-in-up animation-delay-200">
            <div class="relative rounded-2xl overflow-hidden shadow-2xl">
              <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=600&fit=crop" alt="Students" class="w-full h-auto">
              <div class="absolute inset-0 bg-gradient-to-t from-indigo-600/30 to-transparent"></div>
            </div>
            <!-- Floating Cards -->
            <div class="absolute -bottom-6 -left-6 bg-white rounded-xl shadow-2xl p-4 animate-float">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-semibold">Absensi Real-time</p>
                  <p class="text-xs text-gray-500">Dengan GPS & Radius</p>
                </div>
              </div>
            </div>
            <div class="absolute -top-6 -right-6 bg-white rounded-xl shadow-2xl p-4 animate-float animation-delay-2000">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                  <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                  </svg>
                </div>
                <div>
                  <p class="text-sm font-semibold">Laporan Otomatis</p>
                  <p class="text-xs text-gray-500">Export ke Excel</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-white border-y border-gray-100">
      <div class="container mx-auto px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
          <div v-for="stat in stats" :key="stat.label" class="text-center group">
            <div class="text-4xl font-bold text-indigo-600 group-hover:scale-110 transition">{{ stat.value }}+</div>
            <div class="text-sm text-gray-500 mt-1">{{ stat.label }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-gradient-to-b from-white to-gray-50">
      <div class="container mx-auto px-6">
        <div class="text-center max-w-3xl mx-auto mb-16">
          <span class="text-indigo-600 font-semibold text-sm uppercase tracking-wider">Fitur Unggulan</span>
          <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mt-2">Solusi Lengkap <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Monitoring PKL</span></h2>
          <p class="text-gray-600 mt-4 text-lg">Semua fitur dirancang untuk memudahkan koordinasi antara sekolah, siswa, dan perusahaan mitra.</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div v-for="(feature, idx) in features" :key="idx" class="group p-8 bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 border border-gray-100">
            <div class="w-16 h-16 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition duration-300">
              <component :is="feature.icon" class="w-8 h-8 text-indigo-600" />
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-3">{{ feature.title }}</h3>
            <p class="text-gray-500 leading-relaxed">{{ feature.description }}</p>
            <div class="mt-4 text-indigo-600 font-semibold text-sm opacity-0 group-hover:opacity-100 transition">Pelajari lebih lanjut →</div>
          </div>
        </div>
      </div>
    </section>

    <!-- How It Works -->
    <section class="py-24 bg-gradient-to-br from-indigo-50 to-purple-50">
      <div class="container mx-auto px-6">
        <div class="text-center max-w-3xl mx-auto mb-16">
          <span class="text-indigo-600 font-semibold text-sm uppercase tracking-wider">Cara Kerja</span>
          <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mt-2">Mudah Digunakan, <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Cepat & Efisien</span></h2>
        </div>

        <div class="grid md:grid-cols-4 gap-8">
          <div v-for="(step, idx) in steps" :key="idx" class="text-center group">
            <div class="relative">
              <div class="w-20 h-20 bg-indigo-600 rounded-2xl flex items-center justify-center text-white text-3xl font-bold mx-auto mb-6 shadow-xl group-hover:scale-110 transition duration-300 z-10 relative">
                {{ idx + 1 }}
              </div>
              <div v-if="idx < 3" class="hidden md:block absolute top-10 left-full w-full h-0.5 bg-indigo-200 -translate-x-1/2"></div>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-3">{{ step.title }}</h3>
            <p class="text-gray-500">{{ step.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section id="testimonials" class="py-24 bg-white">
      <div class="container mx-auto px-6">
        <div class="text-center max-w-3xl mx-auto mb-16">
          <span class="text-indigo-600 font-semibold text-sm uppercase tracking-wider">Testimoni</span>
          <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mt-2">Apa Kata <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Mereka?</span></h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
          <div v-for="(testimonial, idx) in testimonials" :key="idx" class="p-8 bg-gradient-to-br from-gray-50 to-white rounded-2xl shadow-lg hover:shadow-xl transition">
            <div class="flex items-center gap-4 mb-4">
              <div class="w-12 h-12 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                {{ testimonial.name.charAt(0) }}
              </div>
              <div>
                <h4 class="font-bold text-gray-800">{{ testimonial.name }}</h4>
                <p class="text-sm text-gray-500">{{ testimonial.role }}</p>
              </div>
            </div>
            <p class="text-gray-600 italic">"{{ testimonial.content }}"</p>
            <div class="flex text-yellow-400 mt-4">★★★★★</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing / Paket Section -->
    <section id="pricing" class="py-24 bg-gradient-to-br from-gray-50 to-indigo-50">
      <div class="container mx-auto px-6">
        <div class="text-center max-w-3xl mx-auto mb-16">
          <span class="text-indigo-600 font-semibold text-sm uppercase tracking-wider">Paket Layanan</span>
          <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mt-2">Pilih <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Paket</span> yang Sesuai</h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
          <div v-for="(pkg, idx) in pricing" :key="idx" class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition duration-300">
            <div class="p-8 text-center border-b">
              <h3 class="text-2xl font-bold text-gray-800">{{ pkg.name }}</h3>
              <div class="text-4xl font-bold text-indigo-600 mt-4">{{ pkg.price }}<span class="text-sm text-gray-500">/bulan</span></div>
            </div>
            <div class="p-8 space-y-3">
              <div v-for="feature in pkg.features" :key="feature" class="flex items-center gap-2 text-gray-600">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ feature }}
              </div>
              <button class="w-full mt-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-semibold hover:shadow-lg transition">
                Pilih Paket
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Blog / Artikel Section -->
    <section class="py-24 bg-white">
      <div class="container mx-auto px-6">
        <div class="text-center max-w-3xl mx-auto mb-16">
          <span class="text-indigo-600 font-semibold text-sm uppercase tracking-wider">Artikel & Berita</span>
          <h2 class="text-4xl md:text-5xl font-bold text-gray-800 mt-2">Informasi <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Terbaru</span></h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
          <div v-for="(article, idx) in articles" :key="idx" class="group rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition duration-300">
            <img :src="article.image" class="w-full h-48 object-cover group-hover:scale-110 transition duration-500">
            <div class="p-6">
              <span class="text-xs text-indigo-600 font-semibold">{{ article.date }}</span>
              <h3 class="text-xl font-bold text-gray-800 mt-2 group-hover:text-indigo-600 transition">{{ article.title }}</h3>
              <p class="text-gray-500 mt-2">{{ article.excerpt }}</p>
              <a href="#" class="inline-block mt-4 text-indigo-600 font-semibold hover:underline">Baca Selengkapnya →</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Download App Section -->
    <section class="py-20 bg-gradient-to-r from-indigo-600 to-purple-600">
      <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Unduh Aplikasi Mobile</h2>
        <p class="text-indigo-100 text-lg mb-8 max-w-2xl mx-auto">Akses lebih mudah dengan aplikasi mobile kami. Tersedia untuk Android dan iOS.</p>
        <div class="flex flex-wrap gap-4 justify-center">
          <button class="flex items-center gap-3 px-6 py-3 bg-black text-white rounded-xl hover:scale-105 transition">
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
              <path d="M17.523 3.651a3.5 3.5 0 0 0-3.5 3.5v.5h-4v-.5a3.5 3.5 0 0 0-7 0v.5H2v13h20v-13h-1.477v-.5a3.5 3.5 0 0 0-3.5-3.5zm-10.5 0a1.5 1.5 0 0 1 3 0v.5h-3v-.5zm7 0a1.5 1.5 0 0 1 3 0v.5h-3v-.5zM20 16H4v-9h1.023v.5a3.5 3.5 0 0 0 7 0v-.5h3.954v.5a3.5 3.5 0 0 0 7 0V7H20v9z"/>
            </svg>
            <div class="text-left">
              <p class="text-xs">Download on</p>
              <p class="font-semibold">Google Play</p>
            </div>
          </button>
          <button class="flex items-center gap-3 px-6 py-3 bg-black text-white rounded-xl hover:scale-105 transition">
            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
              <path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.02.07-.42 1.44-1.38 2.83M13 3.5c.73-.83 1.94-1.46 2.94-1.5.13 1.17-.34 2.35-1.04 3.19-.69.85-1.83 1.51-2.95 1.42-.15-1.15.41-2.35 1.05-3.11z"/>
            </svg>
            <div class="text-left">
              <p class="text-xs">Download on</p>
              <p class="font-semibold">App Store</p>
            </div>
          </button>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-24 bg-white">
      <div class="container mx-auto px-6 max-w-4xl">
        <div class="text-center mb-16">
          <span class="text-indigo-600 font-semibold text-sm uppercase tracking-wider">FAQ</span>
          <h2 class="text-4xl font-bold text-gray-800 mt-2">Pertanyaan <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Umum</span></h2>
        </div>

        <div class="space-y-4">
          <div v-for="(faq, idx) in faqs" :key="idx" class="border rounded-xl overflow-hidden">
            <button @click="faq.open = !faq.open" class="w-full flex justify-between items-center p-5 text-left font-semibold text-gray-800 hover:bg-gray-50 transition">
              {{ faq.question }}
              <svg class="w-5 h-5 transition-transform" :class="{ 'rotate-180': faq.open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </button>
            <div v-if="faq.open" class="p-5 pt-0 text-gray-500 border-t">{{ faq.answer }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- Newsletter Section -->
    <section id="contact" class="py-20 bg-gradient-to-r from-indigo-600 to-purple-600">
      <div class="container mx-auto px-6 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Tetap Terhubung</h2>
        <p class="text-indigo-100 mb-8">Dapatkan informasi terbaru tentang PKL dan pengumuman penting</p>
        <div class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
          <input type="email" placeholder="Email Anda" class="flex-1 px-4 py-3 rounded-xl border-0 focus:ring-2 focus:ring-white">
          <button class="px-6 py-3 bg-white text-indigo-600 rounded-xl font-semibold hover:shadow-xl transition">Berlangganan</button>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16">
      <div class="container mx-auto px-6">
        <div class="grid md:grid-cols-4 gap-8">
          <div>
            <div class="flex items-center gap-3 mb-4">
              <div class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
              </div>
              <span class="text-xl font-bold">Monitoring PKL</span>
            </div>
            <p class="text-gray-400 text-sm">Platform monitoring PKL terintegrasi untuk SMKN 1 Subang.</p>
          </div>
          <div>
            <h4 class="font-semibold mb-4">Tentang</h4>
            <ul class="space-y-2 text-gray-400 text-sm">
              <li><button @click="scrollTo('home')" class="hover:text-white transition">Tentang Kami</button></li>
              <li><button @click="scrollTo('features')" class="hover:text-white transition">Fitur</button></li>
              <li><a href="#" class="hover:text-white transition">Karir</a></li>
            </ul>
          </div>
          <div>
            <h4 class="font-semibold mb-4">Bantuan</h4>
            <ul class="space-y-2 text-gray-400 text-sm">
              <li><a href="#" class="hover:text-white transition">Panduan</a></li>
              <li><button @click="scrollTo('faq')" class="hover:text-white transition">FAQ</button></li>
              <li><button @click="scrollTo('contact')" class="hover:text-white transition">Kontak</button></li>
            </ul>
          </div>
          <div>
            <h4 class="font-semibold mb-4">Kontak</h4>
            <ul class="space-y-2 text-gray-400 text-sm">
              <li>📞 (0260) 123456</li>
              <li>✉️ info@smkn1subang.sch.id</li>
              <li>📍 Jl. Pendidikan No.1, Subang</li>
            </ul>
          </div>
        </div>
        <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400 text-sm">
          <p>&copy; 2024 Monitoring PKL SMKN 1 Subang. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import {
  MapPinIcon, DocumentTextIcon, ChartBarIcon,
  UserGroupIcon, ShieldCheckIcon, BellIcon
} from '@heroicons/vue/24/outline'

const scrolled = ref(false)
const mobileMenuOpen = ref(false)

const menuItems = [
  { href: 'home', label: 'Beranda' },
  { href: 'features', label: 'Fitur' },
  { href: 'testimonials', label: 'Testimoni' },
  { href: 'pricing', label: 'Paket' },
  { href: 'faq', label: 'FAQ' },
  { href: 'contact', label: 'Kontak' }
]

const stats = ref([
  { label: 'Siswa Terdaftar', value: '500' },
  { label: 'Perusahaan Mitra', value: '50' },
  { label: 'Logbook Tercatat', value: '1000' },
  { label: 'User Aktif', value: '600' }
])

const features = [
  { title: 'Absensi Real-time', description: 'Absensi dengan GPS dan validasi radius perusahaan, otomatis terdeteksi.', icon: MapPinIcon },
  { title: 'Jurnal Harian', description: 'Catat kegiatan PKL setiap hari dengan upload lampiran foto/dokumen.', icon: DocumentTextIcon },
  { title: 'Monitoring Aktif', description: 'Guru dan perusahaan dapat memantau progres siswa secara real-time.', icon: UserGroupIcon },
  { title: 'Laporan Otomatis', description: 'Generate laporan absensi dan jurnal dalam format Excel.', icon: ChartBarIcon },
  { title: 'Notifikasi', description: 'Dapatkan notifikasi untuk pengajuan izin dan penilaian.', icon: BellIcon },
  { title: 'Aman & Terpercaya', description: 'Sistem dengan keamanan tinggi dan data terenkripsi.', icon: ShieldCheckIcon }
]

const steps = [
  { title: 'Registrasi', description: 'Daftarkan akun sesuai role Anda' },
  { title: 'Penempatan', description: 'Ditempatkan di perusahaan mitra' },
  { title: 'Aktivitas', description: 'Lakukan absensi dan jurnal harian' },
  { title: 'Evaluasi', description: 'Dapatkan penilaian dari pembimbing' }
]

const testimonials = [
  { name: 'Ahmad Fauzi', role: 'Siswa PKL', content: 'Sistem ini sangat membantu saya dalam mencatat kegiatan PKL sehari-hari. Praktis dan mudah digunakan!' },
  { name: 'Ibu Siti Rohmah', role: 'Guru Pembimbing', content: 'Memudahkan monitoring siswa PKL, semua data tersimpan rapi dan bisa diakses kapan saja.' },
  { name: 'Bapak Irfan', role: 'Pembimbing Perusahaan', content: 'Sangat profesional, memudahkan koordinasi antara sekolah dan perusahaan.' }
]

const pricing = [
  { name: 'Gratis', price: 'Rp0', features: ['Akses dasar', '1 Perusahaan', 'Support email'] },
  { name: 'Pro', price: 'Rp99k', features: ['Semua fitur', 'Unlimited perusahaan', 'Priority support', 'Export data'] },
  { name: 'Enterprise', price: 'Kustom', features: ['Fitur lengkap', 'Dedicated support', 'API access', 'Training'] }
]

const articles = [
  { title: 'Panduan Penggunaan Sistem PKL', date: '15 Mar 2024', excerpt: 'Pelajari cara menggunakan sistem monitoring PKL dengan mudah...', image: 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=400&h=250&fit=crop' },
  { title: 'Kerjasama dengan 50 Perusahaan', date: '10 Mar 2024', excerpt: 'SMKN 1 Subang resmi menjalin kerjasama dengan 50 perusahaan...', image: 'https://images.unsplash.com/photo-1556761175-b413da4baf72?w=400&h=250&fit=crop' },
  { title: 'Tips Sukses PKL', date: '5 Mar 2024', excerpt: 'Tips dan trik agar sukses menjalani PKL di perusahaan...', image: 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=400&h=250&fit=crop' }
]

const faqs = ref([
  { question: 'Apa saja yang dibutuhkan untuk menggunakan sistem ini?', answer: 'Cukup memiliki koneksi internet dan browser modern. Untuk absensi GPS, diperlukan izin akses lokasi.', open: false },
  { question: 'Apakah data aman?', answer: 'Ya, data dienkripsi dan disimpan dengan aman di server kami.', open: false },
  { question: 'Bagaimana cara reset password?', answer: 'Klik lupa password pada halaman login, ikuti instruksi yang dikirim ke email.', open: false }
])

// Smooth scroll function
const scrollTo = (sectionId) => {
  const element = document.getElementById(sectionId)
  if (element) {
    element.scrollIntoView({ behavior: 'smooth', block: 'start' })
    mobileMenuOpen.value = false
  }
}

const handleScroll = () => {
  scrolled.value = window.scrollY > 50
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

<style scoped>
@keyframes blob {
  0% { transform: translate(0px, 0px) scale(1); }
  33% { transform: translate(30px, -50px) scale(1.1); }
  66% { transform: translate(-20px, 20px) scale(0.9); }
  100% { transform: translate(0px, 0px) scale(1); }
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes float {
  0% { transform: translateY(0px); }
  50% { transform: translateY(-10px); }
  100% { transform: translateY(0px); }
}

.animate-blob { animation: blob 7s infinite; }
.animation-delay-2000 { animation-delay: 2s; }
.animation-delay-4000 { animation-delay: 4s; }
.animate-fade-in-up { animation: fadeInUp 0.6s ease-out forwards; opacity: 0; }
.animation-delay-100 { animation-delay: 0.1s; }
.animation-delay-200 { animation-delay: 0.2s; }
.animation-delay-300 { animation-delay: 0.3s; }
.animation-delay-400 { animation-delay: 0.4s; }
.animate-float { animation: float 3s ease-in-out infinite; }

@media (max-width: 768px) {
  .animate-fade-in-up { animation-duration: 0.4s; }
  .text-5xl { font-size: 2.5rem; }
  .text-4xl { font-size: 2rem; }
}
</style>