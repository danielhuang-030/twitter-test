<template>
  <div>
    <posts-list
      :posts="posts"
      :current-page="currentPage"
      :page-size="pageSize"
      :total-posts="totalPosts"
      @page-changed="fetchPosts"
      @edit-post="handleEditPost"
      @post-deleted="handlePostDeleted">
    </posts-list>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import { useStore } from 'vuex';
import PostsList from './PostsList.vue';
import apiService from '../apiService.js';

const posts = ref([]);
const currentPage = ref(1);
const pageSize = 10;
const totalPosts = ref(0);

const route = useRoute();
const store = useStore();
const userId = computed(() => route.params.userId);

const fetchPosts = async (page) => {
  try {
    const response = await apiService.getUserPosts({
      userId: userId.value,
      page: page,
      perPage: pageSize
    });
    posts.value = response.data.data.data;
    totalPosts.value = response.data.data.pagination.total;
    currentPage.value = page;
  } catch (error) {
    console.error('Error fetching user posts:', error);
  }
};

const handleEditPost = (post) => {
  store.dispatch('openPostDialog', post);
};

const handlePostDeleted = (deletedPostId) => {
  posts.value = posts.value.filter(post => post.id !== deletedPostId);
  totalPosts.value--;
};

const handlePostSubmitted = (event) => {
  const submittedPost = event.detail;
  // Only update if the post belongs to the user whose page we are on
  if (submittedPost.author_id.toString() === userId.value) {
    const index = posts.value.findIndex(p => p.id === submittedPost.id);
    if (index !== -1) {
      posts.value[index] = submittedPost;
    } else {
      // If it's a new post, it should appear at the top.
      // We can either prepend it or just refetch the first page.
      // Refetching is simpler and handles cases where other new posts arrived.
      if (currentPage.value === 1) {
          posts.value.unshift(submittedPost);
          totalPosts.value++;
      } else {
          fetchPosts(1); // Or just notify the user that there are new posts.
      }
    }
  }
};

onMounted(() => {
  fetchPosts(currentPage.value);
  window.addEventListener('post-submitted', handlePostSubmitted);
});

onUnmounted(() => {
  window.removeEventListener('post-submitted', handlePostSubmitted);
});

</script>

<style scoped>
</style>
