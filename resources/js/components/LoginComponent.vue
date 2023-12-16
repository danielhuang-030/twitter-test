<template>
  <div class="login-container">
    <h1>Login</h1>
    <form @submit.prevent="login">
      <div class="form-group">
        <label for="email">Email</label>
        <input type="text" id="email" v-model="loginForm.email" required autocomplete="email">
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" v-model="loginForm.password" required autocomplete="current-password">
      </div>

      <button type="submit">Login</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useStore } from 'vuex';
import apiService from '../apiService';
import router from '../router';

const store = useStore();
const loginForm = ref({
  email: '',
  password: ''
});

const login = async () => {
  try {
    const response = await apiService.login(loginForm.value);
    store.dispatch('setToken', response.data.data.token);
    store.dispatch('setUserData', response.data.data.user);
    localStorage.setItem('user-token', response.data.data.token);
    router.push({ name: 'home' });
  } catch (error) {
    // console.error('Login error:', error);
  }
};
</script>

<style scoped>
.login-container {
  max-width: 400px;
  margin: 0 auto;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 4px;
  margin-top: 30px;
  margin-bottom: 30px;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  margin-bottom: 5px;
}

.form-group input {
  width: 100%;
  padding: 8px;
  border: 1px solid #ddd;
  border-radius: 4px;
}

button {
  width: 100%;
  padding: 10px;
  background-color: #1DA1F2;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

button:hover {
  background-color: #0d8bf2;
}
</style>
