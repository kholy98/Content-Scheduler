// stores/usePostStore.js
import { defineStore } from 'pinia';

export const usePostStore = defineStore('post', {
  state: () => ({
    currentPost: null,
  }),
  actions: {
    setPost(post) {
      this.currentPost = post;
    },
    clearPost() {
      this.currentPost = null;
    },
  },
});
