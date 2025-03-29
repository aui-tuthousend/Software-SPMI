<script setup>
import {computed, onBeforeMount, ref, watch} from 'vue';
import {useRoute, useRouter} from "vue-router";
import Pelaksanaan from "@/components/sheets/pelaksanaan.vue";
import Pengendalian from "@/components/sheets/pengendalian.vue";
import data from "bootstrap/js/src/dom/data.js";
import Evaluasi from "@/components/sheets/evaluasi.vue";
import {useToast} from "primevue";

const toast = useToast();
const standarData = ref([]);
const loading = ref(false);

const tipe = ['input', 'proses', 'output'];
const current = ref(tipe[0]);
const role = localStorage.getItem("userRole");
const tipeSheet = ['pendidikan', 'penelitian', 'pengabdian'];
const currentSheet = ref(tipeSheet[0]);

const route = useRoute();
const routes = useRouter();
const update = ref(false);
const periode = ref(route.params.periode);
const jurusan = ref(route.params.jurusan);
const search = ref('');

let re = ref(0);

// if (role !== null){
//     watch([re, periode, jurusan, currentSheet, current], async () => {
//         loading.value = true;
//         let response = await fetch(`/api/getPenetapan/${jurusan.value}/${periode.value}/${currentSheet.value}/${current.value}`);
//         standarData.value = await response.json();
//         loading.value = false;
//         console.log(standarData.value);
//     }, { immediate: true });
// }


const submitData = (formData) => {

    const token = localStorage.getItem('token');
    axios.post(`/api/submit${role}`, { data: formData } ,{
        headers: {
            "Authorization": `Bearer ${token}`
        }
    })
        .then(response => {
            toast.add({ severity: 'success', summary: 'Success Saving', detail: 'Evaluasi Saved', life: 3000 });
            console.log('Data submitted successfully:', response.data);
            re.value++;
            update.value = false;
        })
        .catch(error => {
            console.error('Error submitting data:', error.response.data);
        });
};
const checkFormDataBeforeLeave = (to, from, next) => {
    if (update.value) {
        const answer = confirm('Perubahan anda belum disave. Apakah anda yakin ingin meninggalkan halaman ini?');
        if (answer) {
            update.value = false;
            next();
        } else {
            next(false);
        }
    } else {
        next();
    }
};

const filtered = computed(()=>{
    return standarData.value.filter(stand =>
        stand.standar.toLowerCase().includes(search.value.toLowerCase())
    )
});


onBeforeMount(() => {
    routes.beforeEach((to, from, next) => {
        checkFormDataBeforeLeave(to, from, next);
    });
});
</script>

<template>
    <div class="max-h-full h-full w-full">
        <Toast />
        <Panel class="w-full overflow-x-hidden">

            <div v-if="standarData === 'Null'">
                Belum ada data :)
            </div>

            <div v-else class="dt">
<!--                <input v-if="role !== null" v-model="search" placeholder="Search Standars">-->
                <Pelaksanaan
                        v-if="role=== 'Pelaksanaan'"
                        :jurusan="jurusan"
                        :periode="periode"
                        :tipeSheet="currentSheet"
                />

                <Evaluasi
                        v-else-if="role=== 'Evaluasi'"
                        :jurusan="jurusan"
                        :periode="periode"
                        :tipeSheet="currentSheet"
                />

                <pengendalian
                        v-else-if="role=== 'Pengendalian'"
                        :data="filtered"
                        :role="role"
                        @submit-data="submitData"
                        @update="(data) => update = data">
                </pengendalian>
                <peningkatan
                        v-else-if="role=== 'Peningkatan'"
                        :data="filtered"
                        @submit-data="submitData"
                        @update="(data) => update = data">
                </peningkatan>
            </div>
        </Panel>
    </div>
</template>

<style scoped>

.p-stepper {
    --p-stepper-separator-size: 2px;
    --p-stepper-step-number-border-radius: 3px;
    --p-stepper-step-number-border-color: none;
    --p-stepper-step-number-shadow: none;
    width: 80%;
    margin-top: -1rem;
}

button {
    width: 5rem;
    height: 1rem;
}



</style>
