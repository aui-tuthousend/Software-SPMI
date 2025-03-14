import './bootstrap';
import { createApp } from "vue";
import app from './components/app.vue';
import router from "./router";
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import Button from "primevue/button"
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import ToastService from 'primevue/toastservice';
import { Form } from '@primevue/forms';
import Image from 'primevue/image';


const appVue = createApp(app);
appVue.use(router);
appVue.use(PrimeVue, {
    theme: {
        preset: Aura
    }
});
appVue.use(ToastService);
appVue.component(Form);
appVue.component('Button', Button);
appVue.component('Image', Image);
appVue.component('InputText', InputText);
appVue.component('Password', Password);
appVue.mount('#app');
