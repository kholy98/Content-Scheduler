<template>
    <div class="p-4 bg-blue-50 dark:bg-blue-900 text-blue-800 dark:text-blue-100 rounded-lg shadow">
      <h3 class="text-sm font-medium mb-1">{{ title }}</h3>
      <p class="text-2xl font-bold">{{ value }}</p>
    </div>
  </template>
  
  <script setup>
  defineProps({
    title: String,
    value: [String, Number],
  });
  </script>
  