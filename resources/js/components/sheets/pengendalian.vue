<script setup lang="ts">
import {defineAsyncComponent,ref, toRefs, watch} from "vue";
// import ModalLink from "@/components/modal/ModalLink.vue";
import {useToast} from "primevue";
import {fetchPengendalian, submitPengendalian, usePengendalian} from "../stores/usePengendalian";
const Modal = defineAsyncComponent({
    loader: () => import('../sheets/modal.vue'),
});
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
const selectedIndicator = ref<string>('');
const tipeLink = ref<string>('');
const komentar = ref<string>('');

const sheetTypes = ['input', 'proses', 'output'];
const current = ref<string>(sheetTypes[0]);

watch([current, tipeSheet], async ()=> {
  loading.value = true;
  await fetchPengendalian(props.jurusan, props.periode, props.tipeSheet, current.value);
  loading.value = false;
  // console.log(usePengendalian.list)
}, {immediate: true})

const handleSubmitPengendalian = async (data) => {
    if (!data.rtl){
        toast.add({ severity: 'warn', summary: 'Error Saving', detail: 'Please Fill the Adjusment', life: 3000 });
        return;
    } else if (!data.temuan){
        toast.add({ severity: 'warn', summary: 'Error Saving', detail: 'Please Fill the Evaluasi', life: 3000 });
        return;
    } else {
        data.isUpdate = false;
        usePengendalian.initial(data)
        usePengendalian.setUserName(username.value);
        const response = await submitPengendalian()

        if (response === 200){
            await fetchPengendalian(props.jurusan, props.periode, props.tipeSheet, current.value);
            isEditing.value = false;
            toast.add({ severity: 'success', summary: 'Success Saving', detail: 'Evaluasi Saved', life: 3000 });
        }
    }
};

const openPopup = (indicator, tipe, komen) => {
  selectedIndicator.value = indicator;
  tipeLink.value = tipe;
  komentar.value = komen;
  togglePopup();
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
          :value="usePengendalian.list"
          showGridlines
          tableStyle="min-width: 130vw"
          class="custom-table"
        >
          <ColumnGroup type="header">
            <Row>

              <Column header="Penetapan" :colspan="3" />
              <Column header="Pelaksanaan" :rowspan="2" />
              <Column header="Evaluasi" :rowspan="2" />
              <Column header="Pengendalian" :colspan="4" />
              <Column header="Link RTL" :rowspan="2" />
              <Column header="Save" :rowspan="2" />
            </Row>
            <Row>
              <Column header="Standar"/>
              <Column header="Indikator"/>
              <Column header="Target"/>
              <Column header="Temuan"/>
              <Column header="Akar Masalah"/>
              <Column header="RTL"/>
              <Column header="Pelaksanaan RTL"/>
            </Row>
          </ColumnGroup>
          <Column field="standar" header="Standar" class="min-w-[10rem] max-w-[10rem] h-[5rem]">
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
                  <Skeleton width="20rem" height="16px" />
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
          <Column field="indicators" class="w-[5rem]">
            <template #body="slotProps">
              <span v-if="loading">
                  <Skeleton width="5rem" height="16px" />
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
                  <Skeleton width="3rem" height="16px" />
              </span>
              <div
                  v-else
                  v-for="(indicator, index) in slotProps.data.indicators"
                  :key="index"
                  class="h-[10rem] w-[100%] flex items-center justify-center"
              >
                <Button
                    v-if="indicator.evaluasi"
                    @click="openPopup(indicator.idBuktiEvaluasiuasi, 'Evaluasi', indicator.evaluasi)"
                    severity="contrast"
                    variant="outlined"
                    raised
                >show</Button>
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
                      v-tooltip.top="{ value: 'Input Temuan', showDelay: 500, hideDelay: 300 }"
                      :disabled="isEditing && !indicator.isUpdate || !indicator.idBuktiEvaluasi"
                      v-model="indicator.temuan"
                      @input="indicator.isUpdate = true; isEditing = true"
                      style="resize: none; height: 9rem; width: 20rem"
                  />
                  <label
                      for="on_label"
                      v-if="indicator.temuan"
                  >Last Edited by: {{indicator.editorPengendali}}</label>
                </FloatLabel>
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
                      v-tooltip.top="{ value: 'Input Akar Masalah', showDelay: 500, hideDelay: 300 }"
                      :disabled="isEditing && !indicator.isUpdate || !indicator.idBuktiEvaluasi"
                      v-model="indicator.akarMasalah"
                      @input="indicator.isUpdate = true; isEditing = true"
                      style="resize: none; height: 9rem; width: 20rem"
                  />
                  <label
                      for="on_label"
                      v-if="indicator.akarMasalah"
                  >Last Edited by: {{indicator.editorPengendali}}</label>
                </FloatLabel>
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
                      v-tooltip.top="{ value: 'Input RTL', showDelay: 500, hideDelay: 300 }"
                      :disabled="isEditing && !indicator.isUpdate || !indicator.idBuktiEvaluasi"
                      v-model="indicator.rtl"
                      @input="indicator.isUpdate = true; isEditing = true"
                      style="resize: none; height: 9rem; width: 20rem"
                  />
                  <label
                      for="on_label"
                      v-if="indicator.rtl"
                  >Last Edited by: {{indicator.editorPengendali}}</label>
                </FloatLabel>
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
                      v-tooltip.top="{ value: 'Input RTL', showDelay: 500, hideDelay: 300 }"
                      :disabled="isEditing && !indicator.isUpdate || !indicator.idBuktiEvaluasi"
                      v-model="indicator.pelaksanaanRtl"
                      @input="indicator.isUpdate = true; isEditing = true"
                      style="resize: none; height: 9rem; width: 20rem"
                  />
                  <label
                      for="on_label"
                      v-if="indicator.pelaksanaanRtl"
                  >Last Edited by: {{indicator.editorPengendali}}</label>
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
                  class="h-[10rem] w-[3rem] flex items-center justify-center"
              >
                <Button
                    v-if="indicator.idBuktiEvaluasi"
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
                    @click="handleSubmitPengendalian(indicator)"
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

<!--        <table class="tb">-->
<!--        <thead>-->
<!--        <tr>-->
<!--            <th colspan="3"><h4 class="font-poppin">Penetapan</h4></th>-->
<!--            <th rowspan="2"><h4 class="font-poppin">Pelaksanaan</h4></th>-->
<!--            <th rowspan="2"><h4 class="font-poppin">Evaluasi</h4></th>-->
<!--            <th colspan="4"><h4 class="font-poppin">Pengendalian</h4></th>-->
<!--            <th rowspan="2" class="link">Link Bukti Pelaksanaan RTL</th>-->
<!--            <th rowspan="2" class="link">save</th>-->
<!--        </tr>-->
<!--        <tr>-->
<!--            <th><div class="th">Standar</div></th>-->
<!--            <th><div class="th">Indicator</div></th>-->
<!--            <th>Target</th>-->
<!--            <th>Temuan</th>-->
<!--            <th>Akar Masalah</th>-->
<!--            <th>RTL</th>-->
<!--            <th>Pelaksanaan RTL</th>-->
<!--        </tr>-->
<!--        </thead>-->
<!--        <tbody>-->
<!--        <template v-for="(standar, index) in props.data" :key="index">-->
<!--            <tr>-->
<!--                <td :rowspan="standar.indicators.length + 1">{{ standar.standar }}</td>-->
<!--            </tr>-->
<!--            <tr v-for="data in standar.indicators" :key="data.id">-->
<!--                <td>{{ data.indicator }}</td>-->
<!--                <td>{{ data.target }}</td>-->
<!--&lt;!&ndash;             Pelaksanaan            &ndash;&gt;-->
<!--                <td>-->
<!--                    <button-->
<!--                    v-if="data.idBukti !== ''"-->
<!--                    @click="openPopup(data.idBukti, 'Pelaksanaan', data.bukti)"-->
<!--                    :title="data.bukti">-->
<!--                        Link-->
<!--                    </button>-->
<!--                </td>-->
<!--&lt;!&ndash;             Evaluasi            &ndash;&gt;-->
<!--                <td>-->
<!--                    <button-->
<!--                    v-if="data.evaluasi !== ''"-->
<!--                    @click="openPopup(data.idBuktiEvaluasi, 'Evaluasi', data.evaluasi)"-->
<!--                    :title="data.evaluasi">-->
<!--                        Link-->
<!--                    </button>-->
<!--                </td>-->
<!--                    <td>-->
<!--                        <div class="edited">-->
<!--                            <p>Last edited by: {{data.editorPengendali}}</p>-->
<!--                            <textarea-->
<!--                                :disabled="data.idBuktiEvaluasi === ''"-->
<!--                                class="ta"-->
<!--                                v-model="data.temuan"-->
<!--                                @input="data.isUpdate = true"-->
<!--                            ></textarea>-->
<!--                        </div>-->
<!--                    </td>-->
<!--                    <td>-->
<!--                        <textarea-->
<!--                            :disabled="data.idBuktiEvaluasi === ''"-->
<!--                            class="ta"-->
<!--                            v-model="data.akar_masalah"-->
<!--                            @input="data.isUpdate = true"-->
<!--                        ></textarea>-->
<!--                    </td>-->
<!--                    <td>-->
<!--                        <textarea-->
<!--                            :disabled="data.idBuktiEvaluasi === ''"-->
<!--                            class="ta"-->
<!--                            v-model="data.rtl"-->
<!--                            @input="data.isUpdate = true"-->
<!--                        ></textarea>-->
<!--                    </td>-->
<!--                    <td>-->
<!--                        <textarea-->
<!--                            :disabled="data.idBuktiEvaluasi === ''"-->
<!--                            class="ta"-->
<!--                            v-model="data.pelaksanaan_rtl"-->
<!--                            @input="data.isUpdate = true"-->
<!--                        ></textarea>-->
<!--                    </td>-->
<!--                    <td>-->
<!--&lt;!&ndash;                        use teleport !!&ndash;&gt;-->
<!--                        <ModalLink-->
<!--                            :idBukti = data.idBPengendalian-->
<!--                            :tipe="'Pengendalian'"-->
<!--                            :role="role"-->
<!--                        />-->
<!--                    </td>-->
<!--                    <td>-->
<!--                        <button v-if="data.isUpdate" class="btnn" @click="savePengendalian(data.idBuktiEvaluasi, data.temuan, data.akar_masalah, data.rtl, data.pelaksanaan_rtl)">save</button>-->
<!--                        <button v-else >save</button>-->
<!--                    </td>-->
<!--            </tr>-->
<!--        </template>-->
<!--        </tbody>-->
<!--    </table>-->
    </div>
    <Modal
        v-if="popupTriggers"
        :togglePopup="togglePopup"
        :idBukti="selectedIndicator"
        :tipe="tipeLink"
        :role=" 'Pengendalian' "
        :komentar="komentar"
    />
</template>

<style scoped>

</style>
