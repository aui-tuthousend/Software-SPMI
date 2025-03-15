<script setup>
import CustomMenubar from './comp/CustomMenubar.vue';
import { useRoute } from 'vue-router';

const route = useRoute();

</script>

<template>
    <div class="bg-gray-100 max-w-full">
    <CustomMenubar v-if="route.meta.showMenubar !== false" />
        <RouterView v-slot="{ Component, route }">
            <transition
                :enter-active-class="route.meta.enterClass"
                :leave-active-class="route.meta.leaveClass"
            >
                <component :is="Component"/>
            </transition>
        </RouterView>
    </div>
</template>

<style>
.h1{
    position: absolute;
    top: 2rem;
    left: 3rem;
}


</style>
