import { createRouter, createWebHistory } from 'vue-router';
import store from './store';

const HomeComponent = () => import('./components/HomeComponent.vue');
const LoginComponent = () => import('./components/LoginComponent.vue');
const UserPostsComponent = () => import('./components/UserPostsComponent.vue');
const NotFoundComponent = () => import('./components/NotFound.vue');

export const routes = [
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
    },
    { 
      path: '/:pathMatch(.*)*', 
      name: 'NotFound', 
      component: NotFoundComponent 
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
  console.log('[Router] Navigating from:', from.path, 'to:', to.path);
  const publicPages = ['/login', '/']; // 不需要認證的頁面
  const authRequired = !publicPages.includes(to.path);
  console.log('[Router] Auth required:', authRequired);

  store.dispatch('checkLogin').then(loggedIn => {
    console.log('[Router] User logged in:', loggedIn);
    if (loggedIn && to.path === '/login') {
      console.log('[Router] Redirecting to home (user is logged in)');
      next('/');
    } else if (authRequired && !loggedIn) {
      console.log('[Router] Redirecting to login (auth required)');
      next('/login');
    } else {
      console.log('[Router] Proceeding to route');
      next();
    }
  }).catch((error) => {
    console.log('[Router] Error checking login:', error);
    if (authRequired) {
      console.log('[Router] Redirecting to login (auth required, error occurred)');
      next('/login');
    } else {
      console.log('[Router] Proceeding to route (no auth required, error occurred)');
      next();
    }
  });
});

export default router;

