<template>
  <div class="login-container">
    <h1>Login</h1>
    <form @submit.prevent="login">
      <div class="form-group">
        <label for="email">Email</label>
        <input type="text" id="email" v-model="formState.email" @blur="v$.email.$touch" autocomplete="email">
        <div v-if="v$.email.$error" class="error-message">
          <span v-for="error in v$.email.$errors" :key="error.$uid">{{ error.$message }}</span>
        </div>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" v-model="formState.password" @blur="v$.password.$touch" autocomplete="current-password">
        <div v-if="v$.password.$error" class="error-message">
          <span v-for="error in v$.password.$errors" :key="error.$uid">{{ error.$message }}</span>
        </div>
      </div>

      <button type="submit" :disabled="isSubmitting">{{ isSubmitting ? 'Logging in...' : 'Login' }}</button>
    </form>
  </div>
</template>

<script setup>
import useLoginForm from '../composables/useLoginForm';

const { formState, v$, login, isSubmitting } = useLoginForm();
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

.error-message {
  color: red;
  font-size: 0.8em;
  margin-top: 5px;
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

button:disabled {
  background-color: #a0d3f2;
  cursor: not-allowed;
}
</style>
