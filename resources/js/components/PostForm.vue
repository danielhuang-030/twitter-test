<template>
  <el-dialog v-model="dialogVisible" :title="isEditMode ? 'Edit Post' : 'Create Post'">
    <form @submit.prevent="handleSubmit">
      <el-input type="textarea" v-model="postContent" placeholder="Share something new..." :autosize="{ minRows: 4, maxRows: 8 }" />
      <span class="dialog-footer">
        <el-button @click="dialogVisible = false">Cancel</el-button>
        <el-button type="primary" @click="handleSubmit">Post</el-button>
      </span>
    </form>
  </el-dialog>
</template>

<script setup>
import { ref } from 'vue';
import { ElMessage } from 'element-plus';
import apiService from '../apiService';

const props = defineProps({
  post: Object,
  isEditMode: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['post-submitted']);

const postContent = ref('');
const dialogVisible = ref(false);
const currentPost = ref(null);

const handleSubmit = async () => {
  if (!postContent.value.trim()) {
    ElMessage.error('Post content cannot be empty.');
    return;
  }
  if (postContent.value.length > 280) {
    ElMessage.error('Post cannot exceed 280 characters.');
    return;
  }

  try {
    let response;
    if (currentPost.value) {
      response = await apiService.updatePost(currentPost.value.id, { content: postContent.value });
    } else {
      response = await apiService.createPost({ content: postContent.value });
    }
    ElMessage.success(response.data.message);
    emit('post-submitted', response.data.data.post);
    dialogVisible.value = false;
  } catch (error) {
    console.log(error);

    ElMessage.error(error.response?.data?.message || 'Submission failed. Please try again.');
  }
};

const openDialog = (post = null) => {
  currentPost.value = post;
  if (post) {
    postContent.value = post.content;
  } else {
    postContent.value = '';
  }
  dialogVisible.value = true;
};

defineExpose({
  openDialog
});
</script>

<style scoped>
.dialog-footer {
  display: flex;
  justify-content: flex-end;
  margin-top: 20px;
}
</style>
