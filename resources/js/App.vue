<template>
  <div id="app">
    <header>
      <h1 class="logo">twitter-test</h1>
      <nav>
        <ul>
          <li>
            <router-link to="/">Home</router-link>
          </li>
          <template v-if="isLoggedIn">
            <li>
              <a href="#" @click.prevent="openPostDialog">Create New Post</a>
            </li>
            <li class="user-menu">
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
header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0 2rem;
  background-color: #1DA1F2;
  color: white;
  height: 64px;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.logo {
  font-size: 1.5rem;
  font-weight: bold;
}

nav ul {
  display: flex;
  align-items: center;
  list-style: none;
  padding: 0;
  margin: 0;
  gap: 0.5rem;
}

nav ul li a {
  display: block;
  padding: 0.5rem 1rem;
  color: white;
  text-decoration: none;
  border-radius: 6px;
  transition: background-color 0.2s ease;
}

nav ul li a:hover {
  background-color: rgba(255, 255, 255, 0.1);
}

.user-menu {
  position: relative;
}

.submenu {
  position: absolute;
  top: calc(100% + 10px);
  right: 0;
  background-color: white;
  border-radius: 8px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
  padding: 0.5rem;
  min-width: 180px;
  z-index: 100;
  
  /* Animation */
  opacity: 0;
  visibility: hidden;
  transform: translateY(10px) scale(0.98);
  transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s;
}

.user-menu:hover .submenu {
  opacity: 1;
  visibility: visible;
  transform: translateY(0) scale(1);
}

.submenu li {
  display: block;
}

.submenu li a {
  color: #333;
  padding: 0.75rem 1rem;
}

.submenu li a:hover {
  background-color: #f5f5f5;
}

footer {
  background-color: #1DA1F2;
  color: white;
  text-align: center;
  padding: 10px 0;
}
</style>
