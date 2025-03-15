import { createRouter, createWebHistory } from "vue-router";
import Home from './components/homepage/home.vue';
import Sheet from './components/sheets/sheet.vue';
import SuperUser from './components/sheets/superUser.vue';
import Importexcel from './components/upload/import.vue';
import Register from './components/login/register.vue';
import Login from './components/login/login.vue';
import NotFound from './components/notFound.vue';
import HomeAdmin from "./components/admin/homeAdmin.vue";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            component: Home,
            beforeEnter: (to, from, next) => {
                const token = localStorage.getItem("token");
                if (token === null) {
                    next('/login');
                } else {
                    next();
                }
            },
        },
        {
            path: '/sheet/:jurusan/:periode',
            name: 'Sheet',
            component: Sheet,
            beforeEnter: (to, from, next) => {
                const token = localStorage.getItem("token");
                if (token === null) {
                    next('/login');
                } else {
                    next();
                }
            },
        },
        {
            path: '/superUser/:jurusan/:periode',
            name: 'SuperUser',
            component: SuperUser,
            beforeEnter: (to, from, next) => {
                const token = localStorage.getItem("token");
                if (token === null) {
                    next('/login');
                } else {
                    next();
                }
            },
        },
        {
            path: '/import',
            component: Importexcel,
            beforeEnter: (to, from, next) => {
                const token = localStorage.getItem("token");
                if (token === null) {
                    next('/login');
                } else {
                    next();
                }
            },
        },
        {
            path: '/login',
            component: Login,
            meta: {
                showMenubar: false
            },
        },
        {
            path: '/register',
            component: Register,
            meta: {
                showMenubar: false
            },
        },
        {
            path: '/:pathMatch(.*)*',
            component: NotFound,
        },
        {
            path: '/admin/dashboard',
            component: HomeAdmin,
            beforeEnter: (to, from, next) => {
                const token = localStorage.getItem("token");
                if (token === null) {
                    next('/login');
                } else {
                    next();
                }
            },
        },
    ],
})

export default router
