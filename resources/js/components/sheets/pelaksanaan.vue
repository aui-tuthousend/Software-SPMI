<script setup>
import { defineAsyncComponent, ref } from "vue";
import {debounce} from "lodash";

const Modal = defineAsyncComponent({
    loader: () => import('../sheets/modal.vue'),
});

const props = defineProps({
    data: Object,
    role: String,
});

const emit = defineEmits(['submit-data', 'update']);
const username = localStorage.getItem('name');
const formData = ref([]);

const save = (idIndikator, bukti, idP, idBuk) => {
  const newData = { idIndikator: idIndikator, bukti: bukti, idPelaksanaan: idP, userName: username };
  // const index = formData.value.findIndex(item => item.idIndikator === idIndikator);
  // if (index !== -1) {
  //   if (bukti !== ''){
  //     formData.value.splice(index, 1, newData);
  //     return;
  //   }
  //   if (bukti === '' && idBuk !== ''){
  //     formData.value.splice(index, 1, newData);
  //     return;
  //   }
  //   formData.value.splice(index, 1);
  // } else {
  //   formData.value.push(newData);
  // }
  //
  // if (formData.value.length > 0){
  //   emit('update', true);
  // } else {
  //   emit('update', false);
  // }

  console.log(formData.value)
  emit('submit-data', newData)
};

const submit = (idIndikator) => {
    const send = ref([])
    const index = formData.value.findIndex(item => item.idIndikator === idIndikator);
    send.value = { ...formData.value[index] };
    formData.value.splice(index, 1);

  console.log(formData.value)
  console.log(send.value)
};

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

  console.log(props.data);
const isUpdate = (idBP, update, komen) => {
  const index = props.data.findIndex(item => item.indicators === idBP);
  console.log(index);
}
</script>

<template>
    <br />
    <div class="table">
        <table class="Pelaksanaan">
            <thead>
            <tr>
                <th colspan="3"><h4 class="font-poppin">Penetapan</h4></th>
                <th colspan="2"><h4 class="font-poppin">Pelaksanaan</h4></th>
                <th rowspan="2" class="link">save</th>
            </tr>
            <tr>
                <th><div class="font-poppin th">Standar</div></th>
                <th><div class="font-poppin th">Indikator</div></th>
                <th><div class="font-poppin link">Target</div></th>
                <th><div class="xd">Komentar</div></th>
                <th><div class="font-poppin link">Link Bukti</div></th>
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

                    <td>
                        <div class="edited">
                        <p>Last edited by: {{data.editorPelaksanaan}}</p>
                        <textarea class="tb"
                            v-model="data.bukti"
                            @input="data.isUpdate = true"></textarea>
                        </div>
                    </td>
                    <td>
                        <button
                                v-if="data.idBukti !== ''"
                                class="pop"
                                @click="openPopup(data.idBukti, 'Pelaksanaan')">
                            Link
                        </button>
                    </td>
                    <td>
                      <button v-if="data.isUpdate" class="btnn" @click="save(data.id, data.bukti, data.idPelaksanaan, data.idBukti)">save</button>
                      <button v-else >save</button>
                    </td>
                </tr>
            </template>
            </tbody>
        </table>
    </div>

    <Modal
            v-if="popupTriggers"
            :togglePopup="togglePopup"
            :idBukti="selectedIndicator"
            :tipe="tipeLink"
            :role="role"
    />
</template>

<style scoped>
.table {
    overflow-x: auto;
    padding-right: 2%;
    padding-bottom: 2%;
}

.xd{
    width: 30rem;
}

.tb{
    height: 7rem;
}

.btnn{
  background: yellow;
}

.Pelaksanaan {
    width: 100vw;
    margin-top: 1rem;
}

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
