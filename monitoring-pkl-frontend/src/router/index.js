import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [
  // ================================================================
  // ===================== PUBLIC ROUTES ===========================
  // ================================================================
  {
    path: '/',
    name: 'Landing',
    component: () => import('../views/LandingPage.vue'),
    meta: { guest: true }
  },
  { 
    path: '/register', 
    name: 'Register', 
    component: () => import('../views/auth/Register.vue'), 
    meta: { guest: true } 
  },
  { 
    path: '/login', 
    name: 'Login', 
    component: () => import('../views/auth/Login.vue'), 
    meta: { guest: true } 
  },

  // ================================================================
  // ===================== ADMIN ROUTES ============================
  // ================================================================
  { 
    path: '/admin', 
    component: () => import('../layouts/AdminLayout.vue'), 
    meta: { auth: true, role: 'Admin' },
    children: [
      { path: 'dashboard', component: () => import('../views/admin/Dashboard.vue') },
      { path: 'users', component: () => import('../views/admin/Users.vue') },
      { path: 'roles', component: () => import('../views/admin/Roles.vue') },
      { path: 'students', component: () => import('../views/admin/Students.vue') },
      { path: 'teachers', component: () => import('../views/admin/Teachers.vue') },
      { path: 'companies', component: () => import('../views/admin/Companies.vue') },
      { path: 'classes', component: () => import('../views/admin/Classes.vue') },
      { path: 'placements', component: () => import('../views/admin/Placements.vue') },
      { path: 'map', component: () => import('../views/admin/MapDistribution.vue') },
      { path: 'monitoring/attendance', component: () => import('../views/admin/monitoring/Attendance.vue') },
      { path: 'monitoring/logbook', component: () => import('../views/admin/monitoring/Logbook.vue') },
      { path: 'monitoring/progress', component: () => import('../views/admin/monitoring/Progress.vue') },
      { path: '/admin/reports', name: 'Reports', component: () => import('../views/admin/Reports.vue'), meta: { requiresAuth: true, role: 'admin' }},
      { path: 'assessments', component: () => import('../views/admin/Assessments.vue') },
      { path: 'settings/general', component: () => import('../views/admin/Settings.vue') },
      { path: 'settings/school', component: () => import('../views/admin/Settings.vue') },
      { path: 'settings/academic', component: () => import('../views/admin/Settings.vue') },
      { path: 'settings/radius', component: () => import('../views/admin/Settings.vue') },
      { path: 'profile', component: () => import('../views/shared/Profile.vue') },
      { path: '', redirect: '/admin/dashboard' },
    ] 
  },

  // ================================================================
  // ===================== SISWA ROUTES ============================
  // ================================================================
  { 
    path: '/siswa', 
    component: () => import('../layouts/SiswaLayout.vue'), 
    meta: { auth: true, role: 'Siswa' },
    children: [
      { path: 'dashboard', component: () => import('../views/siswa/Dashboard.vue') },
      { path: 'attendance', component: () => import('../views/siswa/Attendance.vue') },
      { path: 'logbook', component: () => import('../views/siswa/Logbook.vue') },
      { path: 'permission', component: () => import('../views/siswa/Permission.vue') },
      { path: 'report', component: () => import('../views/siswa/Report.vue') },
      { path: 'final-report', component: () => import('../views/siswa/FinalReport.vue') },
      { path: 'company', component: () => import('../views/siswa/CompanyInfo.vue') },
      { path: 'guide', component: () => import('../views/siswa/Guide.vue') },
      { path: 'notifications', component: () => import('../views/shared/Notifications.vue') },
      { path: 'profile', component: () => import('../views/shared/Profile.vue') },
      { path: '', redirect: '/siswa/dashboard' }
    ] 
  },

  // ================================================================
  // ===================== GURU ROUTES =============================
  // ================================================================
  { 
    path: '/guru', 
    component: () => import('../layouts/GuruLayout.vue'), 
    meta: { auth: true, role: 'Guru' },
    children: [
      { path: 'dashboard', component: () => import('../views/guru/Dashboard.vue') },
      { path: 'monitoring', component: () => import('../views/guru/Monitoring.vue') },
      { path: 'attendance-monitoring', component: () => import('../views/guru/AttendanceMonitoring.vue') },
      { path: 'logbook-review', component: () => import('../views/guru/LogbookReview.vue') },
      { path: 'report-review', component: () => import('../views/guru/ReportReview.vue') },
      { path: 'permissions', component: () => import('../views/guru/PermissionApproval.vue') },
      { path: 'assessment', component: () => import('../views/guru/Assessment.vue') },
      { path: 'reports', component: () => import('../views/guru/Reports.vue') },
      { path: 'rekap-absensi', component: () => import('../views/guru/RekapAbsensi.vue') },
      { path: 'notifications', component: () => import('../views/shared/Notifications.vue') },
      { path: 'profile', component: () => import('../views/shared/Profile.vue') },
      { path: '', redirect: '/guru/dashboard' }
    ] 
  },

  // ================================================================
  // ===================== PERUSAHAAN ROUTES ========================
  // ================================================================
  { 
    path: '/perusahaan', 
    component: () => import('../layouts/PerusahaanLayout.vue'), 
    meta: { auth: true, role: 'Perusahaan' },
    children: [
      { path: 'dashboard', component: () => import('../views/perusahaan/Dashboard.vue') },
      { path: 'assessment', component: () => import('../views/perusahaan/Assessment.vue') },
      { path: 'progress', component: () => import('../views/perusahaan/Progress.vue') },
      { path: 'report', component: () => import('../views/perusahaan/Report.vue') },
      { path: 'profile', component: () => import('../views/shared/Profile.vue') },
      { path: '', redirect: '/perusahaan/dashboard' },
      { path: 'assessment/:id', component: () => import('../views/perusahaan/AssessmentDetail.vue') },
{ path: 'progress/:id', component: () => import('../views/perusahaan/ProgressDetail.vue') },
    ] 
  },

  // ================================================================
  // ===================== FALLBACK ROUTE ==========================
  // ================================================================
  { path: '/:pathMatch(.*)*', redirect: '/' }
]

const router = createRouter({ 
  history: createWebHistory(), 
  routes 
})

// ================================================================
// ===================== NAVIGATION GUARD =========================
// ================================================================
router.beforeEach(async (to, from, next) => {
  const token = localStorage.getItem('token')
  const authStore = useAuthStore()
  
  // Jika ada token tapi belum login, cek auth
  if (token && !authStore.isAuthenticated) {
    await authStore.checkAuth()
  }
  
  const isLoggedIn = !!token && authStore.isAuthenticated
  const userRole = authStore.role ? String(authStore.role).toLowerCase() : null
  
  // Guest routes (landing, login, register)
  if (to.meta.guest) {
    if (isLoggedIn && userRole && userRole !== 'null') {
      next(`/${userRole}/dashboard`)
    } else {
      next()
    }
    return
  }
  
  // Auth required but not logged in
  if (to.meta.auth && !isLoggedIn) {
    next('/login')
    return
  }
  
  // Role-based access control
  if (to.meta.role && userRole !== to.meta.role.toLowerCase()) {
    if (userRole && userRole !== 'null') {
      next(`/${userRole}/dashboard`)
    } else {
      next('/login')
    }
    return
  }
  
  next()
})

export default router