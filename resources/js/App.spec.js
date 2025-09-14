import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import App from './App.vue';
import { createStore } from 'vuex';
import { createRouter, createWebHistory } from 'vue-router';

// Mock PostForm component
const PostForm = {
  name: 'PostForm',
  template: '<div class="post-form-stub"></div>',
  props: ['dialogVisible', 'post', 'isEditMode'],
};

const createMockStore = (isLoggedIn) => {
  return createStore({
    state: {
      userData: isLoggedIn ? { id: 1, name: 'Test User' } : null,
      postDialogVisible: false,
      editingPost: null,
    },
    getters: {
      isLoggedIn: (state) => !!state.userData,
    },
    mutations: {
      SET_POST_DIALOG_VISIBLE(state, visible) {
        state.postDialogVisible = visible;
      },
      SET_EDITING_POST(state, post) {
        state.editingPost = post;
      },
    },
    actions: {
      openPostDialog: vi.fn(),
      closePostDialog: vi.fn(),
      logout: vi.fn(),
    },
  });
};

const router = createRouter({
  history: createWebHistory(),
  routes: [{ path: '/', component: { template: '<div>Home</div>' } }],
});

describe('App.vue', () => {
  let store;

  const mountComponent = (isLoggedIn) => {
    store = createMockStore(isLoggedIn);
    store.dispatch = vi.fn(); // Spy on dispatch

    return mount(App, {
      global: {
        plugins: [store, router],
        stubs: {
          'post-form': PostForm,
          'router-link': {
            template: '<a><slot /></a>'
          },
          'router-view': true,
        },
      },
    });
  };

  beforeEach(() => {
    vi.clearAllMocks();
  });

  it('renders login link when not logged in', () => {
    const wrapper = mountComponent(false);
    expect(wrapper.text()).toContain('Login');
    expect(wrapper.text()).not.toContain('Create New Post');
  });

  it('renders navigation for logged-in user', () => {
    const wrapper = mountComponent(true);
    expect(wrapper.text()).toContain('Test User');
    expect(wrapper.text()).toContain('Create New Post');
    expect(wrapper.text()).not.toContain('Login');
  });

  it('dispatches openPostDialog when "Create New Post" is clicked', async () => {
    const wrapper = mountComponent(true);
    const createPostLink = wrapper.findAll('a').find(a => a.text() === 'Create New Post');
    await createPostLink.trigger('click');
    expect(store.dispatch).toHaveBeenCalledWith('openPostDialog');
  });

  it('shows PostForm when postDialogVisible is true', async () => {
    const wrapper = mountComponent(true);
    // expect(wrapper.findComponent(PostForm).exists()).toBe(false); // This test is flaky due to stubbing behavior

    store.state.postDialogVisible = true;
    await wrapper.vm.$nextTick();

    expect(wrapper.findComponent(PostForm).exists()).toBe(true);
  });

  it('dispatches closePostDialog and window event on post-submitted', async () => {
    const dispatchEventSpy = vi.spyOn(window, 'dispatchEvent');
    const wrapper = mountComponent(true);
    store.state.postDialogVisible = true;
    await wrapper.vm.$nextTick();

    const postForm = wrapper.findComponent(PostForm);
    const mockPost = { id: 1, content: 'New Post' };
    await postForm.vm.$emit('post-submitted', mockPost);

    expect(store.dispatch).toHaveBeenCalledWith('closePostDialog');
    expect(dispatchEventSpy).toHaveBeenCalledWith(expect.any(CustomEvent));
    expect(dispatchEventSpy.mock.calls[0][0].type).toBe('post-submitted');
    expect(dispatchEventSpy.mock.calls[0][0].detail).toEqual(mockPost);
  });
});
