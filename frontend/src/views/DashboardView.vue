<template>
    <section class="p-6 max-w-7xl mx-auto space-y-8">
      <h1 class="text-3xl font-bold text-slate-800 dark:text-white">Dashboard</h1>
  
      <!-- Analytics -->
      <div class="grid md:grid-cols-3 gap-6">
        <DashboardCard title="Total Posts" :value="totalPosts" />
        <DashboardCard title="Published Success Rate" :value="`${successRate}%`" />
        <DashboardCard title="Scheduled vs Published" :value="`${scheduledCount} / ${publishedCount}`" />
      </div>
  
      <!-- Posts per Platform -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-4">
        <h2 class="text-xl font-semibold mb-4 text-slate-700 dark:text-white">Posts per Platform</h2>
        <ul class="space-y-1 text-slate-700 dark:text-slate-300">
          <li v-for="item in postsPerPlatform" :key="item.type">
            {{ item.type }}: <strong>{{ item.count }}</strong>
          </li>
        </ul>
      </div>
  
      <!-- Calendar View -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-4">
        <h2 class="text-xl font-semibold mb-4 text-slate-700 dark:text-white">Scheduled Posts Calendar</h2>
        <PostCalendar :events="calendarEvents" @date-selected="handleDateSelection" />
      </div>
  
      <!-- List View with Filters -->
      <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-4">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-semibold text-slate-700 dark:text-white">Recent Posts</h2>
          <RouterLink to="/posts" class="text-blue-600 hover:underline">View All Posts →</RouterLink>
        </div>
        <PostListTable :posts="filteredPosts" />
      </div>
    </section>
  </template>
  
  <script setup>
  import { ref, onMounted } from 'vue'
  import axiosInstance from '@/lib/axios'
  import DashboardCard from '@/components/dashboard/DashboardCard.vue'
  import PostCalendar from '@/components/dashboard/PostCalendar.vue'
  import PostListTable from '@/components/dashboard/PostListTable.vue'
  
  const totalPosts = ref(0)
  const successRate = ref(0)
  const scheduledCount = ref(0)
  const publishedCount = ref(0)
  const posts = ref([])
  const filteredPosts = ref([])
  const calendarEvents = ref([])
  const postsPerPlatform = ref([])
  const selectedDate = ref(null)
  
  onMounted(async () => {
    const { data } = await axiosInstance.get('/dashboard-data')
  
    posts.value = data.posts
    totalPosts.value = data.total
    successRate.value = data.success_rate
    scheduledCount.value = data.scheduled
    publishedCount.value = data.published
    postsPerPlatform.value = data.posts_per_platform
  
    filteredPosts.value = posts.value.slice(0, 5)
  
    calendarEvents.value = posts.value
      .filter(p => p.scheduled_time)
      .map(post => ({
        id: post.id,
        title: post.title,
        date: post.scheduled_time,
        status: post.status,
        platforms: post.platforms,
        scheduled_time: post.scheduled_time,
      }))
  })
  
  function handleDateSelection(date) {
    selectedDate.value = new Date(date)
    const selectedDayStr = selectedDate.value.toDateString()
  
    filteredPosts.value = posts.value.filter(post => {
      if (!post.scheduled_time) return false
      const postDate = new Date(post.scheduled_time)
      return postDate.toDateString() === selectedDayStr
    })
  }
  </script>
  