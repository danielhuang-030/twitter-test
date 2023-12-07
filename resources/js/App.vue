<template>
    <div id="app">
      <header>
        <h1>twitter-test</h1>
        <nav>
          <ul>
            <li>
              <a href="/">Home</a>
            </li>
            <li v-if="!isLoggedIn">
              <a href="/login">Login</a>
            </li>
            <li v-else>
              <a href="#">{{ userData.name }}</a> <!-- 父選項 -->
              <ul class="submenu">
                <li><a href="#" @click="openPostDialog">Create New Post</a></li>
                <post-form ref="postForm" @post-submitted="handlePostSubmit"></post-form>
                <li><a href="/posts">Posts</a></li>
                <li><a href="#" @click="logout">Logout</a></li>
              </ul>
            </li>
          </ul>
        </nav>
      </header>

      <router-view :key="$route.fullPath"></router-view> <!-- 當前路由的組件將在這裡顯示 -->

      <footer>
        <p>&copy; {{ currentYear }} twitter-test</p>
      </footer>
    </div>
  </template>

  <script>
  import apiService from './apiService';
  import PostForm from './components/PostForm.vue';

  export default {
    name: 'App',
    components: {
      PostForm
    },
    data() {
      return {
        currentYear: new Date().getFullYear(),
        isLoggedIn: false,
        userData: {}
      };
    },
    created() {
      this.checkLogin();
    },
    watch: {
      '$route': 'checkLogin'
    },
    methods: {
      checkLogin() {
        // 檢查登入狀態的邏輯
        // 例如，檢查 localStorage 中是否有 token
        const token = localStorage.getItem('user-token');
        this.isLoggedIn = !!token;
        if (token) {
          // 假設用戶名存儲在 localStorage 或 Vuex store 中
          this.userData = JSON.parse(localStorage.getItem('user-data'));
        }
      },
      async logout() {
        try {
          await apiService.logout();
          // 清除本地存儲的認證信息
          localStorage.removeItem('user-token');
          localStorage.removeItem('user-name');
          this.isLoggedIn = false;
          this.userName = '';
          // 重定向到登入頁面
          this.$router.push('/login');
        } catch (error) {
          console.error('Logout error:', error);
        }
      },
      openPostDialog() {
        this.$refs.postForm.openDialog();
      },
      handlePostSubmit(postContent) {
        // 處理新增貼文的邏輯
      }
    }
  };
  </script>

  <style>
  /* 全局樣式 */
    header, footer {
        background-color: #1DA1F2;
        color: white;
        text-align: center;
        padding: 10px 0;
    }

    nav ul {
        list-style: none;
        padding: 0;
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
