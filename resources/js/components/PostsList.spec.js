import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import PostsList from './PostsList.vue';
import apiService from '../apiService';
import { createStore } from 'vuex';

// Mock dependencies
vi.mock('../apiService', () => ({
  default: {
    followUser: vi.fn(),
    unfollowUser: vi.fn(),
  },
}));

const createMockStore = (currentUserId) => {
  return createStore({
    state: {
      userData: { id: currentUserId },
    },
  });
};

describe('PostsList.vue', () => {
  let store;
  let wrapper;
  let postsMock;

  const authorIdToFollow = 123;
  const currentUserId = 999;
  const dateNow = new Date().toISOString();

  beforeEach(() => {
    // Reset mocks and mount component before each test
    vi.clearAllMocks();

    postsMock = [
      { id: 1, content: 'Post 1', author: 'Author One', author_id: authorIdToFollow, is_followed: false, updated_at: dateNow },
      { id: 2, content: 'Post 2', author: 'Another Author', author_id: 456, is_followed: false, updated_at: dateNow },
      { id: 3, content: 'Post 3', author: 'Author One', author_id: authorIdToFollow, is_followed: false, updated_at: dateNow },
    ];

    store = createMockStore(currentUserId);
    apiService.followUser.mockResolvedValue({ data: { message: 'Success' } });

    wrapper = mount(PostsList, {
      props: {
        posts: postsMock,
      },
      global: {
        plugins: [store],
        stubs: {
          'el-pagination': true,
        },
      },
    });
  });

  it('updates the follow status for all posts by the same author after a follow action', async () => {
    // Find the first follow button for the target author using the data attribute
    const followButton = wrapper.find(`.follow-action[data-author-id="${authorIdToFollow}"]`);

    // Pre-check: ensure all posts from the author are not followed
    const postsByAuthor = wrapper.props('posts').filter(p => p.author_id === authorIdToFollow);
    expect(postsByAuthor.every(p => !p.is_followed)).toBe(true);

    // Trigger the follow action
    await followButton.trigger('click');

    // Assert that the API was called
    expect(apiService.followUser).toHaveBeenCalledWith(authorIdToFollow);

    // Post-check: ensure all posts from the author are now followed
    expect(postsByAuthor.every(p => p.is_followed)).toBe(true);

    // Check that other authors' posts are unaffected
    const otherPost = wrapper.props('posts').find(p => p.author_id === 456);
    expect(otherPost.is_followed).toBe(false);
  });
});
