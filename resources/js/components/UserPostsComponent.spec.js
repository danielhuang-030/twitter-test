import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import UserPostsComponent from './UserPostsComponent.vue';
import apiService from '../apiService.js';
import { createStore } from 'vuex';

// Mock dependencies
vi.mock('../apiService.js', () => ({
  default: {
    getUserPosts: vi.fn(),
  },
}));

vi.mock('vue-router', () => ({
  useRoute: () => ({
    params: { userId: '123' }, // Mock userId
  }),
}));

const createMockStore = () => {
  return createStore({
    actions: {
      openPostDialog: vi.fn(),
    },
  });
};

describe('UserPostsComponent.vue', () => {
  let store;
  let wrapper;
  let postsMock;

  const mountComponent = () => {
    store = createMockStore();
    store.dispatch = vi.fn();

    postsMock = [
      { id: 1, content: 'User Post 1', author_id: 123 },
      { id: 2, content: 'User Post 2', author_id: 123 },
    ];

    apiService.getUserPosts.mockResolvedValue({ 
      data: { 
        data: { 
          data: [...postsMock], 
          pagination: { total: postsMock.length } 
        }
      }
    });

    wrapper = mount(UserPostsComponent, {
      global: {
        plugins: [store],
        stubs: {
          PostsList: {
            name: 'PostsList',
            template: '<div class="posts-list-stub"></div>',
            props: ['posts'],
          },
        },
      },
    });
  };

  beforeEach(() => {
    vi.clearAllMocks();
    mountComponent();
  });

  it('fetches user posts on mount and passes them to PostsList', async () => {
    expect(apiService.getUserPosts).toHaveBeenCalledWith({ userId: '123', page: 1, perPage: 10 });
    await wrapper.vm.$nextTick();
    const postsList = wrapper.findComponent({ name: 'PostsList' });
    expect(postsList.props('posts')).toEqual(postsMock);
  });

  it('dispatches openPostDialog action when handleEditPost is called', async () => {
    const postToEdit = { id: 1, content: 'Edit me', author_id: 123 };
    const postsList = wrapper.findComponent({ name: 'PostsList' });
    await postsList.vm.$emit('edit-post', postToEdit);
    
    expect(store.dispatch).toHaveBeenCalledWith('openPostDialog', postToEdit);
  });

  it('removes a post from the list when handlePostDeleted is called', async () => {
    const postIdToDelete = 1;
    const postsList = wrapper.findComponent({ name: 'PostsList' });
    await postsList.vm.$emit('post-deleted', postIdToDelete);

    const updatedPosts = wrapper.findComponent({ name: 'PostsList' }).props('posts');
    expect(updatedPosts.find(p => p.id === postIdToDelete)).toBeUndefined();
    expect(updatedPosts.length).toBe(postsMock.length - 1);
  });

  it('adds a new post when post-submitted window event is fired for the same user', async () => {
    const newPost = { id: 3, content: 'New Post', author_id: 123 };
    window.dispatchEvent(new CustomEvent('post-submitted', { detail: newPost }));
    await wrapper.vm.$nextTick();

    const updatedPosts = wrapper.findComponent({ name: 'PostsList' }).props('posts');
    expect(updatedPosts[0]).toEqual(newPost);
    expect(updatedPosts.length).toBe(postsMock.length + 1);
  });

  it('does not add a post when post-submitted window event is for a different user', async () => {
    const newPost = { id: 4, content: 'Another User Post', author_id: 999 };
    window.dispatchEvent(new CustomEvent('post-submitted', { detail: newPost }));
    await wrapper.vm.$nextTick();

    const updatedPosts = wrapper.findComponent({ name: 'PostsList' }).props('posts');
    expect(updatedPosts.length).toBe(postsMock.length);
  });

  it('removes window event listener on unmount', () => {
    const removeEventListenerSpy = vi.spyOn(window, 'removeEventListener');
    wrapper.unmount();
    expect(removeEventListenerSpy).toHaveBeenCalledWith('post-submitted', expect.any(Function));
  });
});
