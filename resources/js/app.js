import './bootstrap';
import { createApp } from "vue";
import ElementPlus from 'element-plus'
import 'element-plus/dist/index.css'
import app from './components/app.vue';
import router from "./router";
import 'bootstrap/dist/css/bootstrap.css';
import bootsrap from 'bootstrap/dist/js/bootstrap.bundle.js';


createApp(app).use(router).use(bootsrap).use(ElementPlus).mount('#app');
