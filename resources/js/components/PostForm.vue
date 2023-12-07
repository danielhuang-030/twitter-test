<!-- PostForm.vue -->
<template>
    <el-dialog v-model="dialogVisible" title="New Post">
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
  export default {
    data() {
      return {
        postContent: '',
        dialogVisible: false
      };
    },
    methods: {
      submitPost() {
        // 發送貼文的邏輯
        this.$emit('post-submitted', this.postContent);
        this.postContent = ''; // 清空表單
        this.dialogVisible = false; // 關閉彈窗
      },
      openDialog() {
        this.dialogVisible = true;
        console.log(this.dialogVisible);
      }
    }
  };
  </script>

  <style scoped>
  /* 您的樣式 */
  </style>
