<template>
  <div>
    <posts-list
      :posts="posts"
      :current-page="currentPage"
      :page-size="pageSize"
      :total-posts="totalPosts"
      @page-changed="fetchPosts"
      @edit-post="handleEditPost">
    </posts-list>
    <post-form :post="editingPost" :isEditMode="true"></post-form>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import PostsList from './PostsList.vue';
import PostForm from './PostForm.vue';
import apiService from '../apiService.js';

const posts = ref([]);
const currentPage = ref(1);
const pageSize = 10; // 每頁顯示的文章數量
const totalPosts = ref(0); // 總文章數量
const editingPost = ref(null);

const route = useRoute();
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
  editingPost.value = post;
};

onMounted(() => {
  fetchPosts(currentPage.value);
});
</script>

<style scoped>
</style>