<template>
  <div class="flex min-h-screen">
    <aside class="w-64 bg-white shadow-md p-4">
      <nav class="space-y-2">
        <RouterLink to="/dashboard" class="block">Dashboard</RouterLink>
        <RouterLink to="/posts" class="block">Posts</RouterLink>
        <RouterLink to="/editor" class="block">New Post</RouterLink>
        <RouterLink to="/settings" class="block">Settings</RouterLink>
        <button @click="logout" class="text-red-600 mt-4">Logout</button>
      </nav>
    </aside>
    <main class="flex-1 p-6 bg-gray-100">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import axiosInstance from '@/lib/axios';

const router = useRouter();

const logout = async () => {
  try {
    await axiosInstance.post('/logout');
    localStorage.removeItem('access_token');
    router.push('/login');
  } catch (error) {
    console.error('Logout failed:', error);
  }
};
</script>
