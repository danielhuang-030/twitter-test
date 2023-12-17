require('./bootstrap');

import { createApp } from 'vue'
import store from './store';
import router from './router'
import App from './App.vue'
import ElementPlus from 'element-plus'
import Echo from 'laravel-echo';
import 'element-plus/dist/index.css'
import '@fortawesome/fontawesome-free/css/all.css'

window.Pusher = require('pusher-js');
window.Echo = new Echo({
  broadcaster: 'pusher',
  key: process.env.MIX_PUSHER_APP_KEY,
  wsHost: window.location.hostname,
  wsPort: window.location.port,
  wssPort: window.location.port,
  wsPath: '/ws',
  forceTLS: false,
  encrypted: true,
  disableStats: true,
  enabledTransports: ['ws', 'wss']
});

window.Echo.connector.pusher.connection.bind('connected', () => {
  console.log('Connected to Soketi!');
});

window.Echo.connector.pusher.connection.bind('disconnected', () => {
  console.log('Disconnected from Soketi.');
});

const app = createApp(App)

app.use(store)
app.use(router)
app.use(ElementPlus)
app.mount('#app')

store.dispatch('setToken', localStorage.getItem('user-token'));
