import { describe, it, expect, vi } from 'vitest';
import { mount } from '@vue/test-utils';
import LoginComponent from '../components/LoginComponent.vue';
import useLoginForm from '../composables/useLoginForm';

// Mock the composable
vi.mock('../composables/useLoginForm', () => ({
  default: vi.fn(() => ({
    formState: { email: '', password: '' },
    v$: {
      email: { $error: false, $errors: [], $touch: vi.fn() },
      password: { $error: false, $errors: [], $touch: vi.fn() },
    },
    login: vi.fn(),
    isSubmitting: false,
  })),
}));

const stubs = {
  // We don't need to stub Element Plus here since the component doesn't use it directly
};

describe('LoginComponent.vue', () => {
  it('renders a login form with email and password fields', () => {
    const wrapper = mount(LoginComponent, { global: { stubs } });
    const emailInput = wrapper.find('input#email');
    const passwordInput = wrapper.find('input#password');
    expect(emailInput.exists()).toBe(true);
    expect(passwordInput.exists()).toBe(true);
  });

  it('calls the login function from the composable when the form is submitted', async () => {
    const loginMock = vi.fn();
    useLoginForm.mockImplementationOnce(() => ({
      formState: { email: '', password: '' },
      v$: {
        email: { $error: false, $errors: [], $touch: vi.fn() },
        password: { $error: false, $errors: [], $touch: vi.fn() },
      },
      login: loginMock,
      isSubmitting: false,
    }));

    const wrapper = mount(LoginComponent, { global: { stubs } });

    await wrapper.find('form').trigger('submit');
    
    expect(loginMock).toHaveBeenCalled();
  });

  it('displays an error message when validation fails', async () => {
    // Re-mock for this specific test to simulate an error state
    useLoginForm.mockImplementationOnce(() => ({
      formState: { email: 'invalid', password: 'short' },
      v$: {
        email: { $error: true, $errors: [{ $uid: '1', $message: 'Invalid email' }] },
        password: { $error: true, $errors: [{ $uid: '2', $message: 'Password too short' }] },
        $touch: vi.fn(),
      },
      login: vi.fn(),
      isSubmitting: false,
    }));

    const wrapper = mount(LoginComponent, { global: { stubs } });
    
    expect(wrapper.find('.error-message').exists()).toBe(true);
    expect(wrapper.html()).toContain('Invalid email');
    expect(wrapper.html()).toContain('Password too short');
  });

  it('disables the submit button when isSubmitting is true', async () => {
     useLoginForm.mockImplementationOnce(() => ({
      formState: { email: '', password: '' },
      v$: {
        email: { $error: false, $errors: [] },
        password: { $error: false, $errors: [] },
        $touch: vi.fn(),
      },
      login: vi.fn(),
      isSubmitting: true, // Simulate submitting state
    }));

    const wrapper = mount(LoginComponent, { global: { stubs } });

    const button = wrapper.find('button[type="submit"]');
    expect(button.attributes('disabled')).toBeDefined();
    expect(button.text()).toContain('Logging in...');
  });
});
