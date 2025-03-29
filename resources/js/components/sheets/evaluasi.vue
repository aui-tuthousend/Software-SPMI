<script setup lang="ts">
import {defineAsyncComponent, toRefs, ref, watch} from "vue";
import {useToast} from "primevue";
import {useEvaluasi, submitEvaluasi, fetchEvaluasi} from '../stores/useEvaluasi'
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
const adjusmentOptions = ref(['melampaui', 'mencapai', 'belum mencapai', 'menyimpang']);

const sheetTypes = ['input', 'proses', 'output'];
const current = ref(sheetTypes[0]);

watch([current, tipeSheet], async ()=> {
    loading.value = true;
    await fetchEvaluasi(props.jurusan, props.periode, props.tipeSheet, current.value);
    loading.value = false;
}, {immediate: true})

const handleSubmitEvaluasi = async (data) => {
    if (!data.adjusment){
        toast.add({ severity: 'warn', summary: 'Error Saving', detail: 'Please Fill the Adjusment', life: 3000 });
        return;
    } else if (!data.komentarEvaluasi){
        toast.add({ severity: 'warn', summary: 'Error Saving', detail: 'Please Fill the Evaluasi', life: 3000 });
        return;
    } else {
        data.isUpdate = false;
        useEvaluasi.initial(data)
        useEvaluasi.setUserName(username);
        const response = await submitEvaluasi()

        if (response === 200){
            await fetchEvaluasi(props.jurusan, props.periode, props.tipeSheet, current.value);
            isEditing.value = false;
            toast.add({ severity: 'success', summary: 'Success Saving', detail: 'Evaluasi Saved', life: 3000 });
        }
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
                    <Column header="Link"/>
                    <Column header="Komentar Evaluasi"/>
                    <Column header="Adjusment"/>
                    <Column header="Link"/>
                </Row>
            </ColumnGroup>
            <Column field="standar" header="Standar" class="w-[5rem] h-[5rem]">
                <template #body="{ data }">
                    <span v-if="loading">
                        <Skeleton width="5rem" height="16px" />
                    </span>
                    <span
                        class="w-[5rem]"
                        v-else
                    >
                        {{ data.standar }}
                    </span>
                </template>
            </Column>
            <Column field="indicators" class="w-[20rem]">
                <template #body="slotProps">
                    <span v-if="loading">
                        <Skeleton width="20rem" height="16px" />
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
            <Column field="indicators" class="w-[3rem]">
                <template #body="slotProps">
                    <span v-if="loading">
                        <Skeleton width="3rem" height="16px" />
                    </span>
                    <div
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[3rem] flex items-center justify-center"
                    >
                        {{indicator.target}}
                    </div>
                </template>
            </Column>
            <Column field="indicators" class="w-[20rem]">
                <template #body="slotProps">
                    <span v-if="loading">
                        <Skeleton width="20rem" height="16px" />
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
            <Column field="indicators" class="w-[3rem]">
                <template #body="slotProps">
                    <span v-if="loading">
                        <Skeleton width="3rem" height="16px" />
                    </span>
                    <div
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[3rem] flex items-center justify-center"
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
            <Column field="indicators" class="w-[20rem]">
                <template #body="slotProps">
                    <span v-if="loading">
                        <Skeleton width="20rem" height="16px" />
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
                                :disabled="isEditing && !indicator.isUpdate || !indicator.idBuktiPelaksanaan"
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
            <Column field="indicators" class="w-[10rem]">
                <template #body="slotProps">
                    <span v-if="loading">
                        <Skeleton width="10rem" height="16px" />
                    </span>
                    <div
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[10rem] flex items-center justify-center"
                    >
                        <Select
                            style="width: 10rem"
                            :disabled="isEditing && !indicator.isUpdate || !indicator.idBuktiPelaksanaan"
                            @change="indicator.isUpdate = true; isEditing = true"
                            v-model="indicator.adjusment"
                            :options="adjusmentOptions"
                            checkmark :highlightOnSelect="false"
                        />
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
                        class="h-[10rem] w-[3rem] flex items-center justify-center"
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

            <Column field="indicators" class="w-[3rem]">
                <template #body="slotProps">
                    <span v-if="loading">
                        <Skeleton width="3rem" height="16px" />
                    </span>
                    <div
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[3rem] flex items-center justify-center"
                    >

                        <Button
                            v-if="indicator.isUpdate"
                            @click="handleSubmitEvaluasi(indicator)"
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
