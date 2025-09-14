<template>
  <div id="app">
    <header>
      <h1>twitter-test</h1>
      <nav>
        <ul>
          <li>
            <router-link to="/">Home</router-link>
          </li>
          <template v-if="isLoggedIn">
            <li>
              <a href="#" @click.prevent="openPostDialog">Create New Post</a>
            </li>
            <li>
              <a href="javascript:void(0);">{{ userData.name }}</a> <!-- 父選項 -->
              <ul class="submenu">
                <li><router-link :to="`/user/${userData.id}/posts`">Posts</router-link></li>
                <li><a href="#" @click="logout">Logout</a></li>
              </ul>
            </li>
          </template>
          <li v-else>
            <router-link to="/login">Login</router-link>
          </li>
        </ul>
      </nav>
    </header>

    <!-- Global Post Form Dialog -->
    <post-form 
      v-model:dialogVisible="postDialogVisible"
      :post="editingPost"
      :isEditMode="isEditMode" 
      @post-submitted="handlePostSubmit"
    ></post-form>

    <router-view :key="$route.fullPath"></router-view>

    <footer>
      <p>&copy; {{ currentYear }} twitter-test</p>
    </footer>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useStore } from 'vuex';
import PostForm from './components/PostForm.vue';

const store = useStore();
const currentYear = new Date().getFullYear();

const userData = computed(() => store.state.userData);
const isLoggedIn = computed(() => !!userData.value);
const postDialogVisible = computed({
  get: () => store.state.postDialogVisible,
  set: (value) => {
    if (!value) {
      store.dispatch('closePostDialog');
    }
  }
});
const editingPost = computed(() => store.state.editingPost);
const isEditMode = computed(() => !!store.state.editingPost);

const logout = async () => {
  try {
    await store.dispatch('logout');
    window.location.reload();
  } catch (error) {
    console.error('Logout error:', error);
  }
};

const openPostDialog = () => {
  store.dispatch('openPostDialog');
};

const handlePostSubmit = (submittedPost) => {
  window.dispatchEvent(new CustomEvent('post-submitted', { detail: submittedPost }));
  store.dispatch('closePostDialog');
};
</script>

<style scoped>
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
