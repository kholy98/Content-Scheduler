<template>
  <div class="min-h-screen bg-gray-100">
    <nav class="bg-gray-800 text-white p-4">
      <RouterLink to="/login" class="mr-4">Login</RouterLink>
      <RouterLink to="/register">Register</RouterLink>
    </nav>
    <main class="p-4">
      <router-view />
    </main>
  </div>
</template>

<script setup>
import { RouterLink } from 'vue-router';
</script>
