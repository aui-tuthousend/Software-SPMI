<template>
    <div class="w-full pb-6">
        <Menubar :model="items" class="shadow-xl">
            <template #start>
                <div class="text-sky-500 text-2xl font-bold">LPMI</div>
            </template>
            <template #center="{ item, props }">
                <a v-ripple class="flex items-center justify-center" v-bind="props.action">
                    <span>{{ item.label }}</span>
                </a>
            </template>
            <template #end>
                <div class="flex items-center mr-2">
                    <Avatar
                        image="https://primefaces.org/cdn/primevue/images/avatar/amyelsner.png"
                        shape="circle"
                        @click="toggleMenu"
                        style="cursor: pointer;"
                    />
                    <TieredMenu ref="menu" id="overlay_tmenu" :model="profileMenu" popup />
                </div>
            </template>
        </Menubar>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
import { Avatar, Menubar, Toast, useToast } from "primevue";
import CryptoJS from "crypto-js";
import {getUserName, getUserRole} from "../stores/commonStore.js";
import axios from "axios";

axios.defaults.withCredentials = true;
const router = useRouter();
const user = getUserName();
const role = getUserRole();
const toast = useToast();

const loading = ref(false);
const menu = ref(null);

const toggleMenu = (e) => {
    menu.value.toggle(e);
};

const profileMenu = computed(() => [
    {
        label: `${user} (${role})`,
        icon: "pi pi-user",
        disabled: true,
    },
    {
        label: "Logout",
        icon: "pi pi-sign-out",
        command: logout,
    },
]);

const logout = async () => {
    try {
        const token = localStorage.getItem('token');
        await axios.post('/api/logout', {}, {
            headers: {
                Authorization: `Bearer ${token}`,
            }
        });

        localStorage.removeItem('token');
        localStorage.removeItem('name');
        localStorage.removeItem('userRole');
        localStorage.removeItem('role');
        localStorage.removeItem('date');

        await router.push('/login');

        toast.add({
            severity: "success",
            summary: "Logout berhasil.",
            life: 3000,
        });
    } catch (err) {
        console.error('Logout failed:', err);
    }
};

const items = computed(() => {
    const baseItems = [
        {
            label: "Home",
            icon: "pi pi-home",
            command: () => router.push("/"),
        },
    ];

    if (role === "Evaluasi") {
        baseItems.splice(1, 0, {
            label: "Upload",
            icon: "pi pi-cloud-upload",
            command: () => router.push("/import"),
        });
    }

    if (role === "Admin") {
        baseItems.splice(0, 1, {
            label: "User Management",
            icon: "pi pi-user-edit",
            command: () => router.push("/admin/dashboard"),
        });
    }

    return baseItems;
});
</script>

<style scoped>
.navbar {
    width: 100vw;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    padding: 15px;
    background: whitesmoke;
}
</style>
