import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import PostForm from './PostForm.vue';
import apiService from '../apiService';
import { ElMessage } from 'element-plus';

// Mock apiService
vi.mock('../apiService', () => ({
  default: {
    createPost: vi.fn(),
    updatePost: vi.fn(),
  },
}));

// Mock Element Plus message component
vi.mock('element-plus', async (importOriginal) => {
    const actual = await importOriginal();
    return {
        ...actual,
        ElMessage: {
            success: vi.fn(),
            error: vi.fn(),
        },
    };
});

describe('PostForm.vue', () => {

  beforeEach(() => {
    vi.clearAllMocks();
  });

  const getWrapper = (props) => mount(PostForm, {
    props: {
      dialogVisible: true,
      isEditMode: false,
      ...props,
    },
    global: {
      stubs: {
        'el-dialog': {
          template: '<div v-if="modelValue"><slot /></div>',
          props: ['modelValue'],
        },
        'el-input': {
            template: '<textarea :value="modelValue" @input="$emit(\'update:modelValue\', $event.target.value)" />',
            props: ['modelValue'],
        },
        'el-button': {
            template: '<button @click="$emit(\'click\', $event)"><slot /></button>',
        },
      }
    }
  });

  it('creates a new post and emits events on success', async () => {
    const wrapper = getWrapper();
    const newPostContent = 'This is a new post';
    const mockPost = { id: 1, content: newPostContent };
    const mockResponse = { data: { message: 'Post created.', data: { post: mockPost } } };
    apiService.createPost.mockResolvedValue(mockResponse);

    const textarea = wrapper.find('textarea');
    await textarea.setValue(newPostContent);

    const postButton = wrapper.findAll('button').find(b => b.text() === 'Post');
    await postButton.trigger('click');

    expect(apiService.createPost).toHaveBeenCalledWith({ content: newPostContent });
    expect(ElMessage.success).toHaveBeenCalledWith('Post created.');
    expect(wrapper.emitted('post-submitted')[0][0]).toEqual(mockPost);
    expect(wrapper.emitted('update:dialogVisible')[0][0]).toBe(false);
  });

  it('updates an existing post and emits events on success', async () => {
    const existingPost = { id: 5, content: 'Original content' };
    const updatedContent = 'Updated content';
    const mockUpdatedPost = { ...existingPost, content: updatedContent };
    const mockResponse = { data: { message: 'Post updated.', data: { post: mockUpdatedPost } } };
    apiService.updatePost.mockResolvedValue(mockResponse);

    const wrapper = getWrapper({ isEditMode: true, post: existingPost });

    // Check if textarea is pre-filled
    const textarea = wrapper.find('textarea');
    expect(textarea.element.value).toBe(existingPost.content);

    await textarea.setValue(updatedContent);

    const postButton = wrapper.findAll('button').find(b => b.text() === 'Post');
    await postButton.trigger('click');

    expect(apiService.updatePost).toHaveBeenCalledWith(existingPost.id, { content: updatedContent });
    expect(ElMessage.success).toHaveBeenCalledWith('Post updated.');
    expect(wrapper.emitted('post-submitted')[0][0]).toEqual(mockUpdatedPost);
    expect(wrapper.emitted('update:dialogVisible')[0][0]).toBe(false);
  });

  it('shows an error message on submission failure', async () => {
    const wrapper = getWrapper();
    const errorMessage = 'An error occurred';
    const mockError = { response: { data: { message: errorMessage } } };
    apiService.createPost.mockRejectedValue(mockError);

    await wrapper.find('textarea').setValue('This will fail');
    const postButton = wrapper.findAll('button').find(b => b.text() === 'Post');
    await postButton.trigger('click');

    expect(ElMessage.error).toHaveBeenCalledWith(errorMessage);
    expect(wrapper.emitted('update:dialogVisible')).toBeUndefined();
  });

  it('emits update:dialogVisible when cancel button is clicked', async () => {
    const wrapper = getWrapper();
    const cancelButton = wrapper.findAll('button').find(b => b.text() === 'Cancel');
    await cancelButton.trigger('click');

    expect(wrapper.emitted('update:dialogVisible')[0][0]).toBe(false);
  });
});
