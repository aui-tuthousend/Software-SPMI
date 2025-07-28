import { createRouter, createWebHistory } from "vue-router";
import axios from "axios";
import Home from "./components/views/home.vue";
import Sheet from "./components/views/sheet.vue";
import SuperUser from "./components/views/superUser.vue";
import Importexcel from "./components/views/import.vue";
import Register from "./components/views/register.vue";
import Login from "./components/views/login.vue";
import NotFound from "./components/notFound.vue";
import HomeAdmin from "./components/views/homeAdmin.vue";


const getRole = async () => {
    return localStorage.getItem("role")
}

axios.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem("token");
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => Promise.reject(error)
);


const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: "/",
            name: "Home",
            component: Home,
            beforeEnter: async (to, from, next) => {
                const role = await getRole();
                if (role) {
                    if (role === "Admin") {
                        next("/admin/dashboard");
                    } else {
                        next();
                    }
                } else {
                    next("/login");
                }
            },
        },
        {
            path: "/sheet/:jurusan/:periode/:tipe",
            name: "Sheet",
            component: Sheet,
            beforeEnter: async (to, from, next) => {
                const role = await getRole();
                if (role) {
                    if (role === "Admin") {
                        next("/admin/dashboard");
                    } else {
                        next();
                    }
                } else {
                    next("/login");
                }
            },
        },
        {
            path: "/superUser/:jurusan/:periode/:tipe",
            name: "SuperUser",
            component: SuperUser,
            beforeEnter: async (to, from, next) => {
                const role = await getRole();
                if (role) {
                    if (role === "Evaluasi") {
                        next();
                    } else {
                        next("/");
                    }
                } else {
                    next("/login");
                }
            },
        },
        {
            path: "/import",
            component: Importexcel,
            beforeEnter: async (to, from, next) => {
                const role = await getRole();
                if (role) {
                    if (role === "Evaluasi") {
                        next();
                    } else {
                        next("/");
                    }
                } else {
                    next("/login");
                }
            },
        },
        {
            path: "/login",
            component: Login,
            beforeEnter: async (to, from, next) => {
                const role = await getRole();
                if (role) {
                    next("/");
                } else {
                    next();
                }
            },
            meta: {
                showMenubar: false,
            },
        },
        {
            path: "/register",
            component: Register,
            beforeEnter: async (to, from, next) => {
                const role = await getRole();
                if (role) {
                    next("/");
                } else {
                    next();
                }
            },
            meta: {
                showMenubar: false,
            },
        },
        {
            path: "/:pathMatch(.*)*",
            component: NotFound,
            meta: {
                showMenubar: false,
            },
        },
        {
            path: "/admin/dashboard",
            component: HomeAdmin,
            beforeEnter: async (to, from, next) => {
                const role = await getRole();
                if (role) {
                    if (role === "Admin") {
                        next();
                    } else {
                        next("/");
                    }
                } else {
                    next("/login");
                }
            },
        },
    ],
});

export default router;
