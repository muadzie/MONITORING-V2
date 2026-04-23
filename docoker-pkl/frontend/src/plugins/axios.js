import axios from 'axios'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

const axiosInstance = axios.create({
  baseURL: API_URL,
  timeout: 30000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// Request interceptor - PASTIKAN INI BERJALAN
axiosInstance.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('token')
    console.log('Interceptor - Token:', token ? 'Ada' : 'Tidak ada')
    console.log('Interceptor - URL:', config.url)
    
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
      console.log('Interceptor - Authorization header ditambahkan')
    }
    return config
  },
  (error) => {
    console.error('Interceptor error:', error)
    return Promise.reject(error)
  }
)

// Response interceptor
axiosInstance.interceptors.response.use(
  (response) => {
    console.log('Response success:', response.config.url, response.status)
    return response
  },
  (error) => {
    console.error('Response error:', error.response?.status, error.response?.data)
    
    if (error.response?.status === 401) {
      console.log('Unauthorized, redirecting to login')
      localStorage.removeItem('token')
      localStorage.removeItem('role')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export default axiosInstance