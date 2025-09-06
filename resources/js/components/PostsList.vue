<template>
  <div class="posts-list">
    <ul>
      <li v-for="post in posts" :key="post.id" class="post-item">
        <div class="post-header">
          <img src="https://i.pravatar.cc/40" alt="avatar" class="avatar">
          <div>
            <p class="author-name">{{ post.author }}</p>
            <p class="post-date" :title="formatDate(post.updated_at).absolute">
              {{ formatDate(post.updated_at).relative }}
            </p>
          </div>
        </div>
        <p class="post-content">{{ truncateContent(post.content) }}</p>
        <div class="post-actions">
          <template v-if="isAuthor(post.author_id)">
            <div class="action-item" @click="editPost(post)">
              <i class="fa fa-edit action-icon"></i>
              <span>Edit</span>
            </div>
            <div class="action-item" @click="confirmDelete(post.id)">
              <i class="fa fa-trash action-icon"></i>
              <span>Delete</span>
            </div>
          </template>
          <template v-else>
            <div class="action-item" @click="toggleLike(post)">
              <i class="fa fa-heart action-icon" :class="{ 'liked': post.is_liked, 'not-liked': !post.is_liked }"></i>
              <span>Like</span>
            </div>
            <div class="action-item" @click="toggleFollow(post)">
              <i class="fa fa-user-plus action-icon" :class="{ 'followed': post.is_followed, 'not-followed': !post.is_followed }"></i>
              <span>Follow</span>
            </div>
          </template>
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
const emit = defineEmits(['page-changed', 'edit-post', 'post-deleted']);

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
  try {
    await ElMessageBox.confirm('Are you sure you want to delete this post?', 'Warning', {
      confirmButtonText: 'Yes',
      cancelButtonText: 'No',
      type: 'warning'
    });
    deletePost(postId);
  } catch (error) {
    // User clicked 'No' or closed the dialog
  }
};

const handleApiAction = async (action, errorMessage) => {
  try {
    const response = await action();
    ElMessage.success(response.data.message);
    return true;
  } catch (error) {
    ElMessage.error(error.response?.data?.message || errorMessage);
    return false;
  }
};

const deletePost = async (postId) => {
  const success = await handleApiAction(
    () => apiService.deletePost(postId),
    'Failed to delete post.'
  );
  if (success) {
    emit('post-deleted', postId);
  }
};

const editPost = (post) => {
  emit('edit-post', post);
};

const toggleLike = async (post) => {
  const action = post.is_liked ? apiService.unlikePost : apiService.likePost;
  const success = await handleApiAction(
    () => action(post.id),
    `Failed to ${post.is_liked ? 'unlike' : 'like'} post.`
  );
  if (success) {
    post.is_liked = !post.is_liked;
  }
};

const toggleFollow = async (post) => {
  const action = post.is_followed ? apiService.unfollowUser : apiService.followUser;
  const success = await handleApiAction(
    () => action(post.author_id),
    `Failed to ${post.is_followed ? 'unfollow' : 'follow'} user.`
  );
  if (success) {
    post.is_followed = !post.is_followed;
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
  border-radius: 12px;
  padding: 1rem;
  margin-bottom: 1rem;
  background-color: white;
  transition: box-shadow 0.2s ease-in-out;
}

.post-item:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.post-header {
  display: flex;
  align-items: center;
  margin-bottom: 0.75rem;
}

.avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  margin-right: 10px;
}

.author-name {
  font-weight: 600;
  margin-right: 10px;
}

.post-date {
  color: #657786;
  font-size: 0.85em;
}

.post-content {
  white-space: pre-wrap;
  margin-bottom: 1rem;
  line-height: 1.5;
}

.post-actions {
  display: flex;
  justify-content: space-around;
  color: #657786;
}

.action-item {
  display: flex;
  align-items: center;
  cursor: pointer;
  padding: 0.5rem;
  border-radius: 9999px;
  transition: background-color 0.2s, color 0.2s;
}

.action-item:hover {
  background-color: rgba(29, 161, 242, 0.1);
  color: #1DA1F2;
}

.action-icon {
  margin-right: 0.5rem;
  font-size: 1.25em;
}

.liked {
  color: #E0245E; /* Twitter's like red */
}

.not-liked {
  color: inherit;
}

.followed {
  color: #17BF63; /* A vibrant green */
}

.not-followed {
  color: inherit;
}

@media (max-width: 768px) {
  .post-item {
    padding: 0.75rem;
    border-radius: 8px;
  }

  .action-item span {
    display: none;
  }

  .action-icon {
    margin-right: 0;
    font-size: 1.2rem;
  }

  .post-actions {
    justify-content: space-around;
  }
}
</style>
