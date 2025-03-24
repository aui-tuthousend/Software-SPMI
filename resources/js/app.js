import "./bootstrap";
import { createApp } from "vue";
import app from "./components/app.vue";
import router from "./router";
import "primeicons/primeicons.css";
import PrimeVue from "primevue/config";
import ToastService from "primevue/toastservice";
import ConfirmationService from 'primevue/confirmationservice'
import DialogService from 'primevue/dialogservice'
import { Form } from "@primevue/forms";
import Aura from "@primeuix/themes/aura";
import {
    AutoComplete,
    Avatar,
    Button,
    Column,
    ColumnGroup,
    DataTable,
    Dialog,
    FloatLabel,
    IconField,
    Image,
    InputIcon,
    InputText,
    Menu,
    Menubar,
    MultiSelect,
    Password,
    ProgressSpinner,
    Ripple,
    Row,
    Select,
    Skeleton,
    Textarea,
    TieredMenu,
    Toolbar,
} from "primevue";

const appVue = createApp(app);
appVue.use(router);
appVue.use(PrimeVue, {
    theme: {
        preset: Aura,
        options: {
            darkModeSelector: false,
        },
        ripple: true,
    },
});
appVue.use(ToastService);
appVue.use(ConfirmationService);
appVue.use(DialogService);
appVue.component("Form", Form);
appVue.component("Avatar", Avatar);
appVue.component("Menubar", Menubar);
appVue.component("Button", Button);
appVue.component("Image", Image);
appVue.component("InputText", InputText);
appVue.component("InputIcon", InputIcon);
appVue.component("IconField", IconField);
appVue.component("AutoComplete", AutoComplete);
appVue.component("Select", Select);
appVue.component("Textarea", Textarea);
appVue.component("FloatLabel", FloatLabel);
appVue.component("Password", Password);
appVue.component("DataTable", DataTable);
appVue.component("Row", Row);
appVue.component("Column", Column);
appVue.component("ColumnGroup", ColumnGroup);
appVue.component("MultiSelect", MultiSelect);
appVue.component("ProgressSpinner", ProgressSpinner);
appVue.component("TieredMenu", TieredMenu);
appVue.component("Toolbar", Toolbar);
appVue.component("Skeleton", Skeleton);
appVue.component("Menu", Menu);
appVue.component("Dialog", Dialog);
appVue.directive("ripple", Ripple);
appVue.mount("#app");
