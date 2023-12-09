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

<script>
import PostsList from './PostsList.vue';
import PostForm from './PostForm.vue';
import apiService from '../apiService.js';

export default {
  components: {
    PostsList,
    PostForm
  },
  data() {
    return {
      posts: [],
      currentPage: 1,
      pageSize: 10, // 每頁顯示的文章數量
      totalPosts: 0, // 總文章數量
      editingPost: null
    };
  },
  computed: {
    userId() {
      return this.$route.params.userId;
    }
  },
  created() {
    this.fetchPosts(this.currentPage);
  },
  methods: {
    async fetchPosts(page) {
      try {
        const response = await apiService.getUserPosts({
          userId: this.userId,
          page: page,
          perPage: this.pageSize
        });
        this.posts = response.data.data.data;
        this.totalPosts = response.data.data.pagination.total;
        this.currentPage = page;
      } catch (error) {
        console.error('Error fetching user posts:', error);
      }
    },
    handleEditPost(post) {
      this.editingPost = post;
    }
  }
};
</script>
