<template>
  <div>
    <ul>
      <li v-for="post in posts" :key="post.id">
        <div class="post-card">
          <p>Content: {{ truncateContent(post.content) }}</p>
          <p>Author: {{ post.author }}</p>
          <p>Updated: {{ formatDate(post.updated_at) }}</p>
        </div>
      </li>
    </ul>
    <!-- 分頁控制 -->
    <el-pagination
      @current-change="fetchPosts"
      :current-page="currentPage"
      :page-size="pageSize"
      layout="prev, pager, next"
      :total="totalPosts">
    </el-pagination>
  </div>
</template>

<style>
/* ...其他樣式... */

.post-card {
  border: 1px solid #ddd; /* 邊框 */
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* 陰影 */
  border-radius: 10px; /* 圓角 */
  padding: 15px; /* 內邊距 */
  margin-bottom: 20px; /* 與下一個卡片的間距 */
  background-color: white; /* 背景色 */
}

.post-card p {
  margin: 5px 0; /* 段落間距 */
}

ul {
  list-style-type: none; /* 移除列表項目前的點 */
  padding: 0; /* 可選，移除默認的內邊距 */
}
</style>

<script>
import apiService from '../apiService';

export default {
  data() {
    return {
      posts: [],
      currentPage: 1,
      pageSize: 10, // 每頁顯示的文章數量
      totalPosts: 0 // 總文章數量
    };
  },
  created() {
    this.fetchPosts(this.currentPage);
  },
  methods: {
    truncateContent(content) {
      return content.length > 20 ? content.substring(0, 20) + '...' : content;
    },

    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('zh-TW', { year: 'numeric', month: 'long', day: 'numeric' });
    },
    async fetchPosts(page) {
      try {
        const response = await apiService.getPosts({ page, per_page: this.pageSize });
        this.posts = response.data.data.data;
        this.totalPosts = response.data.data.pagination.total;
        this.currentPage = page;
      } catch (error) {
        console.error('Error fetching posts:', error);
      }
    }
  }
};
</script>
