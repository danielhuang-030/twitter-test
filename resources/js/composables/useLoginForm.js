import { ref, computed } from 'vue';
import { useVuelidate } from '@vuelidate/core';
import { required, email, minLength, helpers } from '@vuelidate/validators';
import { useStore } from 'vuex';
import router from '../router';
import apiService from '../apiService';
import { ElMessage } from 'element-plus';

export default function useLoginForm() {
  const store = useStore();
  
  const formState = ref({
    email: '',
    password: '',
  });

  const rules = computed(() => ({
    email: { required, email },
    password: {
      required,
      minLength: minLength(8),
      alphaNum: helpers.withMessage('Password must contain letters and numbers', (value) => /[a-zA-Z]/.test(value) && /[0-9]/.test(value))
    },
  }));

  const v$ = useVuelidate(rules, formState);

  const isSubmitting = ref(false);

  const login = async () => {
    const isFormCorrect = await v$.value.$validate();
    if (!isFormCorrect) {
      ElMessage.error('Please check the form for errors.');
      return;
    }

    isSubmitting.value = true;
    try {
      const response = await apiService.login(formState.value);
      store.dispatch('setToken', response.data.data.token);
      store.dispatch('setUserData', response.data.data.user);
      localStorage.setItem('user-token', response.data.data.token);
      await store.dispatch('checkLogin'); // Re-initialize WebSocket etc.
      router.push({ name: 'home' });
    } catch (error) {
      ElMessage.error(error.response?.data?.message || 'Login failed. Please check your credentials.');
    } finally {
      isSubmitting.value = false;
    }
  };

  return { formState, v$, login, isSubmitting };
}
