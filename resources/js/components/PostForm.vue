<template>
  <el-dialog
    :model-value="dialogVisible"
    :title="isEditMode ? 'Edit Post' : 'Create Post'"
    @update:model-value="$emit('update:dialogVisible', $event)"
    @opened="onDialogOpened"
  >
    <form @submit.prevent="handleSubmit">
      <el-input type="textarea" v-model="postContent" placeholder="Share something new..." :autosize="{ minRows: 4, maxRows: 8 }" ref="postInputRef" />
      <span class="dialog-footer">
        <el-button @click="handleCancel">Cancel</el-button>
        <el-button type="primary" @click="handleSubmit">Post</el-button>
      </span>
    </form>
  </el-dialog>
</template>

<script setup>
import { ref, watch, nextTick } from 'vue';
import { ElMessage } from 'element-plus';
import apiService from '../apiService';

const props = defineProps({
  dialogVisible: Boolean,
  post: Object,
  isEditMode: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['update:dialogVisible', 'post-submitted']);

const postContent = ref('');
const postInputRef = ref(null);

watch(() => props.post, (newPost) => {
  postContent.value = newPost ? newPost.content : '';
}, { immediate: true });

const onDialogOpened = () => {
  nextTick(() => {
    postInputRef.value?.focus();
  });
};

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
    if (props.isEditMode && props.post) {
      response = await apiService.updatePost(props.post.id, { content: postContent.value });
    } else {
      response = await apiService.createPost({ content: postContent.value });
    }
    ElMessage.success(response.data.message);
    emit('post-submitted', response.data.data.post);
    emit('update:dialogVisible', false);
  } catch (error) {
    console.log(error);
    ElMessage.error(error.response?.data?.message || 'Submission failed. Please try again.');
  }
};

const handleCancel = () => {
  emit('update:dialogVisible', false);
};
</script>

<style scoped>
.dialog-footer {
  display: flex;
  justify-content: flex-end;
  margin-top: 20px;
}
</style>
