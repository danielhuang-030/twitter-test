<template>
  <el-dialog v-model="dialogVisible" title="Post">
    <form @submit.prevent="submitPost">
      <el-input type="textarea" v-model="postContent" placeholder="Share something new..." />
      <span class="dialog-footer">
        <el-button @click="dialogVisible = false">Cancel</el-button>
        <el-button type="primary" @click="submitPost">Post</el-button>
      </span>
    </form>
  </el-dialog>
</template>

<script setup>
import { ref, watch, defineProps, defineEmits } from 'vue';
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

watch(() => props.post, (newPost) => {
  if (props.isEditMode && newPost) {
    postContent.value = newPost.content;
    dialogVisible.value = true;
  } else {
    postContent.value = '';
    dialogVisible.value = false;
  }
}, { immediate: true });

const submitPost = async () => {
  if (!postContent.value.trim()) {
    ElMessage.error('Please enter some content.');
    return;
  }

  try {
    let response;
    if (props.isEditMode) {
      response = await apiService.updatePost(props.post.id, { content: postContent.value });
    } else {
      response = await apiService.createPost({ content: postContent.value });
    }
    ElMessage.success(response.data.message);
    postContent.value = '';
    dialogVisible.value = false;
    emit('post-submitted');

    setTimeout(() => {
      window.location.reload();
    }, 2000);
  } catch (error) {
    // console.error(error);
  }
};

const openDialog = () => {
  dialogVisible.value = true;
};

defineExpose({
  openDialog
});
</script>

<style scoped>
</style>
