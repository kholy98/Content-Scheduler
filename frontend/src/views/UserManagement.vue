<script setup>
import { ref, onMounted } from 'vue';
import axiosInstance from '@/lib/axios';

const users = ref([]);
const platforms = ref([]);
const loading = ref(true);
const error = ref(null);

const fetchUsersAndPlatforms = async () => {
  loading.value = true;
  error.value = null;
  try {
    const { data: usersData } = await axiosInstance.get('/users'); // API to get all users
    const { data: platformsData } = await axiosInstance.get('/platforms'); // API to get all platforms

    users.value = usersData.map(user => ({
      ...user,
      platforms: [] // will be filled below
    }));

    platforms.value = platformsData;

    // Fetch platform access for each user
    await Promise.all(
      users.value.map(async user => {
        const { data } = await axiosInstance.get(`/users/${user.id}/platforms`);
        user.platforms = data.platforms;
      })
    );

  } catch (err) {
    console.error(err);
    error.value = 'Failed to load data';
  } finally {
    loading.value = false;
  }
};

const togglePlatform = async (userId, platformId, currentStatus) => {
  try {
    await axiosInstance.post(`/users/${userId}/platforms/${platformId}/set-status`, {
      is_active: !currentStatus,
    });

    const user = users.value.find(u => u.id === userId);
    const platform = user.platforms.find(p => p.id === platformId);
    platform.is_active = !currentStatus;
  } catch (err) {
    alert(err.response.data.message);
  }
};

onMounted(fetchUsersAndPlatforms);
</script>

<template>
  <section class="p-6 space-y-4">
    <h1 class="text-2xl font-bold">User Platform Access Management</h1>

    <div v-if="loading">Loading...</div>
    <div v-if="error" class="text-red-600">{{ error }}</div>

    <div v-else class="space-y-6">
      <div 
        v-for="user in users" 
        :key="user.id" 
        class="border p-4 rounded shadow bg-white dark:bg-slate-800"
      >
        <h2 class="text-lg font-semibold mb-2 text-slate-800 dark:text-white">
          {{ user.name }} 
          <span v-if="user.is_admin" class="text-sm text-red-500">(Admin)</span>
        </h2>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <div 
            v-for="platform in user.platforms" 
            :key="platform.id" 
            class="flex items-center justify-between bg-slate-100 dark:bg-slate-900 p-2 rounded"
          >
            <span class="text-sm text-slate-700 dark:text-white">{{ platform.name }}</span>
            <input 
              type="checkbox"
              :checked="platform.is_active"
              :disabled="user.is_admin"
              @change="togglePlatform(user.id, platform.id, platform.is_active)"
            />
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
