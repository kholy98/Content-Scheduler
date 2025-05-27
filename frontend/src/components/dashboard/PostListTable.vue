<template>
    <table class="w-full text-sm text-left">
      <thead class="text-slate-600 border-b dark:text-slate-300">
        <tr>
          <th class="py-2">Title</th>
          <th>Status</th>
          <th>Platform</th>
          <th>Scheduled</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="post in posts" :key="post.id" class="border-b">
          <td class="py-2">{{ post.title }}</td>
          <td>
            <span
              :class="{
                'text-green-600': post.status === 'published',
                'text-yellow-600': post.status === 'scheduled',
                'text-gray-600': post.status === 'draft',
              }"
            >
              {{ post.status }}
            </span>
          </td>
          <td>{{ post.platforms.map(p => p.type).join(', ') }}</td>
          <td>{{ post.scheduled_time ? new Date(post.scheduled_time).toLocaleString() : '—' }}</td>
        </tr>
      </tbody>
    </table>
  </template>
  
  <script setup>
  defineProps({ posts: Array });
  </script>
  