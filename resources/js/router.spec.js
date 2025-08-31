import { describe, it, expect, vi } from 'vitest';
import { createRouter, createMemoryHistory } from 'vue-router';
import { routes } from './router';

// Mock apiService to avoid importing element-plus and its dependencies
vi.mock('./apiService', () => ({
  default: {},
})); // Assuming you export routes from your router file

// Mock components to avoid rendering them
const HomeComponent = { template: '<div>Home</div>' };
const LoginComponent = { template: '<div>Login</div>' };
const UserPostsComponent = { template: '<div>User Posts</div>' };
const NotFoundComponent = { template: '<div>Not Found</div>' };

// We need to slightly modify the original routes array to use mock components
const mockedRoutes = routes.map(route => {
  if (route.name === 'home') return { ...route, component: HomeComponent };
  if (route.name === 'login') return { ...route, component: LoginComponent };
  if (route.name === 'UserPosts') return { ...route, component: UserPostsComponent };
  if (route.name === 'NotFound') return { ...route, component: NotFoundComponent };
  return route;
});

describe('Vue Router', () => {
  it('should render the HomeComponent for the root path', async () => {
    const router = createRouter({
      history: createMemoryHistory(),
      routes: mockedRoutes,
    });
    router.push('/');
    await router.isReady();
    expect(router.currentRoute.value.name).toBe('home');
  });

  it('should render the LoginComponent for the /login path', async () => {
    const router = createRouter({
      history: createMemoryHistory(),
      routes: mockedRoutes,
    });
    router.push('/login');
    await router.isReady();
    expect(router.currentRoute.value.name).toBe('login');
  });

  it('should render the NotFoundComponent for a non-existent path', async () => {
    const router = createRouter({
      history: createMemoryHistory(),
      routes: mockedRoutes,
    });
    router.push('/a-path-that-does-not-exist');
    await router.isReady();
    expect(router.currentRoute.value.name).toBe('NotFound');
  });
});
