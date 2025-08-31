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
import { defineProps, defineEmits, watch } from 'vue';
import usePostForm from '../composables/usePostForm';

const props = defineProps({
  post: Object,
  isEditMode: {
    type: Boolean,
    default: false
  }
});

const emit = defineEmits(['post-submitted']);

const { postContent, dialogVisible, submitPost, openDialog } = usePostForm();

watch(() => props.post, (newPost) => {
  if (props.isEditMode && newPost) {
    openDialog(newPost);
  }
}, { immediate: true });

const handleSubmit = async () => {
  const success = await submitPost(props.isEditMode, props.post?.id);
  if (success) {
    emit('post-submitted');
    // Consider using a more reactive way to update the post list instead of reloading the page
    setTimeout(() => {
      window.location.reload();
    }, 1000);
  }
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
