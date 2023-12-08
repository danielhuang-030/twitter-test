<template>
    <div class="login-container">
      <h1>Login</h1>
      <form @submit.prevent="login">
        <div class="form-group">
          <label for="email">Email</label>
          <input type="text" id="email" v-model="loginForm.email" required>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" v-model="loginForm.password" required>
        </div>

        <button type="submit">Login</button>
      </form>
    </div>
  </template>

  <script>
  import apiService from '../apiService';

  export default {
    data() {
      return {
        loginForm: {
          email: '',
          password: ''
        }
      };
    },
    methods: {
      async login() {
        try {
          const response = await apiService.login(this.loginForm);
          localStorage.setItem('user-token', response.data.data.token);
          localStorage.setItem('user-data', JSON.stringify(response.data.data.user));
          this.$router.push({ name: 'home' });
        } catch (error) {
          // console.log(error);
        }
      }
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
    margin-top: 30px; /* 增加與頂部的間距 */
    margin-bottom: 30px; /* 增加與底部的間距 */
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
