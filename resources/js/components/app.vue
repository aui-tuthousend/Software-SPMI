<script setup>
import CustomMenubar from "./comp/CustomMenubar.vue";
import { useRoute } from "vue-router";

const route = useRoute();
</script>

<template>
    <div class="container min-w-full bg-gray-100">
        <CustomMenubar v-if="route.meta.showMenubar !== false" />
        <RouterView v-slot="{ Component, route }">
            <component :is="Component" />
        </RouterView>
    </div>
</template>

<style>
</style>
