import { ref } from 'vue';
import { ElMessage } from 'element-plus';
import apiService from '../apiService';

export default function usePostForm() {
  const postContent = ref('');
  const dialogVisible = ref(false);

  const submitPost = async (isEditMode, postId) => {
    if (!postContent.value.trim()) {
      ElMessage.error('Post content cannot be empty.');
      return false;
    }
    if (postContent.value.length > 280) {
      ElMessage.error('Post cannot exceed 280 characters.');
      return false;
    }

    try {
      let response;
      if (isEditMode) {
        response = await apiService.updatePost(postId, { content: postContent.value });
      } else {
        response = await apiService.createPost({ content: postContent.value });
      }
      ElMessage.success(response.data.message);
      postContent.value = '';
      dialogVisible.value = false;
      return true;
    } catch (error) {
      ElMessage.error(error.response?.data?.message || 'Submission failed. Please try again.');
      return false;
    }
  };
  
  const openDialog = (post = null) => {
    if (post) {
      postContent.value = post.content;
    } else {
      postContent.value = '';
    }
    dialogVisible.value = true;
  };

  return { postContent, dialogVisible, submitPost, openDialog };
}
