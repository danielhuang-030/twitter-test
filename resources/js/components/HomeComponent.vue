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
import { ref, onMounted, onUnmounted } from 'vue';
import { useStore } from 'vuex';
import PostsList from './PostsList.vue';
import apiService from '../apiService';

const store = useStore();
const posts = ref([]);
const currentPage = ref(1);
const pageSize = 10;
const totalPosts = ref(0);

const fetchPosts = async (page) => {
  try {
    const response = await apiService.getPosts({
      page: page,
      perPage: pageSize
    });
    posts.value = response.data.data.data;
    totalPosts.value = response.data.data.pagination.total;
    currentPage.value = page;
  } catch (error) {
    // console.error('Error fetching posts:', error);
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
  const index = posts.value.findIndex(p => p.id === submittedPost.id);
  if (index !== -1) {
    posts.value[index] = submittedPost;
  } else {
    posts.value.unshift(submittedPost);
    totalPosts.value++;
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
