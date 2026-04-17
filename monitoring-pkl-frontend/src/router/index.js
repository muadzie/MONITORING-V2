import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const routes = [

  {
    path: '/',
    name: 'Landing',
    component: () => import('../views/LandingPage.vue'),
    meta: { guest: true }
  },
  { path: '/login', component: () => import('../views/auth/Login.vue'), meta: { guest: true } },
  
  // Admin
  { path: '/admin', component: () => import('../layouts/AdminLayout.vue'), meta: { auth: true, role: 'Admin' },
    children: [
      { path: 'dashboard', component: () => import('../views/admin/Dashboard.vue') },
      { path: 'users', component: () => import('../views/admin/Users.vue') },
      { path: 'students', component: () => import('../views/admin/Students.vue') },
      { path: 'teachers', component: () => import('../views/admin/Teachers.vue') },
      { path: 'companies', component: () => import('../views/admin/Companies.vue') },
      { path: 'monitoring', component: () => import('../views/admin/Monitoring.vue') },
      { path: '', redirect: '/admin/dashboard' },
{ path: 'placements', component: () => import('../views/admin/Placements.vue') },
{ path: 'placements/create', component: () => import('../views/admin/Placements.vue') },
{ path: 'roles', component: () => import('../views/admin/Roles.vue') },
{ path: 'classes', component: () => import('../views/admin/Classes.vue') },
{ path: 'settings/general', component: () => import('../views/admin/Settings.vue') },
{ path: 'monitoring/attendance', component: () => import('../views/admin/Monitoring.vue') },
{ path: 'reports/attendance', component: () => import('../views/admin/Reports.vue') },
    ] },
  
// Siswa Routes
{ path: '/siswa', component: () => import('../layouts/SiswaLayout.vue'), meta: { auth: true, role: 'Siswa' },
  children: [
    { path: 'dashboard', component: () => import('../views/siswa/Dashboard.vue') },
    { path: 'attendance', component: () => import('../views/siswa/Attendance.vue') },
    { path: 'logbook', component: () => import('../views/siswa/Logbook.vue') },
    { path: 'permission', component: () => import('../views/siswa/Permission.vue') },
    { path: 'report', component: () => import('../views/siswa/Report.vue') },  // Tambahkan ini
    { path: 'company', component: () => import('../views/siswa/CompanyInfo.vue') },
    { path: 'guide', component: () => import('../views/siswa/Guide.vue') },
    { path: 'profile', component: () => import('../views/shared/Profile.vue') },
    { path: '', redirect: '/siswa/dashboard' }
  ] },

// Guru Routes
{ path: '/guru', component: () => import('../layouts/GuruLayout.vue'), meta: { auth: true, role: 'Guru' },
  children: [
    { path: 'dashboard', component: () => import('../views/guru/Dashboard.vue') },
    { path: 'monitoring', component: () => import('../views/guru/Monitoring.vue') },
    { path: 'logbook-review', component: () => import('../views/guru/LogbookReview.vue') },
    { path: 'permissions', component: () => import('../views/guru/PermissionApproval.vue') },
    { path: 'reports', component: () => import('../views/guru/Reports.vue') },
    { path: '', redirect: '/guru/dashboard' }
  ] },

// Perusahaan Routes
{ path: '/perusahaan', component: () => import('../layouts/PerusahaanLayout.vue'), meta: { auth: true, role: 'Perusahaan' },
  children: [
    { path: 'dashboard', component: () => import('../views/perusahaan/Dashboard.vue') },
    { path: 'assessment', component: () => import('../views/perusahaan/Assessment.vue') },
    { path: 'progress', component: () => import('../views/perusahaan/Progress.vue') },
    { path: '', redirect: '/perusahaan/dashboard' }
  ] },
  
  { path: '/', redirect: '/login' }
]

const router = createRouter({ history: createWebHistory(), routes })

router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore()
  const token = localStorage.getItem('token')
  
  if (token && !authStore.isAuthenticated) {
    await authStore.checkAuth()
  }
  
  // Jika user sudah login dan mencoba akses landing page atau login
  if (authStore.isAuthenticated && (to.path === '/' || to.path === '/login')) {
    const role = authStore.role?.toLowerCase()
    next(`/${role}/dashboard`)
  } 
  else if (to.meta.auth && !authStore.isAuthenticated) {
    next('/login')
  } 
  else if (to.meta.role && authStore.role !== to.meta.role) {
    const role = authStore.role?.toLowerCase()
    next(`/${role}/dashboard`)
  } 
  else {
    next()
  }
})

export default router