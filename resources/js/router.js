import { createRouter, createWebHistory } from 'vue-router';
import HomeComponent from './components/HomeComponent.vue';
import LoginComponent from './components/LoginComponent.vue';
import UserPostsComponent from './components/UserPostsComponent.vue';

const routes = [
    {
        path: '/',
        name: 'home',
        component: HomeComponent
    },
    {
        path: '/login',
        name: 'login',
        component: LoginComponent
    },
    {
      path: '/user/:userId/posts',
      name: 'UserPosts',
      component: UserPostsComponent
    }
];

const router = createRouter({
    history: createWebHistory(process.env.BASE_URL),
    routes
});

router.beforeEach((to, from, next) => {
  const publicPages = ['/login', '/']; // 不需要認證的頁面
  const authRequired = !publicPages.includes(to.path);
  const loggedIn = localStorage.getItem('user-token');

  if (to.path === '/') {
    return next();
  }

  if (!authRequired && loggedIn) {
    return next('/'); // 如果已登入且訪問登入頁面，則重定向到首頁
  }

  if (authRequired && !loggedIn) {
    return next('/login');
  }

  next();
});

export default router;

