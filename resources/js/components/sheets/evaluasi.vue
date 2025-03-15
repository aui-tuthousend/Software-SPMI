<script setup>
import { defineAsyncComponent, ref } from "vue";
import CustomButton from "@/components/comp/custom-button.vue";
import {debounce} from "lodash";

const Modal = defineAsyncComponent({
    loader: () => import('../sheets/modal.vue'),
});


const props = defineProps({
    data: Object,
    role: String,
});

const emit = defineEmits(['submit-data', 'update']);

const dataEval = ref([]);
const username = localStorage.getItem('name');

const adjusmentOptions = ref(['melampaui', 'mencapai', 'belum mencapai', 'menyimpang']);
const selectedAdjusment = ref('');

const saveEval =(data) => {

    const newData = {
        idBuktiPelaksanaan: data.idBuktiPelaksanaan,
        komentarEvaluasi: data.komenEval,
        adjusment: data.adjusment,
        idEvaluasi: data.idE,
        userName: data.username,
        idInd: data.idInd,
        indicator: data.indicator,
    };
    // const index = dataEval.value.findIndex(item => item.idBuktiPelaksanaan === idBuktiPelaksanaan);
    // if (index !== -1) {
    //     if (komenEval !== '' || adjusment !== ''){
    //         dataEval.value.splice(index, 1, newData);
    //         return;
    //     }
    //     if (komenEval !== '' || adjusment !== '' || idBE !== ''){
    //         dataEval.value.splice(index, 1, newData);
    //         return;
    //     }
    //     dataEval.value.splice(index, 1);
    // } else {
    //     dataEval.value.push(newData);
    // }
    //
    // if (dataEval.value.length > 0){
    //     emit('update', true);
    // } else {
    //     emit('update', false);
    // }

    // console.log(dataEval.value);
    emit('submit-data', newData)
};

function submit(){
}

const popupTriggers = ref(false);
const selectedIndicator = ref(null);
const tipeLink = ref(null);

const togglePopup = () => {
    popupTriggers.value = !popupTriggers.value;
};

const openPopup = (indicator, tipe) => {
    selectedIndicator.value = indicator;
    tipeLink.value = tipe;
    togglePopup();
};
</script>

<template class="table gap-5">

    <DataTable
        :value="props.data"
        showGridlines
        tableStyle="min-width: 130vw"
        style="padding: 5px"
    >
        <ColumnGroup type="header">
            <Row>
                <Column header="Penetapan" :colspan="3" />
                <Column header="Pelaksanaan" :colspan="2" />
                <Column header="Evaluasi" :colspan="3" />
                <Column header="Save" :rowspan="2" />
            </Row>
            <Row>
                <Column header="Standar"/>
                <Column header="Indikator"/>
                <Column header="Target"/>
                <Column header="Komentar"/>
                <Column header="Link Bukti Pelaksanaan"/>
                <Column header="Komentar Evaluasi"/>
                <Column header="Adjusment"/>
                <Column header="Link Bukti Evaluasi"/>
            </Row>
        </ColumnGroup>
        <Column field="standar" />
        <Column field="indicators">
            <template #body="slotProps">
                <div
                    v-for="(indicator, index) in slotProps.data.indicators"
                    :key="index"
                    class="h-[7rem] flex items-center justify-center"
                >
                    <Textarea
                        v-model="indicator.indicator"
                        style="resize: none; height: 6rem"
                    />
                </div>
            </template>
        </Column>
        <Column field="indicators">
            <template #body="slotProps">
                <div
                    v-for="(indicator, index) in slotProps.data.indicators"
                    :key="index"
                    class="h-[7rem] flex items-center justify-center"
                >
                    {{indicator.target}}
                </div>
            </template>
        </Column>
        <Column field="indicators">
            <template #body="slotProps">
                <div
                    v-for="(indicator, index) in slotProps.data.indicators"
                    :key="index"
                    class="h-[7rem] max-w-[25rem] flex items-center justify-start"
                >
                    <Textarea
                        disabled fluid
                        v-model="indicator.bukti"
                        style="resize: none; width: 30rem; height: 6rem"
                    />
                </div>
            </template>
        </Column>
        <Column field="indicators">
            <template #body="slotProps">
                <div
                    v-for="(indicator, index) in slotProps.data.indicators"
                    :key="index"
                    class="h-[7rem] flex items-center justify-center"
                >
                    <Button
                        @click="openPopup(indicator.idBukti, 'Pelaksanaan')"
                        v-if="indicator.idBukti"
                        severity="contrast"
                        variant="outlined"
                        raised
                        class="pop"
                    >Link</Button>
                </div>
            </template>
        </Column>
        <Column field="indicators">
            <template #body="slotProps">
                <div
                    v-for="(indicator, index) in slotProps.data.indicators"
                    :key="index"
                    class="h-[7rem] flex items-center justify-center"
                >

                    <FloatLabel variant="on" >
                        <Textarea
                            v-model="indicator.evaluasi"
                            @input="indicator.isUpdate = true"
                            style="resize: none; height: 6rem; width: 20rem"
                        />
                        <label
                            for="on_label"
                            v-if="indicator.evaluasi"
                        >Last Edited by: {{indicator.editorEval}}</label>
                    </FloatLabel>

                </div>
            </template>
        </Column>
        <Column field="indicators">
            <template #body="slotProps">
                <div
                    v-for="(indicator, index) in slotProps.data.indicators"
                    :key="index"
                    class="h-[7rem] flex items-center justify-center"
                >
                    <Select
                        style="width: 10rem"
                        @change="indicator.isUpdate = true"
                        v-model="indicator.adjusment"
                        :options="adjusmentOptions"
                        checkmark :highlightOnSelect="false"
                    />
                </div>
            </template>
        </Column>
        <Column field="indicators">
            <template #body="slotProps">
                <div
                    v-for="(indicator, index) in slotProps.data.indicators"
                    :key="index"
                    class="h-[7rem] flex items-center justify-center"
                >
                    <Button
                        v-if="indicator.idBuktiEval !== ''"
                        @click="openPopup(indicator.idBuktiEval, 'Evaluasi')"
                        severity="contrast"
                        variant="outlined"
                        raised
                        class="pop"
                    >
                        Link
                    </Button>
                </div>
            </template>
        </Column>

        <Column field="indicators">
            <template #body="slotProps">
                <div
                    v-for="(indicator, index) in slotProps.data.indicators"
                    :key="index"
                    class="h-[7rem] flex items-center justify-center"
                >
<!--                    <Button>Link</Button>-->
                    <Button
                        v-if="indicator.isUpdate"
                        @click="saveEval(indicator)"
                        icon="pi pi-check" iconPos="right"
                        style="width: 4rem; height: 2rem;"
                        severity="info"
                    >save</Button>
                    <Button
                        v-else
                        disabled
                        icon="pi pi-check" iconPos="right"
                        style="width: 4rem; height: 2rem;"
                    >save</Button>
                </div>
            </template>
        </Column>


    </DataTable>
    <br />
<!--    <div class="table">-->
<!--        <table :class="props.role">-->
<!--            <thead>-->
<!--            <tr>-->
<!--                <th colspan="3"><h4 class="font-poppin">Penetapan</h4></th>-->
<!--                <th colspan="2"><h4 class="font-poppin">Pelaksanaan</h4></th>-->
<!--                <th colspan="5"><h4 class="font-poppin">Evaluasi</h4></th>-->
<!--                <th rowspan="2" class="link">save</th>-->
<!--            </tr>-->
<!--            <tr>-->
<!--                <th><div class="th">Standar</div></th>-->
<!--                <th><div class="th">Indikator</div></th>-->
<!--                <th>Target</th>-->
<!--                <th><div class="xd">Komentar</div></th>-->
<!--                <th><div class="font-poppin link">Link Bukti</div></th>-->
<!--                <th colspan="2"><div class="font-poppin th">Komentar Evaluasi</div></th>-->
<!--                <th colspan="2"><div class="font-poppin">Adjusment</div></th>-->
<!--                <th><div class="font-poppin link">Link Bukti Evaluasi</div></th>-->
<!--            </tr>-->
<!--            </thead>-->
<!--            <tbody>-->
<!--            <template v-for="(standar, index) in props.data" :key="index">-->
<!--                <tr>-->
<!--                    <td :rowspan="standar.indicators.length + 1">{{ standar.standar }}</td>-->
<!--                </tr>-->
<!--                <tr v-for="data in standar.indicators" :key="data.id">-->
<!--&lt;!&ndash;                    <td>{{ data.indicator }}</td>&ndash;&gt;-->
<!--                    <td>-->
<!--                        <textarea v-model="data.indicator" class="ta" ></textarea>-->
<!--                    </td>-->
<!--                    <td>{{ data.target }}</td>-->

<!--                    <td>-->
<!--                        <p>{{ data.bukti }}</p>-->
<!--                    </td>-->
<!--                    <td>-->
<!--                        <button-->
<!--                            v-if="data.idBukti !== ''"-->
<!--                            class="pop"-->
<!--                            @click="openPopup(data.idBukti, 'Pelaksanaan')">-->
<!--                            Link-->
<!--                        </button>-->
<!--                    </td>-->

<!--                        <td colspan="2">-->
<!--                            <div class="edited">-->
<!--                            <p>Last edited by: {{data.editorEval}}</p>-->
<!--                                <el-input-->
<!--                                    style="width: 240px"-->
<!--                                    :disabled="data.bukti === ''"-->
<!--                                    v-model="data.evaluasi"-->
<!--                                    @input="data.isUpdate = true"-->
<!--                                    :rows="2"-->
<!--                                    type="textarea"-->
<!--                                    placeholder="Please input"-->
<!--                                />-->
<!--                            </div>-->
<!--                        </td>-->
<!--                        <td colspan="2">-->
<!--                            <select-->
<!--                                v-model="data.adjusment"-->
<!--                                :disabled="data.bukti === ''"-->
<!--                                @change="data.isUpdate = true">-->
<!--                                <option value=""></option>-->
<!--                                <option v-for="a in adjusmentOptions" :key="a">{{ a }}</option>-->
<!--                            </select>-->
<!--                        </td>-->
<!--                        <td>-->
<!--                            <button-->
<!--                                v-if="data.idBuktiEval !== ''"-->
<!--                                class="pop"-->
<!--                                @click="openPopup(data.idBuktiEval, 'Evaluasi')"-->
<!--                            >-->
<!--                                Link-->
<!--                            </button>-->
<!--                        </td>-->
<!--                        <td>-->
<!--                            <button v-if="data.isUpdate" class="btnn" @click="saveEval(data.idBukti, data.evaluasi, data.adjusment, data.idPelaksanaan, data.id, data.indicator)">save</button>-->
<!--                            <button v-else >save</button>-->
<!--                        </td>-->
<!--                </tr>-->
<!--            </template>-->
<!--            </tbody>-->
<!--        </table>-->
<!--    </div>-->

    <Modal
        v-if="popupTriggers"
        :togglePopup="togglePopup"
        :idBukti="selectedIndicator"
        :tipe="tipeLink"
        :role="role"
    />
</template>

<style scoped>

.btnn{
    background: yellow;
}



.ta{
    height: 10rem;
}

.Evaluasi {
    width: 120vw;
    margin-top: 1rem;
}

.xd{
    width: 30rem;
}

textarea {
    width: 100%;
    box-sizing: border-box;
}

.pop {
    width: 80%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}


</style>
