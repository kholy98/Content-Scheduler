<script setup>
import { ref, watch, onMounted } from 'vue';
import axiosInstance from '@/lib/axios';

const props = defineProps({
  user: Object,
});

const platforms = ref([]);
const userPlatformStates = ref({});
const loading = ref(false);
const error = ref(null);

const fetchPlatforms = async () => {
  loading.value = true;
  error.value = null;
  try {
    // Fetch all platforms and the activation status for this user
    const { data } = await axiosInstance.get(`/users/${props.user.id}/platforms`);
    platforms.value = data.platforms;  // assuming API returns all platforms + user active flag
    userPlatformStates.value = {};

    // Map active status per platform
    data.platforms.forEach(p => {
      userPlatformStates.value[p.id] = p.is_active;
    });
  } catch (err) {
    error.value = 'Failed to load platforms.';
    console.error(err);
  } finally {
    loading.value = false;
  }
};

const togglePlatform = async (platformId) => {
  try {
    const newState = !userPlatformStates.value[platformId];
    await axiosInstance.put(`/users/${props.user.id}/platforms/${platformId}`, {
      is_active: newState,
    });
    userPlatformStates.value[platformId] = newState;
  } catch (err) {
    alert('Failed to update platform status.');
    console.error(err);
  }
};

watch(() => props.user, () => {
  if (props.user?.id) fetchPlatforms();
}, { immediate: true });
</script>

<template>
  <div>
    <h2 class="text-xl font-semibold mb-4">Platforms for {{ user.name }}</h2>
    <div v-if="loading">Loading platforms...</div>
    <div v-if="error" class="text-red-600 mb-2">{{ error }}</div>

    <ul>
      <li 
        v-for="platform in platforms" 
        :key="platform.id" 
        class="flex items-center justify-between p-2 border-b last:border-b-0"
      >
        <span>{{ platform.name }}</span>
        <input 
          type="checkbox" 
          :checked="userPlatformStates[platform.id]" 
          @change="togglePlatform(platform.id)"
          :disabled="user.is_admin" 
          title="Cannot change platforms for admin users"
        />
      </li>
    </ul>
  </div>
</template>
