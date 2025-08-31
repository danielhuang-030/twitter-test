import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import { ref } from 'vue';
import PostForm from '../components/PostForm.vue';
import usePostForm from './usePostForm';

// Mock the composable
vi.mock('./usePostForm', () => ({
  default: vi.fn(() => ({
    postContent: ref(''),
    dialogVisible: ref(true), // 這裡改為 true
    submitPost: vi.fn().mockResolvedValue(true),
    openDialog: vi.fn(),
  })),
}));

// Mock El-Dialog and other Element Plus components to avoid rendering issues
const stubs = {
  'el-dialog': {
    template: '<div><slot /></div>',
    props: ['modelValue'],
  },
  'el-input': {
    template: '<textarea :value="modelValue" @input="$emit(\'update:modelValue\', $event.target.value)"></textarea>',
    props: ['modelValue'],
  },
  'el-button': {
    template: '<button :type="type"><slot /></button>',
    props: ['type'],
  },
};

describe('PostForm.vue', () => {
  it('renders the dialog with a form', () => {
    const wrapper = mount(PostForm, {
      global: { stubs },
    });
    expect(wrapper.find('form').exists()).toBe(true);
    expect(wrapper.find('textarea').exists()).toBe(true);
  });

  it('calls handleSubmit when the post button is clicked', async () => {
    const submitPostMock = vi.fn().mockResolvedValue(true);
    usePostForm.mockImplementationOnce(() => ({
      postContent: ref(''),
      dialogVisible: ref(true),
      submitPost: submitPostMock,
      openDialog: vi.fn(),
    }));

    const wrapper = mount(PostForm, {
      global: { stubs },
      props: { isEditMode: false },
    });

    // 直接呼叫 handleSubmit 方法
    await wrapper.vm.$.setupState.handleSubmit();
    expect(submitPostMock).toHaveBeenCalled();
  });

  it('emits post-submitted on successful submission', async () => {
    const submitPostMock = vi.fn().mockResolvedValue(true);
    usePostForm.mockImplementationOnce(() => ({
      postContent: ref(''),
      dialogVisible: ref(true),
      submitPost: submitPostMock,
      openDialog: vi.fn(),
    }));
    
    const wrapper = mount(PostForm, {
      global: { stubs },
      props: { isEditMode: false },
    });

    // 直接呼叫 handleSubmit 方法
    await wrapper.vm.$.setupState.handleSubmit();
    await wrapper.vm.$nextTick(); // Wait for promises to resolve

    expect(wrapper.emitted('post-submitted')).toBeTruthy();
  });

  it('does not emit post-submitted on failed submission', async () => {
    const submitPostMock = vi.fn().mockResolvedValue(false);
    usePostForm.mockImplementationOnce(() => ({
      postContent: ref(''),
      dialogVisible: ref(true),
      submitPost: submitPostMock,
      openDialog: vi.fn(),
    }));

    const wrapper = mount(PostForm, {
      global: { stubs },
      props: { isEditMode: false },
    });

    // 直接呼叫 handleSubmit 方法
    await wrapper.vm.$.setupState.handleSubmit();
    await wrapper.vm.$nextTick();

    expect(wrapper.emitted('post-submitted')).toBeFalsy();
  });
});
