<script setup>
    import { ref, onMounted, onUnmounted } from 'vue';
    import { useRouter } from 'vue-router'; // ⬅️ Import this
    import { usePostStore } from '@/stores/usePostStore';
    import axiosInstance from '@/lib/axios';
    import { Upload, CalendarDays } from 'lucide-vue-next';

    const router = useRouter(); // ⬅️ Init router

    const postStore = usePostStore();
    const post = ref({
        id: null,
        title: '',
        content: '',
        status: 'draft',
        scheduled_time: '',
        platforms: [],
        image_url: '',
    });

    const availablePlatforms = ref([]);
    const contentLength = ref(0);
    const selectedImage = ref(null);
    const loading = ref(false);

    const characterLimit = 280;

    function getImageUrl(path) {
        if (path.startsWith('http') || path.startsWith('data:')) return path;
        return `http://localhost:8000${path.startsWith('/') ? path : '/' + path}`;
    }

    onMounted(async () => {
        if (postStore.currentPost) {
            post.value = {
            ...postStore.currentPost,
            platforms: postStore.currentPost.platforms.map(p => p.type),
            };
            contentLength.value = post.value.content?.length || 0;
        }

        try {
            const { data } = await axiosInstance.get('/platforms');
            availablePlatforms.value = data;
        } catch (e) {
            console.error('Failed to load platforms:', e);
        }
    });

    onUnmounted(() => {
        postStore.clearPost();
    });

    async function handleImageUpload(e) {
        const file = e.target.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('image', file);

        try {
            const { data } = await axiosInstance.post('/upload', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
            });
            post.value.image_url = data.url;
            selectedImage.value = file;
        } catch (err) {
            console.error('Image upload failed', err);
            alert('Image upload failed');
        }
    }

    async function handleSubmit() {
        loading.value = true;
        try {
            const payload = {
            title: post.value.title,
            content: post.value.content,
            scheduled_time: post.value.scheduled_time,
            status: post.value.status,
            image_url: post.value.image_url,
            platforms: post.value.platforms,
            };

            const { data } = await axiosInstance.put(`/posts/${post.value.id}`, payload);

            alert('Post updated successfully!');
            router.push('/posts'); // Redirect to posts view
        } catch (err) {
            console.error('Failed to update post', err);
            alert(err.response?.data?.error || 'Failed to update post');
        } finally {
            loading.value = false;
        }
    }
</script>


<template>
  <section class="p-6 max-w-3xl mx-auto">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow p-6 space-y-6">
      <h1 class="text-2xl font-semibold text-slate-800 dark:text-white">Edit Post</h1>

      <form @submit.prevent="handleSubmit" class="space-y-4">
        <input v-model="post.title" type="text" placeholder="Title"
          class="w-full px-4 py-2 border rounded-md bg-white dark:bg-slate-900 dark:text-white" />

        <div>
          <textarea
            v-model="post.content"
            @input="contentLength = post.content.length"
            rows="6"
            placeholder="Write your post..."
            class="w-full px-4 py-2 border rounded-md bg-white dark:bg-slate-900 dark:text-white resize-none"
          ></textarea>
          <div class="text-sm text-right text-slate-500 dark:text-slate-400">
            {{ contentLength }} / {{ characterLimit }} characters
          </div>
        </div>

        <!-- Image Upload -->
        <label class="flex items-center gap-2 text-slate-600 dark:text-white cursor-pointer">
          <Upload class="w-4 h-4" />
          <span>Select Image</span>
          <input type="file" class="hidden" @change="handleImageUpload" />
        </label>

        <!-- Image Preview -->
        <img
            v-if="post.image_url"
            :src="getImageUrl(post.image_url)"
            alt="Image Preview"
            class="h-32 mt-2 rounded shadow"
        />

        <!-- Platform Multi Select -->
        <div class="flex flex-wrap gap-2">
          <label
            v-for="platform in availablePlatforms"
            :key="platform.id"
            class="flex items-center gap-2 text-sm cursor-pointer"
          >
            <input
              type="checkbox"
              :value="platform.type"
              v-model="post.platforms"
            />
            {{ platform.name }}
          </label>
        </div>

        <div class="flex items-center gap-2">
          <CalendarDays class="w-5 h-5 text-slate-500 dark:text-slate-300" />
          <input
            type="datetime-local"
            v-model="post.scheduled_time"
            class="border rounded px-3 py-2 bg-white dark:bg-slate-900 dark:text-white"
          />
        </div>

        <select v-model="post.status"
          class="px-4 py-2 border rounded-md bg-white dark:bg-slate-900 dark:text-white w-full">
          <option value="draft">Draft</option>
          <option value="scheduled">Scheduled</option>
          <option value="published">Published</option>
        </select>

        <button
          type="submit"
          :disabled="loading"
          class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium"
        >
          Save Post
        </button>
      </form>
    </div>
  </section>
</template>
