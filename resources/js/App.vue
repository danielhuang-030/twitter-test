<template>
  <div id="app">
    <header>
      <h1>twitter-test</h1>
      <nav>
        <ul>
          <li>
            <router-link to="/">Home</router-link>
          </li>
          <li v-if="!isLoggedIn">
            <router-link to="/login">Login</router-link>
          </li>
          <li v-else>
            <a href="javascript:void(0);">{{ userData.name }}</a> <!-- 父選項 -->
            <ul class="submenu">
              <li><a href="#" @click="openPostDialog">Create New Post</a></li>
              <post-form ref="postFormRef" @post-submitted="handlePostSubmit" :isEditMode="false"></post-form>
              <li><router-link :to="`/user/${userData.id}/posts`">Posts</router-link></li>
              <li><a href="#" @click="logout">Logout</a></li>
            </ul>
          </li>
        </ul>
      </nav>
    </header>

    <router-view :key="$route.fullPath"></router-view>

    <footer>
      <p>&copy; {{ currentYear }} twitter-test</p>
    </footer>
  </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { useStore } from 'vuex';
import PostForm from './components/PostForm.vue';

const store = useStore();
const currentYear = new Date().getFullYear();

const isLoggedIn = ref(false);
const userData = ref(null);
const postFormRef = ref(null);

watch(() => store.state.userData, (newUserData) => {
  userData.value = newUserData;
  isLoggedIn.value = !!newUserData;
});

const logout = async () => {
  try {
    store.dispatch('logout').then(() => {
      window.location.reload();
    });
  } catch (error) {
    console.error('Logout error:', error);
  }
};

const openPostDialog = () => {
  if (postFormRef.value) {
    postFormRef.value.openDialog();
  }
};

const handlePostSubmit = (postContent) => {
  // ... 處理貼文提交
};
</script>

<style>
header, footer {
  background-color: #1DA1F2;
  color: white;
  text-align: center;
  padding: 10px 0;
}

nav ul {
  list-style: none;
  padding: 0;
  text-align: center;
}

nav ul li {
  display: inline;
  margin: 0 10px;
  position: relative;
}

nav ul li a {
  color: white;
  text-decoration: none;
}

nav ul li:hover .submenu,
.submenu:hover {
  display: block;
}

/* 子選單的基本樣式 */
.submenu {
  display: none;
  position: absolute;
  background-color: #1DA1F2;
  left: 0; /* 將子選單對齊到父元素的左側 */
  top: 100%; /* 將子選單放在父元素的下方 */
  min-width: 150px; /* 例如，設置最小寬度 */
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); /* 可選，添加陰影 */
  padding: 5px 0; /* 添加一些內邊距 */
}

.submenu li {
  display: block; /* 讓子選單項目垂直顯示 */
  padding: 5px 10px; /* 為子選單項目添加填充 */
}
</style>
