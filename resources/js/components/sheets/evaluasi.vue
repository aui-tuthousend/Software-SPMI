<script setup lang="ts">
import {defineAsyncComponent, toRefs, ref, watch} from "vue";
import {useConfirm, useToast} from "primevue";
import ConfirmPopup from 'primevue/confirmpopup';
import {useEvaluasi, submitEvaluasi, fetchEvaluasi} from '../stores/useEvaluasi'
const Modal = defineAsyncComponent({
    loader: () => import('../sheets/modal.vue'),
});
const props = defineProps<{
    jurusan: string,
    periode: string,
    tipeSheet: string,
    tipe: string,
}>();

const { jurusan, periode, tipeSheet, tipe } = toRefs(props);

const role = localStorage.getItem("userRole");
const username = localStorage.getItem('name');
const confirm = useConfirm();
const toast = useToast();

const visible = ref(false);
const isEditing = ref(false);
const popupTriggers = ref(false);
const selectedIndicator = ref(null);
const tipeLink = ref(null);
const adjusmentOptions = ref(['melampaui', 'mencapai', 'belum mencapai', 'menyimpang']);

// onMounted(async ()=> {
//     console.log(useEvaluasi.list)
// })

watch([tipe, tipeSheet], async ()=> {
    await fetchEvaluasi(props.jurusan, props.periode, props.tipeSheet, props.tipe);
}, {immediate: true})

const saveEval = (data, event) => {
    if (!data.adjusment){
        toast.add({ severity: 'warn', summary: 'Error Saving', detail: 'Please Fill the Adjusment', life: 3000 });
        return;
    } else if (!data.komentarEvaluasi){
        toast.add({ severity: 'warn', summary: 'Error Saving', detail: 'Please Fill the Evaluasi', life: 3000 });
        return;
    }

    confirm.require({
        target: event.currentTarget,
        group: 'headless',
        message: 'Save your current process?',
        accept: () => {
            useEvaluasi.initial(data)
            submitEvaluasi()
            isEditing.value = false
            toast.add({severity:'info', summary:'Confirmed', detail:'You have accepted', life: 3000});
        },
        reject: () => {
            toast.add({severity:'error', summary:'Rejected', detail:'You have rejected', life: 3000});
        }
    });
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
    <ConfirmPopup group="headless">
        <template #container="{ message, acceptCallback, rejectCallback }">
            <div class="rounded p-4">
                <span>{{ message.message }}</span>
                <div class="flex items-center gap-2 mt-4">
                    <Button label="Save" @click="acceptCallback" size="small"></Button>
                    <Button label="Cancel" outlined @click="rejectCallback" severity="secondary" size="small" text></Button>
                </div>
            </div>
        </template>
    </ConfirmPopup>
    <DataTable
        :value="useEvaluasi.list"
        showGridlines
        tableStyle="min-width: 130vw"
        class="custom-table"
    >
        <ColumnGroup type="header">
            <Row>
                <Column header="Penetapan" :colspan="3" />
                <Column header="Pelaksanaan" :colspan="2" />
                <Column header="Evaluasi" :colspan="3" />
                <Column header="Save" :rowspan="2" />
            </Row>
            <Row>
                <Column header="Standar" style="width: 10px"/>
                <Column header="Indikator"/>
                <Column header="Target"/>
                <Column header="Komentar"/>
                <Column header="Link Bukti Pelaksanaan"/>
                <Column header="Komentar Evaluasi"/>
                <Column header="Adjusment"/>
                <Column header="Link Bukti Evaluasi"/>
            </Row>
        </ColumnGroup>
        <Column field="standar" header="Standar">
            <template #body="{ data }">
                <Skeleton v-if="useEvaluasi.loading" width="100px" height="16px" />
                <span
                    v-else
                >
                    {{ data.standar }}
                </span>
            </template>
        </Column>
        <Column field="indicators">
            <template #body="slotProps">
                <span v-if="useEvaluasi.loading">
                    <Skeleton width="150px" height="16px" />
                </span>
                <span
                    v-else
                    v-for="(indicator, index) in slotProps.data.indicators"
                    :key="index"
                    class="h-[10rem] w-[20rem] flex items-center justify-center"
                >
                    <Textarea
                        v-model="indicator.indicator"
                        style="resize: none; height: 9rem"
                    />
                </span>
            </template>
        </Column>
        <Column field="indicators">
            <template #body="slotProps">
                <span v-if="useEvaluasi.loading">
                    <Skeleton width="150px" height="16px" />
                </span>
                <div
                    v-else
                    v-for="(indicator, index) in slotProps.data.indicators"
                    :key="index"
                    class="h-[10rem] flex items-center justify-center"
                >
                    {{indicator.target}}
                </div>
            </template>
        </Column>
        <Column field="indicators">
            <template #body="slotProps">
                <span v-if="useEvaluasi.loading">
                    <Skeleton width="150px" height="16px" />
                </span>
                <div
                    v-else
                    v-for="(indicator, index) in slotProps.data.indicators"
                    :key="index"
                    class="h-[10rem] w-[20rem] flex items-center justify-start"
                >
                    <Card class="custom-card" >
                        <template #content>
                            <ScrollPanel style="width: 100%; height: 9rem">
                                <p>
                                    {{indicator.bukti}}
                                </p>
                            </ScrollPanel>
                        </template>
                    </Card>
                </div>
            </template>
        </Column>
        <Column field="indicators">
            <template #body="slotProps">
                <span v-if="useEvaluasi.loading">
                    <Skeleton width="150px" height="16px" />
                </span>
                <div
                    v-else
                    v-for="(indicator, index) in slotProps.data.indicators"
                    :key="index"
                    class="h-[10rem] flex items-center justify-center"
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
        <Column field="indicators">
            <template #body="slotProps">
                <span v-if="useEvaluasi.loading">
                    <Skeleton width="150px" height="16px" />
                </span>
                <div
                    v-else
                    v-for="(indicator, index) in slotProps.data.indicators"
                    :key="index"
                    class="h-[10rem] w-[20rem] flex items-center justify-center"
                >

                    <FloatLabel variant="on" >
                        <Textarea
                            v-tooltip.top="{ value: 'Input Evaluasi', showDelay: 500, hideDelay: 300 }"
                            :disabled="isEditing && !indicator.isUpdate"
                            v-model="indicator.komentarEvaluasi"
                            @input="indicator.isUpdate = true; isEditing = true"
                            style="resize: none; height: 9rem; width: 20rem"
                        />
                        <label
                            for="on_label"
                            v-if="indicator.komentarEvaluasi"
                        >Last Edited by: {{indicator.editorEval}}</label>
                    </FloatLabel>

                </div>
            </template>
        </Column>
        <Column field="indicators">
            <template #body="slotProps">
                <span v-if="useEvaluasi.loading">
                    <Skeleton width="150px" height="16px" />
                </span>
                <div
                    v-else
                    v-for="(indicator, index) in slotProps.data.indicators"
                    :key="index"
                    class="h-[10rem] flex items-center justify-center"
                >
                    <Select
                        style="width: 10rem"
                        :disabled="isEditing && !indicator.isUpdate"
                        @change="indicator.isUpdate = true; isEditing = true"
                        v-model="indicator.adjusment"
                        :options="adjusmentOptions"
                        checkmark :highlightOnSelect="false"
                    />
                </div>
            </template>
        </Column>
        <Column field="indicators">
            <template #body="slotProps">
                <span v-if="useEvaluasi.loading">
                    <Skeleton width="150px" height="16px" />
                </span>
                <div
                    v-else
                    v-for="(indicator, index) in slotProps.data.indicators"
                    :key="index"
                    class="h-[10rem] flex items-center justify-center"
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
                <span v-if="useEvaluasi.loading">
                    <Skeleton width="150px" height="16px" />
                </span>
                <div
                    v-else
                    v-for="(indicator, index) in slotProps.data.indicators"
                    :key="index"
                    class="h-[10rem] flex items-center justify-center"
                >

                    <Button
                        v-if="indicator.isUpdate"
                        @click="saveEval(indicator, $event)"
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

    <Dialog v-model:visible="visible" modal header="Edit Profile" :style="{ width: '25rem' }">
        <span class="text-surface-500 dark:text-surface-400 block mb-8">Update your information.</span>
        <div class="flex items-center gap-4 mb-4">
            <label for="username" class="font-semibold w-24">Username</label>
            <InputText id="username" class="flex-auto" autocomplete="off" />
        </div>
        <div class="flex items-center gap-4 mb-8">
            <label for="email" class="font-semibold w-24">Email</label>
            <InputText id="email" class="flex-auto" autocomplete="off" />
        </div>
        <div class="flex justify-end gap-2">
            <Button type="button" label="Cancel" severity="secondary" @click="visible = false"></Button>
            <Button type="button" label="Save" @click="visible = false"></Button>
        </div>
    </Dialog>
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

.custom-card {
    --p-card-shadow: 0px 3px 4px rgba(0, 0, 0, 0.4);
    //--p-card-body-padding: 10px;
    width: 100%; height: 9rem; overflow: hidden
}

.custom-table {
    --p-datatable-header-cell-background: rgba(202, 240, 248, 0.4);
    --p-datatable-header-cell-text-align: center;
    padding: 5px;
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
