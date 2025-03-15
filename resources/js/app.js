import './bootstrap';
import { createApp } from "vue";
import app from './components/app.vue';
import router from "./router";
import 'primeicons/primeicons.css';
import PrimeVue from 'primevue/config';
import ToastService from 'primevue/toastservice';
import Aura from '@primeuix/themes/aura';
import { Form } from '@primevue/forms';
import { Avatar, Menubar, Image, InputText, Password, Button } from "primevue";


const appVue = createApp(app);
appVue.use(router);
appVue.use(PrimeVue, {
    theme: {
        preset: Aura,
        options: {
            darkModeSelector: false,
        }
    }
});
appVue.use(ToastService);
appVue.component('Form', Form);
appVue.component('Avatar', Avatar);
appVue.component('Menubar', Menubar);
appVue.component('Button', Button);
appVue.component('Image', Image);
appVue.component('InputText', InputText);
appVue.component('Password', Password);
appVue.mount('#app');
