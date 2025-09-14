import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import HomeComponent from './HomeComponent.vue';
import apiService from '../apiService';
import { createStore } from 'vuex';

// Mock apiService
vi.mock('../apiService', () => ({
  default: {
    getPosts: vi.fn(),
  },
}));

const createMockStore = () => {
  return createStore({
    actions: {
      openPostDialog: vi.fn(),
    },
  });
};

describe('HomeComponent.vue', () => {
  let store;
  let wrapper;
  let postsMock;

  beforeEach(() => {
    vi.clearAllMocks();
    
    postsMock = [
      { id: 1, content: 'Post 1' },
      { id: 2, content: 'Post 2' },
    ];

    store = createMockStore();
    apiService.getPosts.mockResolvedValue({ 
      data: { 
        data: { 
          data: [...postsMock], // Return a copy to prevent mutation
          pagination: { total: postsMock.length } 
        }
      }
    });

    wrapper = mount(HomeComponent, {
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
  });

  it('fetches posts on mount and passes them to PostsList', async () => {
    expect(apiService.getPosts).toHaveBeenCalledWith({ page: 1, perPage: 10 });
    await wrapper.vm.$nextTick();
    const postsList = wrapper.findComponent({ name: 'PostsList' });
    expect(postsList.props('posts')).toEqual(postsMock);
  });

  it('dispatches openPostDialog action when handleEditPost is called', async () => {
    const postToEdit = { id: 3, content: 'Edit me' };
    store.dispatch = vi.fn(); // Spy on dispatch
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

  it('updates a post when post-submitted window event is fired for an existing post', async () => {
    const updatedPost = { id: 1, content: 'Updated Post 1' };
    window.dispatchEvent(new CustomEvent('post-submitted', { detail: updatedPost }));
    await wrapper.vm.$nextTick();

    const updatedPosts = wrapper.findComponent({ name: 'PostsList' }).props('posts');
    expect(updatedPosts.find(p => p.id === updatedPost.id).content).toBe(updatedPost.content);
    expect(updatedPosts.length).toBe(postsMock.length);
  });

  it('adds a new post when post-submitted window event is fired for a new post', async () => {
    const newPost = { id: 3, content: 'New Post' };
    window.dispatchEvent(new CustomEvent('post-submitted', { detail: newPost }));
    await wrapper.vm.$nextTick();

    const updatedPosts = wrapper.findComponent({ name: 'PostsList' }).props('posts');
    expect(updatedPosts[0]).toEqual(newPost);
    expect(updatedPosts.length).toBe(postsMock.length + 1);
  });

  it('removes window event listener on unmount', () => {
    const removeEventListenerSpy = vi.spyOn(window, 'removeEventListener');
    wrapper.unmount();
    expect(removeEventListenerSpy).toHaveBeenCalledWith('post-submitted', expect.any(Function));
  });
});
