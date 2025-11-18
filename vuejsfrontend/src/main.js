import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import axios from './axios';
import './assets/style.css'


const app = createApp(App);

app.config.globalProperties.$axios = axios; // globally accessible as this.$axios

app.use(router).mount('#app');
