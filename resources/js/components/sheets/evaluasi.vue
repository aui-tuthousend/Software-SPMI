<script setup>
import { defineAsyncComponent, ref } from "vue";
import {useConfirm, useToast} from "primevue";
import ConfirmPopup from 'primevue/confirmpopup';
const Modal = defineAsyncComponent({
    loader: () => import('../sheets/modal.vue'),
});
const props = defineProps({
    data: Object,
    role: String,
});
const emit = defineEmits(['submit-data', 'update']);

const confirm = useConfirm();
const toast = useToast();
const visible = ref(false);
const isEditing = ref(false);
const username = localStorage.getItem('name');

const adjusmentOptions = ref(['melampaui', 'mencapai', 'belum mencapai', 'menyimpang']);
const selectedAdjusment = ref('');

const saveEval =(data, event) => {
    if (!data.adjusment){
        toast.add({ severity: 'warn', summary: 'Error Saving', detail: 'Please Fill the Adjusment', life: 3000 });
        return;
    } else if (!data.evaluasi){
        toast.add({ severity: 'warn', summary: 'Error Saving', detail: 'Please Fill the Evaluasi', life: 3000 });
        return;
    }

    confirm.require({
        target: event.currentTarget,
        group: 'headless',
        message: 'Save your current process?',
        accept: () => {
            const newData = {
                idBuktiPelaksanaan: data.idBukti,
                komentarEvaluasi: data.evaluasi,
                adjusment: data.adjusment,
                idEvaluasi: data.idPelaksanaan,
                userName: username,
                idInd: data.id,
                indicator: data.indicator,
            };
            emit('submit-data', newData)
            isEditing.value = false
            // toast.add({severity:'info', summary:'Confirmed', detail:'You have accepted', life: 3000});
        },
        reject: () => {
            toast.add({severity:'error', summary:'Rejected', detail:'You have rejected', life: 3000});
        }
    });
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
                    class="h-[10rem] w-[20rem] flex items-center justify-center"
                >
                    <Textarea
                        v-model="indicator.indicator"
                        style="resize: none; height: 9rem"
                    />
                </div>
            </template>
        </Column>
        <Column field="indicators">
            <template #body="slotProps">
                <div
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
                <div
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
                <div
                    v-for="(indicator, index) in slotProps.data.indicators"
                    :key="index"
                    class="h-[10rem] flex items-center justify-center"
                >
                    <Button
                        v-if="indicator.idBukti"
                        @click="openPopup(indicator.idBukti, 'Pelaksanaan')"
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
                    class="h-[10rem] w-[20rem] flex items-center justify-center"
                >

                    <FloatLabel variant="on" >
                        <Textarea
                            v-tooltip.top="{ value: 'Input Evaluasi', showDelay: 500, hideDelay: 300 }"
                            :disabled="isEditing && !indicator.isUpdate"
                            v-model="indicator.evaluasi"
                            @input="indicator.isUpdate = true; isEditing = true"
                            style="resize: none; height: 9rem; width: 20rem"
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
                <div
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
                <div
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
</template>

<style scoped>

.custom-card {
    --p-card-shadow: 0px 3px 4px rgba(0, 0, 0, 0.4);
    //--p-card-body-padding: 10px;
    width: 100%; height: 9rem; overflow: hidden
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
