<template>
  <div class="posts-list">
    <ul>
      <li v-for="post in posts" :key="post.id" class="post-item">
        <div class="post-header">
          <p class="author-name">{{ post.author }}</p>
          <p class="post-date" :title="formatDate(post.updated_at).absolute">
            {{ formatDate(post.updated_at).relative }}
          </p>
        </div>
        <p class="post-content">{{ truncateContent(post.content) }}</p>
        <div class="post-actions">
          <!-- Like Icon -->
          <i class="fa fa-heart action-icon" :class="{ 'liked': post.is_liked, 'not-liked': !post.is_liked }" @click="toggleLike(post)" v-if="!isAuthor(post.author_id)"></i>
          <!-- Follow Icon -->
          <i class="fa fa-user-plus action-icon" :class="{ 'followed': post.is_followed, 'not-followed': !post.is_followed }" @click="toggleFollow(post)" v-if="!isAuthor(post.author_id)"></i>
          <!-- Edit and Delete Icons for Author -->
          <i class="fa fa-edit action-icon" @click="editPost(post)" v-if="isAuthor(post.author_id)"></i>
          <i class="fa fa-trash action-icon" @click="confirmDelete(post.id)" v-if="isAuthor(post.author_id)"></i>
        </div>
      </li>
    </ul>
    <el-pagination
      @current-change="handlePageChange"
      :current-page="currentPage"
      :page-size="pageSize"
      layout="prev, pager, next"
      :total="totalPosts">
    </el-pagination>
  </div>
</template>

<script setup>
import { useStore } from 'vuex';
import { computed } from 'vue';
import { formatDistanceToNow, format, differenceInDays } from 'date-fns';
import { ElMessage, ElMessageBox } from 'element-plus';
import apiService from '../apiService';
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
  posts: Array,
  currentPage: Number,
  pageSize: Number,
  totalPosts: Number
});

const store = useStore();
const userData = computed(() => store.state.userData);
const emit = defineEmits(['page-changed', 'edit-post']);

const truncateContent = (content, maxLength = 50) => {
  if (content.length <= maxLength) {
    return content;
  }
  let truncated = content.slice(0, maxLength);

  const lastNewline = truncated.lastIndexOf('\n');
  if (lastNewline > -1) {
    truncated = truncated.slice(0, lastNewline);
  }

  return truncated + '...';
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();

  return {
    relative: differenceInDays(now, date) > 7
      ? format(date, 'yyyy-MM-dd HH:mm:ss')
      : formatDistanceToNow(date, { addSuffix: true }),
    absolute: format(date, 'yyyy-MM-dd HH:mm:ss')
  };
};

const handlePageChange = (newPage) => {
  emit('page-changed', newPage);
};

const isAuthor = (authorId) => {
  return userData.value && userData.value.id === authorId;
};

const confirmDelete = async (postId) => {
  ElMessageBox.confirm('Are you sure you want to delete this post?', 'Warning', {
    confirmButtonText: 'Yes',
    cancelButtonText: 'No',
    type: 'warning'
  }).then(() => {
    deletePost(postId);
  }).catch(() => {
  });
};

const deletePost = async (postId) => {
  try {
    const response = await apiService.deletePost(postId);
    ElMessage.success(response.data.message);

    setTimeout(() => {
      window.location.reload();
    }, 2000);
  } catch (error) {
  }
};

const editPost = (post) => {
  emit('edit-post', post);
};

const toggleLike = async (post) => {
  try {
    if (post.is_liked) {
      const response = await apiService.unlikePost(post.id);
      ElMessage.success(response.data.message);
      post.is_liked = false;
    } else {
      const response = await apiService.likePost(post.id);
      ElMessage.success(response.data.message);
      post.is_liked = true;
    }
  } catch (error) {
    console.error(error);
  }
};

const toggleFollow = async (post) => {
  try {
    if (post.is_followed) {
      const response = await apiService.unfollowUser(post.author_id);
      ElMessage.success(response.data.message);
      post.is_followed = false;
    } else {
      const response = await apiService.followUser(post.author_id);
      ElMessage.success(response.data.message);
      post.is_followed = true;
    }
  } catch (error) {
    console.error(error);
  }
};
</script>

<style scoped>
ul {
  list-style-type: none; /* 移除列表項目前的點 */
  padding: 0;
}

.post-item {
  border: 1px solid #e1e8ed;
  border-radius: 10px;
  padding: 10px;
  margin-bottom: 10px;
  background-color: white;
}

.post-header {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.author-name {
  font-weight: bold;
  margin-right: 10px;
}

.post-date {
  color: #657786;
  font-size: 0.9em;
}

.post-content {
  white-space: pre-wrap;
  margin-bottom: 10px;
}

.post-actions {
  display: flex;
  justify-content: flex-start;
}

.action-icon {
  cursor: pointer;
  margin-right: 10px;
  /* 可以添加更多樣式來美化圖標 */
}

.liked {
  color: red; /* 已点赞状态，红色 */
}

.not-liked {
  color: black; /* 未点赞状态，黑色 */
}

.followed {
  color: green; /* 已关注状态 */
}

.not-followed {
  color: grey; /* 未关注状态 */
}

</style>
