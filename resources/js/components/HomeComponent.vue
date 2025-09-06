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
    <post-form ref="postFormRef" :post="editingPost" :isEditMode="!!editingPost" @post-submitted="handlePostSubmitted"></post-form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import PostsList from './PostsList.vue';
import PostForm from './PostForm.vue';
import apiService from '../apiService';

const posts = ref([]);
const currentPage = ref(1);
const pageSize = 10; // 每頁顯示的文章數量
const totalPosts = ref(0); // 總文章數量
const editingPost = ref(null);
const postFormRef = ref(null);

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
  editingPost.value = post;
  if (postFormRef.value) {
    postFormRef.value.openDialog(post);
  }
};

const handlePostDeleted = (deletedPostId) => {
  posts.value = posts.value.filter(post => post.id !== deletedPostId);
  totalPosts.value--;
};

const handlePostSubmitted = (submittedPost) => {
  const index = posts.value.findIndex(p => p.id === submittedPost.id);
  if (index !== -1) {
    // Editing an existing post
    posts.value[index] = submittedPost;
  } else {
    // Adding a new post
    posts.value.unshift(submittedPost);
    totalPosts.value++;
  }
  editingPost.value = null; // Reset editing state
};

onMounted(() => {
  fetchPosts(currentPage.value);
});
</script>

<style scoped>
</style>
