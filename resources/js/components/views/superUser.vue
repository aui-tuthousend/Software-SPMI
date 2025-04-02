<script setup>
import {computed, onBeforeMount, ref, watch} from 'vue';
import {useRoute, useRouter} from "vue-router";
import Pelaksanaan from "@/components/sheets/pelaksanaan.vue";
import CustomSelect from "@/components/comp/custom-select.vue";
import Pengendalian from "@/components/sheets/pengendalian.vue";
import data from "bootstrap/js/src/dom/data.js";
import Evaluasi from "@/components/sheets/evaluasi.vue";
import Peningkatan from "@/components/sheets/peningkatan.vue";
import ModalLink from "@/components/components/modal/ModalLink.vue";


const standarData = ref([]);
const loading = ref(false);

const tipe = ['input', 'proses', 'output'];
const current = ref(tipe[0]);

const roleUser = ['Pelaksanaan','Evaluasi', 'Pengendalian', 'Peningkatan'];
const role = ref(roleUser[0]);

const tipeSheet = ['pendidikan', 'penelitian', 'pengabdian'];
const currentSheet = ref(tipeSheet[0]);

const route = useRoute();
const routes = useRouter();
const update = ref(false);
const periode = ref(route.params.periode);
const jurusan = ref(route.params.jurusan);
const search = ref('');

let re = ref(0);

if (role !== null){
    watch([re, periode, jurusan, currentSheet, current], async () => {
        loading.value = true;
        let response = await fetch(`/api/getPenetapan/${jurusan.value}/${periode.value}/${currentSheet.value}/${current.value}`);
        standarData.value = await response.json();
        loading.value = false;
        // console.log(standarData.value);
    }, { immediate: true });
}

const submitData = (formData) => {

    const token = localStorage.getItem('token');
    axios.post(`/api/submit${role.value}`, { data: formData },{
      headers: {
        "Authorization": `Bearer ${token}`
      }
    })
        .then(response => {
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
    <div class="c1">
        <router-link class="pop" to="/">Home</router-link>

        <p>tipe:</p>
        <select v-model="currentSheet" class="tipe" required>
            <option v-for="t in tipeSheet" :key="t">{{ t }}</option>
        </select>

        <custom-select :data="roleUser" :wid="10" @response="(data) => role = data"/>

        <br><br>

        <template v-for="t in tipe">
            <input type="radio" :id="t" :value="t" v-model="current">
            <label :for="t" style="margin-right: 0.5rem;">{{ t }}</label>
        </template>

        <div v-if="standarData === 'Null'">
            Belum ada data :)
        </div>

        <div v-else class="dt">
            <br>
            <input v-if="role !== null" v-model="search" placeholder="Search Standars">

            <Pelaksanaan
                v-if="role=== 'Pelaksanaan'"
                :data="filtered"
                :role="role"
                @submit-data="submitData"
                @update="(data) => update = data">
            </Pelaksanaan>
            <evaluasi
                v-else-if="role=== 'Evaluasi'"
                :data="filtered"
                :role="role"
                @submit-data="submitData"
                @update="(data) => update = data">
            </evaluasi>
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
                :role="role"
                :saving="loading"
                @submit-data="submitData"
                @update="(data) => update = data">
            </peningkatan>
        </div>
    </div>
</template>

<style scoped>
.c1 {
    position: absolute;
    width: 100vw;
    padding: 3%;
}

button {
    width: 5rem;
    height: 1rem;
}

.dt{
    padding-bottom: 3%;
}

.pop {
    padding: 3px;
    height: 2rem;
}

</style>

<table class="tb">
<thead>
<tr>
    <th colspan="3"><h4 class="font-poppin">Penetapan</h4></th>
    <th rowspan="2"><h4 class="font-poppin">Pelaksanaan</h4></th>
    <th rowspan="2"><h4 class="font-poppin">Evaluasi</h4></th>
    <th rowspan="2"><h4 class="font-poppin">Pengendalian</h4></th>
    <th><h4 class="font-poppin" style="width: 27rem;">Peningkatan</h4></th>
    <th rowspan="2" class="link">Link Peningkatan</th>
    <th rowspan="2" class="link">save</th>
</tr>
<tr>
    <th><div class="th">Standar</div></th>
    <th><div class="th">Indicator</div></th>
    <th>Target</th>
    <th>Komentar</th>
</tr>
</thead>
<tbody>
<template v-for="(standar, index) in props.data" :key="index">
    <tr>
        <td :rowspan="standar.indicators.length + 1">{{ standar.standar }}</td>
    </tr>
    <tr v-for="data in standar.indicators" :key="data.id">
        <td>{{ data.indicator }}</td>
        <td>{{ data.target }}</td>
        <!--             Pelaksanaan            -->
        <td>
            <button
                v-if="data.idBukti !== ''"
                @click="openPopup(data.idBukti, 'Pelaksanaan', data.bukti)"
                :title="data.bukti">
                Link
            </button>
        </td>
        <!--             Evaluasi            -->
        <td>
            <button
                v-if="data.evaluasi !== ''"
                @click="openPopup(data.idBuktiEval, 'Evaluasi', data.evaluasi)"
                :title="data.evaluasi">
                Link
            </button>
        </td>
        <td>
            <button
                v-if="data.idBPengendalian !== ''"
                @click="openPopup2(data.idBPengendalian, data.temuan, data.akar_masalah, data.rtl, data.pelaksanaan_rtl)"
                :title="data.temuan">
                Link
            </button>
        </td>
        <td>
            <div class="edited">
                <p>Last edited by: {{data.editorPeningkatan}}</p>
                <textarea
                    :disabled="data.idBPengendalian === ''"
                    class="ta"
                    v-model="data.komenPeningkatan"
                    @input="data.isUpdate=true"
                ></textarea>
            </div>
        </td>
        <td>
            <ModalLink
                :idBukti = data.idPeningkatan
                :tipe="'Peningkatan'"
                :role="role"
            />
        </td>
        <td>
            <button v-if="data.isUpdate" class="btnn" @click="savePeningkatan(data.idBPengendalian, data.komenPeningkatan)">save</button>
            <button v-else >save</button>
            <p v-if="saving">Saving...</p>
        </td>
    </tr>
</template>
</tbody>
</table>

