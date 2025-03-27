import { createRouter, createWebHistory } from "vue-router";
import axios from "axios";
import Home from './components/homepage/home.vue';
import Sheet from './components/sheets/sheet.vue';
import SuperUser from './components/sheets/superUser.vue';
import Importexcel from './components/upload/import.vue';
import Register from './components/login/register.vue';
import Login from './components/login/login.vue';
import NotFound from './components/notFound.vue';
import HomeAdmin from "./components/admin/homeAdmin.vue";

const isAuthenticated = async () => {
    try {
        axios.defaults.withCredentials = true;
        await axios.get("/api/user", { withCredentials: true });
        return true;
    } catch (error) {
        return false;
    }
};

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            component: Home,
            beforeEnter: async (to, from, next) => {
                if (await isAuthenticated()) {
                    const role = localStorage.getItem("userRole");
                    if (role === "Admin") {
                        next('/admin/dashboard');
                    } else {
                        next();
                    }
                } else {
                    next('/login');
                }
            },
        },
        {
            path: '/sheet/:jurusan/:periode',
            name: 'Sheet',
            component: Sheet,
            beforeEnter: async (to, from, next) => {
                if (await isAuthenticated()) {
                    next();
                } else {
                    next('/login');
                }
            },
        },
        {
            path: '/superUser/:jurusan/:periode',
            name: 'SuperUser',
            component: SuperUser,
            beforeEnter: async (to, from, next) => {
                if (await isAuthenticated()) {
                    next();
                } else {
                    next('/login');
                }
            },
        },
        {
            path: '/import',
            component: Importexcel,
            beforeEnter: async (to, from, next) => {
                if (await isAuthenticated()) {
                    const role = localStorage.getItem("userRole");
                    if (role === "Evaluasi") {
                        next();
                    } else {
                        next('/');
                    }
                } else {
                    next('/login');
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
            meta: {
                showMenubar: false
            },
        },
        {
            path: '/admin/dashboard',
            component: HomeAdmin,
            beforeEnter: async (to, from, next) => {
                if (await isAuthenticated()) {
                    const role = localStorage.getItem("userRole");
                    console.log(role);
                    if (role === "Admin") {
                        next();
                    } else {
                        next('/');
                    }
                } else {
                    next('/login');
                }
            },
        },
    ],
});

export default router;
