import axios from 'axios';
import { ElMessageBox } from 'element-plus';
import router from './router';
import store from './store';

const apiClient = axios.create({
  baseURL: '/api/v1',
  withCredentials: false,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json'
  }
});

apiClient.interceptors.request.use(config => {
  const token = store.state.token;
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

apiClient.interceptors.response.use(
  response => response,
  error => {
    if (error.response.status === 401) {
      store.dispatch('setToken', null);
      store.dispatch('setUserData', null);
      router.push({ name: 'login' });
    } else {
      ElMessageBox.alert(
        error.response.data.message || 'An error occurred',
        'Error',
        {
          confirmButtonText: 'OK',
          type: 'error'
        }
      );
    }

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
  getPosts({ page, perPage }) {
    return apiClient.get('/posts', {
      params: {
        page: page,
        per_page: perPage
      }
    });
  },
  getUserPosts({ userId, page, perPage }) {
    return apiClient.get(`/users/${userId}/posts`, {
      params: {
        page: page,
        per_page: perPage
      }
    });
  },
  createPost(postData) {
    return apiClient.post('/posts', postData);
  },
  updatePost(postId, postData) {
    return apiClient.put(`/posts/${postId}`, postData);
  },
  deletePost(postId) {
    return apiClient.delete(`/posts/${postId}`);
  },
  likePost(postId) {
    return apiClient.patch(`/posts/${postId}/like`);
  },
  unlikePost(postId) {
    return apiClient.delete(`/posts/${postId}/like`);
  },
  followUser(userId) {
    return apiClient.patch(`/following/${userId}`);
  },
  unfollowUser(userId) {
    return apiClient.delete(`/following/${userId}`);
  }
};
