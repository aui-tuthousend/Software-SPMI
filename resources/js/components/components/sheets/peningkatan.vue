<script setup lang="ts">
import {ref, toRefs, watch} from "vue";
import ModalPeningkatan from "../sheets/modalPeningkatan.vue";
import Modal from "../sheets/modal.vue";
import ModalLink from "../modal/ModalLink.vue";
import {useToast} from "primevue";
import {fetchPeningkatan, submitPeningkatan, usePeningkatan} from "../../stores/usePeningkatan";

const props = defineProps<{
    jurusan: string,
    periode: string,
    tipeSheet: string,
    role: string,
    username: string,
}>();
const { jurusan, periode, tipeSheet, role, username } = toRefs(props);

const toast = useToast();
const loading = ref<boolean>(false);
const isEditing = ref<boolean>(false);
const popupTriggers = ref<boolean>(false);
const popupTriggers2 = ref<boolean>(false);
const selectedIndicator = ref<number>(0);
const tipeLink = ref<string>('');
const komentar = ref<string>('');
const oldVal = ref<string>('');
const count = ref<number>(0);

const sheetTypes = ['input', 'proses', 'output'];
const current = ref<string>(sheetTypes[0]);

watch([current, tipeSheet], async ()=> {
    loading.value = true;
    await fetchPeningkatan(props.jurusan, props.periode, props.tipeSheet, current.value);
    loading.value = false;
    count.value = 0;
    oldVal.value = '';
}, {immediate: true})

const handleSubmitPeningkatan = async (data) => {
    data.isUpdate = false;
    usePeningkatan.initial(data)
    usePeningkatan.setUserName(username.value)
    const response = await submitPeningkatan()

    if (response === 200){
        await fetchPeningkatan(props.jurusan, props.periode, props.tipeSheet, current.value);
        isEditing.value = false;
        toast.add({ severity: 'success', summary: 'Success Saving', detail: 'Evaluasi Saved', life: 3000 });
    }
};

const handleFocus = (old: string) => {
    count.value += 1;
    if (count.value == 1){
        oldVal.value = old;
    }
}

const isChanged = (data: any) => {
    data.isUpdate = true;
    isEditing.value = true;
    if (data.komenPeningkatan === oldVal.value){
        isEditing.value = false;
        data.isUpdate = false;
        count.value = 0;
    }
}

const openPopup = (indicator, tipe, komen) => {
    selectedIndicator.value = indicator;
    tipeLink.value = tipe;
    komentar.value = komen;
    togglePopup();
};

const dataPengendalian = ref<any>(null);
const openPopup2 = (indicator, t, am, rtl, prtl ) => {
    selectedIndicator.value = indicator;
    dataPengendalian.value = {
        temuan: t,
        akarMasala: am,
        rtl: rtl,
        pelakRtl: prtl,
    }
    togglePopup2();
};

const togglePopup2 = () => {
    popupTriggers2.value = !popupTriggers2.value;
};
const togglePopup = () => {
    popupTriggers.value = !popupTriggers.value;
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
            :value="usePeningkatan.list"
            showGridlines
            tableStyle="min-width: 110vw"
            class="custom-table"
        >
            <ColumnGroup type="header">
                <Row>

                    <Column header="Penetapan" :colspan="3" />
                    <Column header="Pelaksanaan" :rowspan="2" />
                    <Column header="Evaluasi" :rowspan="2" />
                    <Column header="Pengendalian" :rowspan="2" />
                    <Column header="Peningkatan" :colspan="2" />
                    <Column header="Save" :rowspan="2" />
                </Row>
                <Row>
                    <Column header="Standar"/>
                    <Column header="Indikator"/>
                    <Column header="Target"/>
                    <Column header="Komentar"/>
                    <Column header="Link"/>
                </Row>
            </ColumnGroup>
            <Column field="standar" header="Standar" class="min-w-[5rem] max-w-[5rem] h-[5rem]">
                <template #body="{ data }">
              <span v-if="loading">
                  <Skeleton width="100%" height="16px" />
              </span>
                    <span
                        class="w-[5rem]"
                        v-else
                    >
                  {{ data?.standar! }}
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
                        class="h-[10rem] w-[20rem] flex items-center justify-center"
                    >
                  <Textarea
                      disabled
                      class="custom-textarea"
                      v-model="indicator.indicator"
                      style="resize: none; height: 9rem; width: 100%;"
                  />
              </span>
                </template>
            </Column>
            <Column field="indicators" class="w-[3rem]">
                <template #body="slotProps">
              <span v-if="loading">
                  <Skeleton width="100%" height="16px" />
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
            <Column field="indicators" class="w-[5rem]">
                <template #body="slotProps">
              <span v-if="loading">
                  <Skeleton width="100%" height="16px" />
              </span>
                    <div
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[100%] flex items-center justify-start"
                    >
                        <Button
                            v-if="indicator.idBukti"
                            @click="openPopup(indicator.idBukti, 'Pelaksanaan', indicator.bukti)"
                            severity="contrast"
                            variant="outlined"
                            raised
                            class="min-w-[100%]"
                        >show</Button>
                    </div>
                </template>
            </Column>
            <Column field="indicators" class="w-[3rem]">
                <template #body="slotProps">
              <span v-if="loading">
                  <Skeleton width="100%" height="16px" />
              </span>
                    <div
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[100%] flex items-center justify-center"
                    >
                        <Button
                            v-if="indicator.idBuktiEvaluasi"
                            @click="openPopup(indicator.idBuktiEvaluasi, 'Evaluasi', indicator.evaluasi)"
                            severity="contrast"
                            variant="outlined"
                            raised
                            class="min-w-[100%]"
                        >show</Button>
                    </div>
                </template>
            </Column>
            <Column field="indicators" class="w-[3rem]">
                <template #body="slotProps">
              <span v-if="loading">
                  <Skeleton width="100%" height="16px" />
              </span>
                    <div
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[100%] flex items-center justify-center"
                    >
                        <Button
                            v-if="indicator.idBuktiPengendalian"
                            @click="openPopup2(indicator.idBuktiPengendalian, indicator.temuan, indicator.akar_masalah, indicator.rtl, indicator.pelaksanaan_rtl)"
                            severity="contrast"
                            variant="outlined"
                            raised
                            class="min-w-[100%]"
                        >show</Button>
                    </div>
                </template>
            </Column>

            <Column field="indicators" class="w-[20rem]">
                <template #body="slotProps">
              <span v-if="loading">
                  <Skeleton width="100%" height="16px" />
              </span>
                    <div
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[20rem] flex items-center justify-center"
                    >
                        <FloatLabel variant="on" >
                  <Textarea
                      v-tooltip.top="{ value: 'Input Peningkatan', showDelay: 500, hideDelay: 300 }"
                      :disabled="isEditing && !indicator.isUpdate || !indicator.idBuktiPengendalian"
                      v-model="indicator.komenPeningkatan"
                      @input="isChanged(indicator)"
                      @focus="handleFocus(indicator.komenPeningkatan)"
                      style="resize: none; height: 9rem; width: 20rem"
                  />
                            <label
                                for="on_label"
                                v-if="indicator.komenPeningkatan"
                            >Last Edited by: {{indicator.editorPeningkatan}}</label>
                        </FloatLabel>
                    </div>
                </template>
            </Column>

            <Column field="indicators" class="w-[3rem]">
                <template #body="slotProps">
              <span v-if="loading">
                  <Skeleton width="100%" height="16px" />
              </span>
                    <div
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[3rem] flex items-center justify-center"
                    >
                        <ModalLink
                            v-if="indicator.idPeningkatan"
                            :idBukti = indicator.idPeningkatan
                            :tipe="'Peningkatan'"
                            :role="role"
                        />
                    </div>
                </template>
            </Column>

            <Column field="indicators" class="w-[5rem]">
                <template #body="slotProps">
              <span v-if="loading">
                  <Skeleton width="100%" height="16px" />
              </span>
                    <div
                        v-else
                        v-for="(indicator, index) in slotProps.data.indicators"
                        :key="index"
                        class="h-[10rem] w-[5rem] flex items-center justify-center"
                    >
                        <ButtonGroup >
                            <Button
                                icon="pi pi-check"
                                severity="info"
                                :disabled="!indicator.isUpdate"
                                raised
                                @click="handleSubmitPeningkatan(indicator)"
                            />
                            <Button
                                icon="pi pi-times"
                                severity="danger"
                                :disabled="!indicator.isUpdate"
                                raised
                            />
                        </ButtonGroup>
                    </div>
                </template>
            </Column>
        </DataTable>
    </div>

    <Modal
        v-if="popupTriggers"
        :togglePopup="togglePopup"
        :idBukti="selectedIndicator"
        :tipe="tipeLink"
        :role=" 'Peningkatan' "
        :komentar="komentar"
    />

    <ModalPeningkatan
        v-if="popupTriggers2"
        :togglePopup2="togglePopup2"
        :idBukti="selectedIndicator"
        :tipe="'Pengendalian'"
        :data="dataPengendalian"
    />
</template>

<style scoped>


</style>
