<script setup lang="ts">
import { ref } from 'vue';
import {useRoute} from "vue-router";
import Pelaksanaan from "../sheets/pelaksanaan.vue";
import Pengendalian from "../sheets/pengendalian.vue";
import Evaluasi from "../sheets/evaluasi.vue";
import {getUserName, getUserRole} from "../stores/commonStore.js";


const role = getUserRole();
const name = getUserName();
const route = useRoute();

const tipeSheet = ['pendidikan', 'penelitian', 'pengabdian'];
const currentSheet = ref<string>(tipeSheet[0]);

const periode = ref<string>(route.params.periode.toString());
const jurusan = ref<string>(route.params.jurusan.toString());

</script>

<template>
    <div class="max-h-full h-full w-full">
        <Toast />
        <Panel class="w-full overflow-x-hidden">

            <div class="pb-[3%]">
                <Pelaksanaan
                    v-if="role=== 'Pelaksanaan'"
                    :jurusan="jurusan"
                    :periode="periode"
                    :tipeSheet="currentSheet"
                    :role="role"
                    :username="name"
                />

                <Evaluasi
                    v-else-if="role=== 'Evaluasi'"
                    :jurusan="jurusan"
                    :periode="periode"
                    :tipeSheet="currentSheet"
                    :role="role"
                    :username="name"
                />

                <Pengendalian
                    v-else-if="role=== 'Pengendalian'"
                    :jurusan="jurusan"
                    :periode="periode"
                    :tipeSheet="currentSheet"
                    :role="role"
                    :username="name"
                />
                <peningkatan
                    v-else-if="role=== 'Peningkatan'"
                    :jurusan="jurusan"
                    :periode="periode"
                    :tipeSheet="currentSheet"
                />

            </div>
        </Panel>
    </div>
</template>

<style scoped>


button {
    width: 5rem;
    height: 1rem;
}



</style>
