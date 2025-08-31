import { describe, it, expect, vi, beforeEach } from 'vitest';
import { useVuelidate } from '@vuelidate/core';
import useLoginForm from './useLoginForm';
import apiService from '../apiService';
import router from '../router';
import { useStore } from 'vuex';

// Mock dependencies
vi.mock('@vuelidate/core');
vi.mock('vuex');
vi.mock('../router');
vi.mock('../apiService');
vi.mock('element-plus', () => ({
  ElMessage: {
    error: vi.fn(),
  },
}));

describe('useLoginForm', () => {
  let mockValidate;
  let mockDispatch;

  beforeEach(() => {
    mockValidate = vi.fn();
    mockDispatch = vi.fn().mockResolvedValue(true);
    
    useVuelidate.mockReturnValue({
      value: {
        $validate: mockValidate,
        email: { $error: false, $errors: [] },
        password: { $error: false, $errors: [] },
      },
    });
    
    useStore.mockReturnValue({
      dispatch: mockDispatch,
    });

    apiService.login.mockResolvedValue({
      data: {
        data: {
          token: 'test-token',
          user: { id: 1, name: 'Test User' },
        },
      },
    });
    
    vi.clearAllMocks();
  });

  it('should not call login api if validation fails', async () => {
    mockValidate.mockResolvedValue(false);
    const { login } = useLoginForm();
    await login();
    expect(apiService.login).not.toHaveBeenCalled();
  });

  it('should call login api if validation succeeds', async () => {
    mockValidate.mockResolvedValue(true);
    const { login } = useLoginForm();
    await login();
    expect(apiService.login).toHaveBeenCalled();
  });

  it('should dispatch actions and navigate on successful login', async () => {
    mockValidate.mockResolvedValue(true);
    const { login } = useLoginForm();
    await login();

    expect(mockDispatch).toHaveBeenCalledWith('setToken', 'test-token');
    expect(mockDispatch).toHaveBeenCalledWith('setUserData', { id: 1, name: 'Test User' });
    expect(mockDispatch).toHaveBeenCalledWith('checkLogin');
    expect(router.push).toHaveBeenCalledWith({ name: 'home' });
  });

  it('should handle login failure', async () => {
    mockValidate.mockResolvedValue(true);
    apiService.login.mockRejectedValue({ response: { data: { message: 'Invalid credentials' } } });
    const { login } = useLoginForm();
    await login();
    
    expect(router.push).not.toHaveBeenCalled();
  });
});
