require('./bootstrap');

window.Echo.channel('new-user-following-uesr-2')
    .listen('UserFollowCreated', (e) => {
        console.log("UserFollowCreated");
        console.log(e);
    });

window.Echo.channel('new-user-unfollow-uesr-2')
    .listen('UserFollowDeleted', (e) => {
        console.log("UserFollowDeleted");
        console.log(e);
    });

import { createApp } from 'vue'
// import store from './store';
import router from './router'
import App from './App.vue'
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import '@fortawesome/fontawesome-free/css/all.css'

const app = createApp(App)

// app.use(store)
app.use(router)
app.use(ElementPlus)
app.mount('#app')
