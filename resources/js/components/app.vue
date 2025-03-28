<script setup>
import CustomMenubar from "./comp/CustomMenubar.vue";
import { useRoute } from "vue-router";

const route = useRoute();
</script>

<template>
  <div class="w-screen h-screen bg-gray-100 flex flex-col items-center px-[5%] overflow-x-hidden">
        <CustomMenubar v-if="route.meta.showMenubar !== false" />
        <RouterView v-slot="{ Component, route }">
            <component :is="Component" />
        </RouterView>
    </div>
</template>

<style>
</style>
