<script setup>
    import { ref, onMounted, watch } from 'vue';
    import axiosInstance from "@/lib/axios";
    import { Pencil } from 'lucide-vue-next';
    import { useRouter } from 'vue-router';
    import { usePostStore } from '@/stores/usePostStore';

    const posts = ref([]);
    const currentPage = ref(1);
    const totalPages = ref(1);
    const filters = ref({
    status: '',
    search: '',
    from: '',
    to: '',
    });
    const loading = ref(false);

    const router = useRouter();
    const postStore = usePostStore();

    const fetchPosts = async () => {
    loading.value = true;
    try {
        const { data } = await axiosInstance.get('/posts', {
        params: {
            ...filters.value,
            page: currentPage.value,
        },
        });
        posts.value = data.data;
        currentPage.value = data.current_page;
        totalPages.value = data.last_page;
    } catch (err) {
        console.error('Error fetching posts', err);
    } finally {
        loading.value = false;
    }
    };

    const handleEdit = (post) => {
    postStore.setPost(post);
    router.push({ name: 'editor' });
    };

    onMounted(fetchPosts);
    watch([filters, currentPage], fetchPosts, { deep: true });
</script>

<template>
  <section class="p-6 space-y-6">
    <div class="bg-white dark:bg-slate-800 shadow-md rounded-xl p-4">
      <h1 class="text-xl font-semibold mb-4 text-slate-800 dark:text-white">Posts</h1>

      <!-- Filters -->
      <div class="grid md:grid-cols-4 sm:grid-cols-2 gap-4 mb-6">
        <input v-model="filters.search" type="text" placeholder="Search..." class="input" />
        <select v-model="filters.status" class="input">
          <option value="">All Statuses</option>
          <option value="draft">Draft</option>
          <option value="published">Published</option>
          <option value="scheduled">Scheduled</option>
        </select>
        <input v-model="filters.from" type="date" class="input" />
        <input v-model="filters.to" type="date" class="input" />
      </div>

      <!-- Posts Table -->
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto text-left">
          <thead class="bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-white">
            <tr>
              <th class="p-3">Title</th>
              <th class="p-3">Status</th>
              <th class="p-3">Created</th>
              <th class="p-3">Platforms</th>
            </tr>
          </thead>
          <tbody class="text-slate-700 dark:text-slate-300">
            <tr v-if="loading">
              <td colspan="4" class="p-4 text-center">Loading...</td>
            </tr>
            <tr
              v-for="post in posts"
              :key="post.id"
              class="border-t border-slate-200 dark:border-slate-700"
            >
              <td class="p-3 font-medium">{{ post.title }}</td>
              <td class="p-3 capitalize">{{ post.status }}</td>
              <td class="p-3">{{ new Date(post.created_at).toLocaleDateString() }}</td>
              <td class="p-3">
                <div class="flex items-center gap-2 flex-wrap">
                  <div class="flex flex-wrap gap-2">
                    <span
                      v-for="platform in post.platforms"
                      :key="platform.id"
                      class="bg-blue-100 dark:bg-blue-800 text-blue-700 dark:text-white text-xs px-2 py-1 rounded"
                    >
                      {{ platform.name }}
                    </span>
                  </div>
                  <button
                    v-if="post.status === 'scheduled'"
                    @click="handleEdit(post)"
                    class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800"
                    title="Edit Post"
                  >
                    <Pencil class="w-4 h-4" />
                    <span>Edit</span>
                  </button>
                </div>
              </td>
            </tr>
            <tr v-if="!loading && posts.length === 0">
              <td colspan="4" class="p-4 text-center text-slate-400">No posts found.</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="flex justify-center items-center mt-6 gap-2">
        <button
          class="px-4 py-2 rounded bg-slate-200 dark:bg-slate-700 text-sm disabled:opacity-50"
          :disabled="currentPage === 1"
          @click="currentPage--"
        >
          Prev
        </button>
        <span class="text-sm text-slate-600 dark:text-slate-300">Page {{ currentPage }} of {{ totalPages }}</span>
        <button
          class="px-4 py-2 rounded bg-slate-200 dark:bg-slate-700 text-sm disabled:opacity-50"
          :disabled="currentPage === totalPages"
          @click="currentPage++"
        >
          Next
        </button>
      </div>
    </div>
  </section>
</template>

<style scoped>
.input {
  @apply w-full px-3 py-2 rounded-md border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-slate-700 dark:text-white;
}
</style>
