<script setup>
import { ref, onMounted } from 'vue';
import axiosInstance from '@/lib/axios';
import { useAuthStore } from '@/stores/useAuthStore';
import { Pencil } from 'lucide-vue-next';

const platforms = ref([]);
const loading = ref(false);
const editedSettings = ref({});
const editingPlatform = ref(null);
const authStore = useAuthStore();
const isAdmin = ref(false);

const fetchPlatforms = async () => {
  loading.value = true;
  try {
    const { data } = await axiosInstance.get('/platforms');
    platforms.value = data;

    data.forEach(p => {
      editedSettings.value[p.id] = {
        characters_limit: p.setting?.characters_limit || ''
      };
    });
  } catch (err) {
    console.error('Error fetching platforms', err);
  } finally {
    loading.value = false;
  }
};

const editPlatform = (platformId) => {
  if (!isAdmin.value) {
    alert('You do not have permission to edit this platform.');
    return;
  }
  editingPlatform.value = platformId;
};

const saveSetting = async (platformId) => {
  try {
    await axiosInstance.put(`/platforms/${platformId}`, {
      characters_limit: editedSettings.value[platformId].characters_limit,
    });
    alert('Settings saved.');
    editingPlatform.value = null;
    fetchPlatforms();
  } catch (err) {
    console.error('Save failed:', err);
    alert('Failed to save settings.');
  }
};

onMounted(async () => {
  await authStore.fetchUser();
  isAdmin.value = authStore.user?.is_admin;
  await fetchPlatforms();
});
</script>

<template>
  <section class="p-6 space-y-6">
    <div class="bg-white dark:bg-slate-800 shadow-md rounded-xl p-4">
      <h1 class="text-xl font-semibold mb-4 text-slate-800 dark:text-white">Platforms</h1>

      <div v-if="loading" class="text-center py-4 text-slate-500">Loading...</div>

      <div v-else class="grid gap-4">
        <div
          v-for="platform in platforms"
          :key="platform.id"
          class="border p-4 rounded-lg dark:border-slate-700 bg-slate-50 dark:bg-slate-900"
        >
          <div class="flex justify-between items-center mb-1">
            <h2 class="text-lg font-semibold text-slate-700 dark:text-white">
              {{ platform.name }}
            </h2>
            <button
              @click="editPlatform(platform.id)"
              class="flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800"
            >
              <Pencil class="w-4 h-4" />
              Edit
            </button>
          </div>

          <p class="text-sm text-slate-500 mb-2">{{ platform.type }}</p>

          <div v-if="editingPlatform === platform.id" class="space-y-2">
            <label class="block text-sm text-slate-600 dark:text-slate-300">
              Character Limit:
              <input
                type="number"
                class="input mt-1"
                v-model="editedSettings[platform.id].characters_limit"
              />
            </label>

            <button
              class="bg-blue-600 text-white px-4 py-2 text-sm rounded hover:bg-blue-700"
              @click="saveSetting(platform.id)"
            >
              Save
            </button>
          </div>

          <div v-else class="text-sm text-slate-500">
            Character Limit: {{ platform.setting?.characters_limit ?? 'Not Set' }}
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped>
.input {
  @apply w-full px-3 py-2 rounded-md border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-700 dark:text-white;
}
</style>
