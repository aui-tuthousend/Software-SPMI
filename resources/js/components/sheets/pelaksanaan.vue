<script setup lang="ts">
import {defineAsyncComponent, ref, toRefs, watch} from "vue";
import {usePelaksanaan, submitPelaksanaan, fetchPelaksanaan} from '../stores/usePelaksanaan.js'
import {useToast} from "primevue";
const Modal = defineAsyncComponent({
    loader: () => import('../sheets/modal.vue'),
});

const props = defineProps<{
    jurusan: string,
    periode: string,
    tipeSheet: string,
}>();
const { jurusan, periode, tipeSheet } = toRefs(props);
const role = localStorage.getItem("userRole");
const username = localStorage.getItem('name');
const toast = useToast();

const loading = ref(false);
const isEditing = ref(false);
const popupTriggers = ref(false);
const selectedIndicator = ref(null);
const tipeLink = ref(null);

const sheetTypes = ['input', 'proses', 'output'];
const current = ref(sheetTypes[0]);

watch([current, tipeSheet], async ()=> {
    loading.value = true;
    await fetchPelaksanaan(props.jurusan, props.periode, props.tipeSheet, current.value);
    loading.value = false;
}, {immediate: true})

const handleSumbitPelaksanaan = async (data) => {
    data.isUpdate = false;
    usePelaksanaan.initial(data)
    usePelaksanaan.setUserName(username)
    const response = await submitPelaksanaan()

    if (response === 200){
        await fetchPelaksanaan(props.jurusan, props.periode, props.tipeSheet, current.value);
        isEditing.value = false;
        toast.add({ severity: 'success', summary: 'Success Saving', detail: 'Evaluasi Saved', life: 3000 });
    }

};



const togglePopup = () => {
    popupTriggers.value = !popupTriggers.value;
};

const openPopup = (indicator, tipe) => {
    selectedIndicator.value = indicator;
    tipeLink.value = tipe;
    togglePopup();
};

</script>

<template>
    <div class="w-full h-full">
        <Toast />
        <div class="w-full card flex justify-center">
            <Stepper value="Input" class="p-stepper">
                <StepList>
                    <Step
                        value="Input"
                        :disabled="isEditing"
                        @click="current = 'input'"
                    />
                    <Step
                        value="Proses"
                        :disabled="isEditing"
                        @click="current = 'proses'"
                    />
                    <Step
                        value="Output"
                        :disabled="isEditing"
                        @click="current = 'output'"
                    />
                </StepList>
            </Stepper>
        </div>

        <DataTable
            :value="usePelaksanaan.list"
            showGridlines
            tableStyle="min-width: 100%"
            class="custom-table"
        >
            <ColumnGroup type="header">
                <Row>
                    <Column header="Penetapan" :colspan="3" />
                    <Column header="Pelaksanaan" :colspan="2" />
                    <Column header="Save" :rowspan="2" />
                </Row>
                <Row>
                    <Column header="Standar" style="width: 10px"/>
                    <Column header="Indikator"/>
                    <Column header="Target"/>
                    <Column header="Komentar"/>
                    <Column header="Link"/>
                </Row>
            </ColumnGroup>
            <Column field="standar" header="Standar" class="w-[10rem] h-[5rem]">
                <template #body="{ data }">
                    <span v-if="loading">
                        <Skeleton width="8rem" height="16px" />
                    </span>
                    <span
                        class="w-[10rem]"
                        v-else
                    >
                        {{ data.standar }}
                    </span>
                </template>
            </Column>
            <Column field="indicators" class="w-[20rem]">
                <template #body="slotProps">
                    <span v-if="loading">
                        <Skeleton width="100%" height="16px" />
                    </span>
                    <span
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[100%] flex items-center justify-center"
                    >
                        <Textarea
                            disabled
                            v-model="indicator.indicator"
                            style="resize: none; height: 9rem"
                        />
                    </span>
                </template>
            </Column>
            <Column field="indicators" class="w-[2rem]">
                <template #body="slotProps">
                    <span v-if="loading">
                        <Skeleton width="2rem" height="16px" />
                    </span>
                    <div
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[100%] flex items-center justify-center"
                    >
                        {{indicator.target}}
                    </div>
                </template>
            </Column>
            <Column field="indicators" class="w-[25rem]">
                <template #body="slotProps">
                    <span v-if="loading">
                        <Skeleton width="25rem" height="16px" />
                    </span>
                    <div
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[25rem] flex items-center justify-center"
                    >
                        <FloatLabel variant="on" >
                            <Textarea
                                v-tooltip.top="{ value: 'Input Pelaksanaan', showDelay: 500, hideDelay: 300 }"
                                :disabled="isEditing && !indicator.isUpdate"
                                v-model="indicator.komentarPelaksanaan"
                                @input="indicator.isUpdate = true; isEditing = true"
                                style="resize: none; height: 9rem; width: 25rem"
                            />
                            <label
                                for="on_label"
                                v-if="indicator.komentarPelaksanaan"
                            >Last Edited by: {{indicator.editorPelaksanaan}}</label>
                        </FloatLabel>
                    </div>
                </template>
            </Column>
            <Column field="indicators" class="w-[3rem]">
                <template #body="slotProps">
                    <span v-if="loading">
                        <Skeleton width="3rem" height="16px" />
                    </span>
                    <div
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[100%] flex items-center justify-center"
                    >
                        <Button
                            v-if="indicator.idBuktiPelaksanaan"
                            @click="openPopup(indicator.idBuktiPelaksanaan, 'Pelaksanaan')"
                            severity="contrast"
                            variant="outlined"
                            raised
                            class="pop"
                        >Link</Button>
                    </div>
                </template>
            </Column>

            <Column field="indicators" class="w-[2rem]">
                <template #body="slotProps">
                    <span v-if="loading">
                        <Skeleton width="3rem" height="16px" />
                    </span>
                    <div
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[100%] flex items-center justify-center"
                    >

                        <Button
                            v-if="indicator.isUpdate"
                            @click="handleSumbitPelaksanaan(indicator)"
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


<!--        <div class="table">-->
<!--            <table class="Pelaksanaan">-->
<!--                <thead>-->
<!--                <tr>-->
<!--                    <th colspan="3"><h4 class="font-poppin">Penetapan</h4></th>-->
<!--                    <th colspan="2"><h4 class="font-poppin">Pelaksanaan</h4></th>-->
<!--                    <th rowspan="2" class="link">save</th>-->
<!--                </tr>-->
<!--                <tr>-->
<!--                    <th><div class="font-poppin th">Standar</div></th>-->
<!--                    <th><div class="font-poppin th">Indikator</div></th>-->
<!--                    <th><div class="font-poppin link">Target</div></th>-->
<!--                    <th><div class="xd">Komentar</div></th>-->
<!--                    <th><div class="font-poppin link">Link Bukti</div></th>-->
<!--                </tr>-->
<!--                </thead>-->
<!--                <tbody>-->
<!--                <template v-for="(standar, index) in props.data" :key="index">-->
<!--                    <tr>-->
<!--                        <td :rowspan="standar.indicators.length + 1">{{ standar.standar }}</td>-->
<!--                    </tr>-->
<!--                    <tr v-for="data in standar.indicators" :key="data.id">-->
<!--                        <td>{{ data.indicator }}</td>-->
<!--                        <td>{{ data.target }}</td>-->

<!--                        <td>-->
<!--                            <div class="edited">-->
<!--                                <p>Last edited by: {{data.editorPelaksanaan}}</p>-->
<!--                                <textarea class="tb"-->
<!--                                          v-model="data.bukti"-->
<!--                                          @input="data.isUpdate = true"></textarea>-->
<!--                            </div>-->
<!--                        </td>-->
<!--                        <td>-->
<!--                            <button-->
<!--                                v-if="data.idBukti !== ''"-->
<!--                                class="pop"-->
<!--                                @click="openPopup(data.idBukti, 'Pelaksanaan')">-->
<!--                                Link-->
<!--                            </button>-->
<!--                        </td>-->
<!--                        <td>-->
<!--                            <button v-if="data.isUpdate" class="btnn" @click="save(data.id, data.bukti, data.idPelaksanaan, data.idBukti)">save</button>-->
<!--                            <button v-else >save</button>-->
<!--                        </td>-->
<!--                    </tr>-->
<!--                </template>-->
<!--                </tbody>-->
<!--            </table>-->
<!--        </div>-->


        <Modal
            v-if="popupTriggers"
            :togglePopup="togglePopup"
            :idBukti="selectedIndicator"
            :tipe="tipeLink"
            :role="role"
        />
    </div>

</template>

<style scoped>

textarea {
    width: 100%;
    height: 5rem;
    box-sizing: border-box;
}

.pop {
    width: 80%;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

</style>
