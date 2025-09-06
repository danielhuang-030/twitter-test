import { describe, it, expect, vi, beforeEach } from 'vitest';
import { mount } from '@vue/test-utils';
import PostForm from './PostForm.vue';
import apiService from '../apiService';
import { ElMessage } from 'element-plus';

// 模擬 apiService
vi.mock('../apiService', () => ({
  default: {
    createPost: vi.fn(),
    updatePost: vi.fn(),
  },
}));

// 模擬 Element Plus 的 message 元件
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
  let wrapper;

  beforeEach(() => {
    // 在每個測試前重置 mock
    vi.clearAllMocks();
    wrapper = mount(PostForm, {
      global: {
        stubs: {
          'el-dialog': true,
          'el-input': true,
          'el-button': true,
        }
      }
    });
  });

  it('提交新貼文並觸發 post-submitted 事件', async () => {
    const newPostContent = '這是一篇新貼文';
    const mockPost = { id: 1, content: newPostContent };
    const mockResponse = {
      data: {
        message: '貼文建立成功。',
        data: {
          post: mockPost,
        },
      },
    };
    apiService.createPost.mockResolvedValue(mockResponse);

    // 開啟對話框以建立貼文
    await wrapper.vm.openDialog();
    await wrapper.vm.$nextTick();
    wrapper.vm.postContent = newPostContent;

    // 觸發提交
    await wrapper.vm.handleSubmit();
    await wrapper.vm.$nextTick();

    // 斷言 apiService 被呼叫
    expect(apiService.createPost).toHaveBeenCalledWith({ content: newPostContent });

    // 斷言成功訊息已顯示
    expect(ElMessage.success).toHaveBeenCalledWith('貼文建立成功。');

    // 斷言事件已觸發並帶有正確的 payload
    expect(wrapper.emitted('post-submitted')).toBeTruthy();
    expect(wrapper.emitted('post-submitted')[0][0]).toEqual(mockPost);

    // 斷言對話框已關閉
    expect(wrapper.vm.dialogVisible).toBe(false);
  });

  it('提交更新後的貼文並觸發 post-submitted 事件', async () => {
    const existingPost = { id: 5, content: '原始內容' };
    const updatedContent = '更新後的內容';
    const mockUpdatedPost = { ...existingPost, content: updatedContent };
    const mockResponse = {
      data: {
        message: '貼文更新成功。',
        data: {
          post: mockUpdatedPost,
        },
      },
    };
    apiService.updatePost.mockResolvedValue(mockResponse);

    // 開啟對話框以編輯貼文
    await wrapper.vm.openDialog(existingPost);
    await wrapper.vm.$nextTick();
    wrapper.vm.postContent = updatedContent;

    // 觸發提交
    await wrapper.vm.handleSubmit();
    await wrapper.vm.$nextTick();

    // 斷言 apiService 被呼叫
    expect(apiService.updatePost).toHaveBeenCalledWith(existingPost.id, { content: updatedContent });

    // 斷言成功訊息已顯示
    expect(ElMessage.success).toHaveBeenCalledWith('貼文更新成功。');

    // 斷言事件已觸發並帶有正確的 payload
    expect(wrapper.emitted('post-submitted')).toBeTruthy();
    expect(wrapper.emitted('post-submitted')[0][0]).toEqual(mockUpdatedPost);

    // 斷言對話框已關閉
    expect(wrapper.vm.dialogVisible).toBe(false);
  });

  it('提交失敗時顯示錯誤訊息', async () => {
    const newPostContent = '這將會失敗';
    const errorMessage = '發生錯誤';
    const mockError = {
        response: {
            data: {
                message: errorMessage,
            }
        }
    };
    apiService.createPost.mockRejectedValue(mockError);

    // 開啟對話框並設定內容
    await wrapper.vm.openDialog();
    await wrapper.vm.$nextTick();
    wrapper.vm.postContent = newPostContent;

    // 觸發提交
    await wrapper.vm.handleSubmit();
    await wrapper.vm.$nextTick();

    // 斷言錯誤訊息已顯示
    expect(ElMessage.error).toHaveBeenCalledWith(errorMessage);

    // 斷言對話框仍然開啟
    expect(wrapper.vm.dialogVisible).toBe(true);
  });
});
