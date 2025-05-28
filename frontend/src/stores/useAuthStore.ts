import { defineStore } from 'pinia'
import axiosInstance from '@/lib/axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    loading: false,
  }),

  actions: {
    async fetchUser() {
      this.loading = true
      try {
        const { data } = await axiosInstance.get('/user')
        this.user = data
      } catch (error) {
        console.error('Failed to fetch user:', error)
        this.user = null
      } finally {
        this.loading = false
      }
    },

    logout() {
      this.user = null
      localStorage.removeItem('access_token') 
    },
  },

  getters: {
    isAdmin: (state) => state.user?.is_admin === 1,
    isAuthenticated: (state) => !!state.user,
  },
})
