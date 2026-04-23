import { defineStore } from 'pinia'
import axios from '../plugins/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token'),
    role: null,  // Pastikan ini null, bukan object
    loading: false
  }),

  getters: {
    isAuthenticated: (state) => !!state.token && !!state.role
  },

  actions: {
    async login(credentials) {
      this.loading = true
      try {
        const response = await axios.post('/login', credentials)
        console.log('Login response:', response.data)
        console.log('Full response:', response)  // ← Tambahkan ini
        console.log('Response data:', response.data)  // ← Tambahkan ini
        console.log('Token:', response.data.token)  // ← Tambahkan ini
       // Simpan token dan user
        if (response.data.token) {
            localStorage.setItem('token', response.data.token)
            localStorage.setItem('user', JSON.stringify(response.data.user))
            return response.data
        }
        this.user = response.data.user
        this.token = response.data.token
        // Ambil role sebagai string, bukan object
        this.role = response.data.role || (response.data.user?.role?.name) || null
        
        localStorage.setItem('token', this.token)
        localStorage.setItem('role', this.role || '')
        
        axios.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
        
        return response.data
      } catch (error) {
        console.error('Login error:', error.response?.data)
        throw error
      } finally {
        this.loading = false
      }
    },

    async logout() {
      this.loading = true
      try {
        if (this.token) {
          await axios.post('/logout')
        }
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.user = null
        this.token = null
        this.role = null
        localStorage.removeItem('token')
        localStorage.removeItem('role')
        delete axios.defaults.headers.common['Authorization']
        this.loading = false
        window.location.href = '/login'
      }
    },

    async checkAuth() {
      const token = localStorage.getItem('token')
      const savedRole = localStorage.getItem('role')
      
      console.log('checkAuth - Token:', token ? 'Ada' : 'Tidak ada')
      console.log('checkAuth - Saved role:', savedRole)
      
      if (!token) {
        return false
      }
      
      this.token = token
      this.role = savedRole  // Ambil dari localStorage sebagai string
      axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
      
      try {
        const response = await axios.get('/me')
        console.log('checkAuth - User:', response.data)
        
        this.user = response.data
        // Ambil role sebagai string
        this.role = response.data.role || savedRole
        
        if (this.role && typeof this.role !== 'string') {
          this.role = this.role.name || String(this.role)
        }
        
        localStorage.setItem('role', this.role || '')
        return true
      } catch (error) {
        console.error('checkAuth error:', error.response?.status)
        this.logout()
        return false
      }
    }
  }
})