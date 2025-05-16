import { createRouter, createWebHistory } from 'vue-router';
import store from './store';

const HomeComponent = () => import('./components/HomeComponent.vue');
const LoginComponent = () => import('./components/LoginComponent.vue');
const UserPostsComponent = () => import('./components/UserPostsComponent.vue');

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
    history: createWebHistory('/'),
    routes
});

router.beforeEach((to, from, next) => {
  const publicPages = ['/login', '/']; // 不需要認證的頁面
  const authRequired = !publicPages.includes(to.path);

  store.dispatch('checkLogin').then(loggedIn => {
    if (loggedIn && to.path === '/login') {
      next('/');
    } else if (authRequired && !loggedIn) {
      next('/login');
    } else {
      next();
    }
  }).catch(() => {
    if (authRequired) {
      next('/login');
    } else {
      next();
    }
  });
});

export default router;

