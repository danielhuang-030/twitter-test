<template>
  <div class="posts-list">
    <ul v-if="posts.length > 0">
      <li v-for="post in posts" :key="post.id" class="post-item">
        <div class="post-container">
          <!-- Avatar Column -->
          <div class="avatar-column">
            <img :src="`https://i.pravatar.cc/48?u=${post.author_id}`" alt="avatar" class="avatar">
          </div>

          <!-- Content Column -->
          <div class="content-column">
            <div class="post-header">
              <div class="author-info">
                <span class="author-name">{{ post.author }}</span>
                <span class="author-handle">@{{ post.author.toLowerCase().replace(' ', '_') }}</span>
                <span class="dot">·</span>
                <span class="post-date" :title="formatDate(post.updated_at).absolute">
                  {{ formatDate(post.updated_at).relative }}
                </span>
              </div>
              <div class="more-options" v-if="isAuthor(post.author_id)">
                <div class="more-options-button" @click.stop="toggleOptionsMenu(post.id)">
                  <i class="fa fa-ellipsis-h"></i>
                </div>
                <div v-if="activeOptionsMenu === post.id" class="options-menu">
                  <div class="action-item" @click="editPost(post)">
                    <i class="fa fa-edit action-icon"></i>
                    <span>Edit</span>
                  </div>
                  <div class="action-item delete-item" @click="confirmDelete(post.id)">
                    <i class="fa fa-trash action-icon"></i>
                    <span>Delete</span>
                  </div>
                </div>
              </div>
            </div>

            <p class="post-content">{{ post.content }}</p>

            <div class="post-actions">
              <div class="action-item like-action" :class="{ 'liked': post.is_liked }" v-if="!isAuthor(post.author_id)" @click="toggleLike(post)">
                <i class="fa fa-heart action-icon"></i>
                <span>{{ post.likes_count || 0 }}</span>
              </div>
              <div class="action-item follow-action" :class="{ 'followed': post.is_followed }" v-if="!isAuthor(post.author_id)" @click="toggleFollow(post)">
                <i class="fa fa-user-plus action-icon"></i>
                <span>{{ post.is_followed ? 'Following' : 'Follow' }}</span>
              </div>
            </div>
          </div>
        </div>
      </li>
    </ul>
    <div v-else class="no-posts">
        <p>No posts to display.</p>
    </div>
    <el-pagination
      v-if="totalPosts > pageSize"
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
import { computed, ref, onMounted, onUnmounted } from 'vue';
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

const activeOptionsMenu = ref(null);

const toggleOptionsMenu = (postId) => {
  if (activeOptionsMenu.value === postId) {
    activeOptionsMenu.value = null;
  } else {
    activeOptionsMenu.value = postId;
  }
};

const closeOptionsMenu = () => {
  activeOptionsMenu.value = null;
};

onMounted(() => {
  document.addEventListener('click', closeOptionsMenu);
});

onUnmounted(() => {
  document.removeEventListener('click', closeOptionsMenu);
});

const formatDate = (dateString) => {
  const date = new Date(dateString);
  const now = new Date();

  return {
    relative: differenceInDays(now, date) > 1
      ? format(date, 'MMM d')
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
    post.likes_count = post.is_liked ? (post.likes_count || 0) + 1 : (post.likes_count || 1) - 1;
  }
};

const toggleFollow = async (clickedPost) => {
  const action = clickedPost.is_followed ? apiService.unfollowUser : apiService.followUser;
  const success = await handleApiAction(
    () => action(clickedPost.author_id),
    `Failed to ${clickedPost.is_followed ? 'unfollow' : 'follow'} user.`
  );
  if (success) {
    const newFollowState = !clickedPost.is_followed;
    // Update all posts from the same author on the page
    props.posts.forEach(p => {
      if (p.author_id === clickedPost.author_id) {
        p.is_followed = newFollowState;
      }
    });
  }
};

</script>

<style scoped>
ul {
  list-style-type: none;
  padding: 0;
}

.post-item {
  border-bottom: 1px solid #e1e8ed;
  padding: 1rem 1.5rem;
  background-color: white;
  transition: background-color 0.2s ease-in-out;
}

.post-item:hover {
  background-color: #f5f8fa;
}

.post-container {
  display: flex;
  gap: 1rem;
}

.avatar-column {
  flex-shrink: 0;
}

.content-column {
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

.avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
}

.post-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 0.25rem;
}

.author-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.author-name {
  font-weight: bold;
  color: #14171a;
}

.author-handle, .post-date, .dot {
  color: #657786;
  font-size: 0.95em;
}

.post-content {
  white-space: pre-wrap;
  margin-bottom: 1rem;
  line-height: 1.5;
  color: #14171a;
  font-size: 1.05em;
}

.post-actions {
  display: flex;
  gap: 3rem; /* Add gap between actions */
  justify-content: flex-start;
  color: #657786;
  max-width: 425px;
}

.action-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
  font-size: 0.9em;
}

.action-item .action-icon {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  transition: background-color 0.2s, color 0.2s;
}

.action-item:hover .action-icon {
  background-color: rgba(29, 161, 242, 0.1);
  color: #1DA1F2;
}

.like-action .action-icon {
  color: inherit; /* Default color is grey */
}

.like-action.liked, .like-action.liked .action-icon {
  color: #E0245E; /* Red when liked */
}

.like-action:hover .action-icon {
  background-color: rgba(224, 36, 94, 0.1);
  color: #E0245E;
}

.follow-action.followed, .follow-action.followed .action-icon {
  color: #17BF63; /* Green when followed */
}

.more-options {
  position: relative;
}

.more-options-button {
  cursor: pointer;
  color: #657786;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

.more-options-button:hover {
  background-color: rgba(29, 161, 242, 0.1);
  color: #1DA1F2;
}

.options-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  padding: 0.5rem;
  min-width: 180px;
  z-index: 100;
  border: 1px solid #e1e8ed;
}

.options-menu .action-item {
  width: 100%;
  padding: 0.75rem;
  color: #14171a;
}

.options-menu .action-item:hover {
  background-color: #f5f8fa;
}

.options-menu .delete-item:hover {
  background-color: rgba(224, 36, 94, 0.1);
  color: #E0245E;
}

.no-posts {
    text-align: center;
    padding: 3rem;
    color: #657786;
}
</style>

