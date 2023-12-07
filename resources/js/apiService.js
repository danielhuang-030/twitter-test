// apiService.js
import axios from 'axios';
import { ElMessageBox } from 'element-plus';

const apiClient = axios.create({
  baseURL: '/api/v1',
  withCredentials: false,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json'
  }
});

apiClient.interceptors.request.use(config => {
  const token = localStorage.getItem('user-token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});
apiClient.interceptors.response.use(
  response => response,
  error => {
    if (error.response.status === 401) {
      localStorage.removeItem('user-token');
      router.push({ name: 'Login' });
    }

    ElMessageBox.alert(
      error.response.data.message || 'An error occurred',
      'Error',
      {
        confirmButtonText: 'OK',
        type: 'error'
      }
    );

    return Promise.reject(error);
  }
);

export default {
  getUserProfile() {
    return apiClient.get('/profile');
  },
  signup(data) {
    return apiClient.post('/signup', data);
  },
  login(data) {
    return apiClient.post('/login', data);
  },
  logout() {
    return apiClient.get('/logout');
  },
  getPosts({ page, per_page }) {
    return apiClient.get('/posts', {
      params: {
        page: page,
        per_page: per_page
      }
    });
  }
};
