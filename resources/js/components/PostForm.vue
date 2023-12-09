<!-- PostForm.vue -->
<template>
    <el-dialog v-model="dialogVisible" title="Post">
      <form @submit.prevent="submitPost">
        <el-input type="textarea" v-model="postContent" placeholder="Share something new..." />
        <span slot="footer" class="dialog-footer">
          <el-button @click="dialogVisible = false">Cancel</el-button>
          <el-button type="primary" @click="submitPost">Post</el-button>
        </span>
      </form>
    </el-dialog>
  </template>

  <script>
  import apiService from '../apiService';

  export default {
    props: {
      post: Object,
      isEditMode: {
        type: Boolean,
        default: false
      }
    },
    watch: {
      post(newPost) {
        if (this.isEditMode) {
          this.postContent = newPost.content;
          this.dialogVisible = true;
        } else {
          this.postContent = '';
          this.dialogVisible = false;
        }
      }
    },
    data() {
      return {
        postContent: '',
        dialogVisible: false
      };
    },
    methods: {
      async submitPost() {
        if (!this.postContent.trim()) {
          this.$message.error('Please enter some content.');

          return;
        }

        try {
          if (this.isEditMode) {
            // 編輯現有帖子
            const response = await apiService.updatePost(this.post.id, { content: this.postContent });
            this.$message.success(response.data.message);
          } else {
            // 創建新帖子
            const response = await apiService.createPost({ content: this.postContent });
            this.$message.success(response.data.message);
          }
          this.postContent = ''; // 清空表單
          this.dialogVisible = false; // 關閉彈窗

          setTimeout(() => {
            window.location.reload();
          }, 2000);
          // 可以在這裡發送一個事件，通知父組件更新列表
          this.$emit('post-submitted');
        } catch (error) {
          // 錯誤處理已經在 apiService.js 的攔截器中處理
          console.error(error);
        }
      },
      openDialog() {
        this.dialogVisible = true;
      }
    }
  };
  </script>

  <style scoped>
  /* 您的樣式 */
  </style>
