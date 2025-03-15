import './bootstrap';
import { createApp } from "vue";
import app from './components/app.vue';
import router from "./router";
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import Button from "primevue/button"
import Dropdown from "primevue/dropdown";
import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import ToastService from 'primevue/toastservice';
import { Form } from '@primevue/forms';
import Image from 'primevue/image';
import {AutoComplete, Column, ColumnGroup, DataTable, FloatLabel, Row, Select, Textarea} from "primevue";


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
appVue.component('Button', Button);
appVue.component('Image', Image);
appVue.component('InputText', InputText);
appVue.component('AutoComplete', AutoComplete);
appVue.component('Select', Select);
appVue.component('Textarea', Textarea);
appVue.component('FloatLabel', FloatLabel);
appVue.component('Password', Password);
appVue.component('DataTable', DataTable)
appVue.component('Row', Row)
appVue.component('Column', Column)
appVue.component('ColumnGroup', ColumnGroup)
appVue.mount('#app');
