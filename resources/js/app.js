import './bootstrap';
import { createApp } from 'vue'
import store from './store';
import router from './router'
import App from './App.vue'
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import '@fortawesome/fontawesome-free/css/all.css'

const app = createApp(App)

app.use(store)
app.use(router)
app.use(ElementPlus)
app.mount('#app')

store.dispatch('setToken', localStorage.getItem('user-token'));
