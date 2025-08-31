import { describe, it, expect, vi } from 'vitest';
import { ref } from 'vue';
import usePostForm from './usePostForm';
import apiService from '../apiService';

// Mock ElMessage
vi.mock('element-plus', () => ({
  ElMessage: {
    error: vi.fn(),
    success: vi.fn(),
  },
}));

// Mock apiService
vi.mock('../apiService', () => ({
  default: {
    createPost: vi.fn(),
    updatePost: vi.fn(),
  },
}));

describe('usePostForm', () => {
  it('should initialize with empty post content and hidden dialog', () => {
    const { postContent, dialogVisible } = usePostForm();
    expect(postContent.value).toBe('');
    expect(dialogVisible.value).toBe(false);
  });

  it('should not submit if content is empty', async () => {
    const { submitPost } = usePostForm();
    const success = await submitPost(false);
    expect(success).toBe(false);
    expect(apiService.createPost).not.toHaveBeenCalled();
  });

  it('should not submit if content exceeds 280 characters', async () => {
    const { postContent, submitPost } = usePostForm();
    postContent.value = 'a'.repeat(281);
    const success = await submitPost(false);
    expect(success).toBe(false);
    expect(apiService.createPost).not.toHaveBeenCalled();
  });

  it('should call createPost for new posts', async () => {
    apiService.createPost.mockResolvedValue({ data: { message: 'Success' } });
    const { postContent, submitPost } = usePostForm();
    postContent.value = 'New post';
    await submitPost(false);
    expect(apiService.createPost).toHaveBeenCalledWith({ content: 'New post' });
  });

  it('should call updatePost for existing posts', async () => {
    apiService.updatePost.mockResolvedValue({ data: { message: 'Success' } });
    const { postContent, submitPost } = usePostForm();
    postContent.value = 'Updated post';
    await submitPost(true, 1);
    expect(apiService.updatePost).toHaveBeenCalledWith(1, { content: 'Updated post' });
  });
  
  it('should open dialog and set post content when editing', () => {
    const { postContent, dialogVisible, openDialog } = usePostForm();
    const post = { id: 1, content: 'Existing post' };
    openDialog(post);
    expect(dialogVisible.value).toBe(true);
    expect(postContent.value).toBe('Existing post');
  });
});
