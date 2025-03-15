<script setup>
import XlsxRead from "./XlsxRead.vue";
import XlsxTable from "./XlsxTable.vue";
import XlsxSheets from "./XlsxSheets.vue";
import { useRouter } from "vue-router";
import { ref } from "vue";
import axios from 'axios';
import { FileUpload, Select } from "primevue";

const router = useRouter();
const file = ref(null);
const selectedSheet = ref(null);
const department = ref("");
const selectedMajor = ref("");
const type = ref("");
const period = ref("");
const note = ref("");
const loading = ref(false);
const showMajorError = ref(false);

const departments = [
    {
        name: "F. Teknologi Industri",
        majors: ["Teknik Mesin", "Teknik Industri", "Teknik Kimia", "Teknik Pertambangan", "Teknik Perkapalan"]
    },
    {
        name: "F. Teknik Sipil & Perencanaan",
        majors: ["Teknik Sipil", "Arsitektur", "Teknik Lingkungan"]
    },
    {
        name: "F. Teknik Elektro dan Teknologi Informasi",
        majors: ["Teknik Elektro", "Teknik Informatika", "Sistem Informasi"]
    }
];

const validateMajorSelection = () => {
    if (!department.value) {
        showMajorError.value = true;
        selectedMajor.value = "";
    } else {
        showMajorError.value = false;
    }
};

const handleFileChange = (event) => {
    file.value = event.target.files ? event.target.files[0] : null;
};

const submitData = async () => {
    loading.value = true;
    const formData = new FormData();
    formData.append("file", file.value);
    formData.append("jurusan", selectedMajor.value);
    formData.append("tipe", type.value);
    formData.append("periode", period.value);
    formData.append("note", note.value);

    try {
        const token = localStorage.getItem('token');
        const response = await axios.post("api/penetapan/import", formData, {
            headers: {
                "Content-Type": "multipart/form-data",
                "Authorization": `Bearer ${token}`
            },
        });
        if (response.data.success) {
            alert(response.data.message);
            router.push('/');
        } else {
            alert("Error: " + response.data.message);
        }
    } catch (error) {
        console.error("Error mengirim file:", error);
    } finally {
        loading.value = false;
    }
};

function generateYearRange() {
    const currentYear = new Date().getFullYear();
    const years = [];
    for (let year = currentYear - 5; year <= currentYear; year++) {
        years.push(year);
    }
    return years;
}

const downloadFile = async () => {
    try {
        const response = await axios({
            url: '/api/downloadSheet',
            method: 'GET',
            responseType: 'blob',
        });

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'TemplatePenetapan.xlsx'); // Nama file saat diunduh
        document.body.appendChild(link);
        link.click();
        link.remove();
    } catch (error) {
        console.error('Download failed:', error.response.data);
    }
};
</script>

<template>
    <div class="c1">
        <div v-if="loading" class="loading-overlay">
            <l-dot-stream size="60" speed="2.5" color="black"></l-dot-stream>
        </div>
        <div class="container">
            <div class="upload-section">
                <!-- Download Template Button -->
                <Button label="Download Template"
                    class="p-button-outlined mb-4 px-6 py-3 rounded-lg border-2 border-blue-500 text-blue-500 hover:bg-blue-500 hover:text-white transition-all duration-300 ease-in-out"
                    @click="downloadFile" />

                <h2>Tambah/ Unggah Berkas</h2>

                <!-- Form -->
                <form @submit.prevent="submitData">
                    <!-- File Upload -->
                    <div class="form-group">
                        <label for="file-upload">Pilih berkas <span class="required">*</span></label>
                        <FileUpload mode="basic" name="file-upload" @change="handleChange" :auto="true"
                            @uploader="handleFileChange" chooseLabel="Pilih Berkas" class="w-full" required />
                    </div>

                    <!-- Department Select -->
                    <div class="form-group">
                        <label for="department">Fakultas <span class="required">*</span></label>
                        <Select v-model="department" :options="departments" optionLabel="name"
                            placeholder="Pilih Fakultas" class="w-full" :filter="true" :showClear="true"
                            @change="validateMajorSelection" required />
                    </div>

                    <!-- Major Select -->
                    <div class="form-group">
                        <label for="major">Jurusan <span class="required">*</span></label>
                        <Select v-model="selectedMajor"
                            :options="departments.find(d => d.name === department)?.majors || []"
                            placeholder="Pilih Jurusan" class="w-full" :filter="true" :showClear="true"
                            :disabled="!department" required />
                        <div v-if="showMajorError" class="error-message">
                            <span style="color: red;">Silakan pilih fakultas terlebih dahulu.</span>
                        </div>
                    </div>

                    <!-- Type Select -->
                    <div class="form-group">
                        <label for="type">Tipe <span class="required">*</span></label>
                        <Select v-model="type" :options="typeOptions" placeholder="Pilih Tipe" class="w-full"
                            :filter="true" :showClear="true" required />
                    </div>

                    <!-- Period Select -->
                    <div class="form-group">
                        <label for="period">Periode <span class="required">*</span></label>
                        <Select v-model="period" :options="generateYearRange()" placeholder="Pilih Periode"
                            class="w-full" :filter="true" :showClear="true" required />
                    </div>

                    <!-- Note Textarea -->
                    <div class="form-group">
                        <label for="note">Catatan <span class="required">*</span></label>
                        <Textarea v-model="note" rows="4" placeholder="Masukkan catatan jika ada" class="w-full" />
                    </div>

                    <!-- Submit Button -->
                    <Button type="submit" label="Kirim" class="p-button-primary mt-4" />
                </form>
            </div>
            <div class="preview-section">
                <h3>Pratinjau Berkas</h3>
                <xlsx-read :file="file">
                    <template #default="{ loading }">
                        <span v-if="loading">Memuat...</span>
                        <xlsx-sheets>
                            <template #default="{ sheets }">
                                <nav class="navbar">
                                    <ul class="navbar-nav">
                                        <li class="nav-item" v-for="sheet in sheets" :key="sheet">
                                            <a class="nav-link" :class="{
                                                active: selectedSheet === sheet,
                                            }" href="#" @click="selectedSheet = sheet" :style="{
                                                backgroundColor:
                                                    selectedSheet === sheet
                                                        ? '#94b6ff'
                                                        : '',
                                            }">{{ sheet }}</a>
                                        </li>
                                    </ul>
                                </nav>
                            </template>
                        </xlsx-sheets>
                        <div class="xlsx-table">
                            <xlsx-table :sheet="selectedSheet" />
                        </div>
                    </template>
                </xlsx-read>
            </div>
        </div>
    </div>
</template>

<style scoped>
.c1 {
    position: absolute;
    width: 100vw;
    padding: 3%;
}

.bt {
    width: 10rem;
    margin-bottom: 1rem;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: rgba(197,
            197,
            197,
            0.1);
    backdrop-filter: blur(2px);
    z-index: 999;
}

.container {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 20px;
}

.upload-section,
.preview-section {
    height: fit-content;
    width: 48%;
    margin: 0;
    padding: 20px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 5px;
}

input[type="file"],
input[type="text"],
select,
textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.required {
    color: red;
}

.navbar {
    position: sticky;
    top: 0;
    z-index: 1000;
    background-color: white;
}

.preview-section {
    overflow: hidden;
    max-height: 600px;
}

.xlsx-table {
    overflow-x: auto;
    max-height: 450px;
}

::-webkit-scrollbar {
    width: 5px;
}

/* Track */
::-webkit-scrollbar-track {
    box-shadow: inset 0 0 5px grey;
    border-radius: 10px;
}

/* Handle */
::-webkit-scrollbar-thumb {
    background: #94b6ff;
    border-radius: 50px;
}

/* Handle on hover */
::-webkit-scrollbar-thumb:hover {
    background: #94b6ff;
}

.error-message {
    margin-top: 5px;
    color: red;
}
</style>
