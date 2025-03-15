<script setup>
import { ref, watchEffect } from "vue";
import { dotStream } from 'ldrs'
import { Select } from "primevue";
import ProgressSpinner from 'primevue/progressspinner';
import { useRouter } from "vue-router";
dotStream.register()
const role = localStorage.getItem("userRole");

// const message = ref('upload new sheet')
// const jurusan = ref('')
// const periode = ref('')
const token = localStorage.getItem("token");
const faculties = [
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
// const selectedMajor = ref("");
// const showMajorError = ref(false);

// const validateMajorSelection = () => {
//     if (!jurusan.value) {
//         showMajorError.value = true;
//         selectedMajor.value = "";
//     } else {
//         showMajorError.value = false;
//     }
// };

// Reactive variables
const selectedFaculty = ref(null); // Selected faculty
const selectedMajor = ref(null); // Selected major
const majors = ref([]); // List of majors for the selected faculty
const loading = ref(false); // Loading state
// const per = ref([{ periode: "2023" }, { periode: "2024" }]); // Placeholder for period data
const per = ref([]); // Placeholder for period data
const periode = ref(null); // Selected period
// const role = ref("User"); // User role (change to "SuperUser" if needed)

// Initialize Vue Router
const router = useRouter();

// Function to update majors based on selected faculty
const updateMajors = () => {
    if (selectedFaculty.value) {
        // Set the majors for the selected faculty
        majors.value = selectedFaculty.value.majors;
    } else {
        // Clear majors if no faculty is selected
        majors.value = [];
    }
    // Reset selected major when faculty changes
    selectedMajor.value = null;

    // Simulate loading state (e.g., fetching data)
    loading.value = true;
    setTimeout(() => {
        // Simulate fetching data based on selected major
        if (selectedMajor.value) {
            per.value = [{ periode: "2023" }, { periode: "2024" }]; // Replace with actual data fetching logic
        } else {
            per.value = [];
        }
        loading.value = false;
    }, 1000); // Simulate a 1-second delay
};

// Function to navigate to SuperUser route
const navigateToSuperUser = () => {
    router.push({
        name: "SuperUser",
        params: { jurusan: selectedMajor.value, periode: periode.value?.periode }
    });
};

// Function to navigate to Sheet route
const navigateToSheet = () => {
    router.push({
        name: "Sheet",
        params: { jurusan: selectedMajor.value, periode: periode.value?.periode }
    });
};

// Function to handle navigation
const navigateToImport = () => {
    router.push("/import"); // Navigate to the "/import" route
};

watchEffect(async () => {
    loading.value = true;
    let response = await fetch(`/api/getPeriode/${selectedMajor.value}`, {});
    per.value = await response.json();
    loading.value = false;
})

</script>

<template>

    <main class="w-full min-h-screen flex flex-col items-center justify-center bg-white rounded-lg shadow-lg">
        <!-- Your content here -->
        <div class="flex flex-col gap-4 w-full max-w-4xl">
            <div class="flex items-center justify-between w-full">
                <h1 class="text-3xl font-bold">Software Penjamin Mutu Internal</h1>

                <!-- Centered + Button -->
                <div class="flex items-center justify-center">
                    <Button v-if="role === 'Evaluasi'" label="+" class="p-button-raised p-button-text"
                        title="Upload new sheet" @click="navigateToImport" />
                </div>
            </div>
        </div>
        <p v-if="token === null">Anda Belum <router-link to="/login">Login</router-link> 🫨</p>
        <div class="w-full max-w-4xl flex flex-col gap-4" v-else>
            <!-- Select for selecting faculty -->
            <div class="p-2 bg-white rounded-lg shadow-lg">
                <Select v-model="selectedFaculty" :options="faculties" optionLabel="name" placeholder="Pilih Fakultas"
                    class="pilihSheet w-full" :filter="true" :showClear="true" @change="updateMajors">
                    <template #value="slotProps">
                        <div v-if="slotProps.value" class="flex items-center">
                            <span class="text-lg font-semibold">{{ slotProps.value.name }}</span>
                        </div>
                        <span v-else class="text-lg font-semibold">
                            {{ slotProps.placeholder }}
                        </span>
                    </template>
                    <template #option="slotProps">
                        <div class="flex items-center">
                            <span class="text-lg font-semibold">{{ slotProps.option.name }}</span>
                        </div>
                    </template>
                </Select>
            </div>

            <!-- Select for selecting major -->
            <div class="p-2 bg-white rounded-lg shadow-lg">
                <Select v-model="selectedMajor" :options="majors" placeholder="Pilih Major" class="pilihSheet w-full"
                    :filter="true" :showClear="true" :disabled="!selectedFaculty">
                    <template #value="slotProps">
                        <div v-if="slotProps.value" class="flex items-center">
                            <span class="text-lg font-semibold">{{ slotProps.value }}</span>
                        </div>
                        <span v-else class="text-lg font-semibold">
                            {{ slotProps.placeholder }}
                        </span>
                    </template>
                    <template #option="slotProps">
                        <div class="flex items-center">
                            <span class="text-lg font-semibold">{{ slotProps.option }}</span>
                        </div>
                    </template>
                </Select>
            </div>

            <!-- Loading state -->
            <div v-if="loading" class="flex items-center justify-center p-4 bg-white rounded-lg shadow-lg">
                <ProgressSpinner style="width: 50px; height: 50px" strokeWidth="8" fill="transparent"
                    animationDuration=".5s" aria-label="Custom ProgressSpinner" class="animate-spin" />
            </div>

            <!-- Conditional rendering for empty data -->
            <div v-else-if="per.length === 0"
                class="text-center text-lg text-gray-500 p-4 bg-white rounded-lg shadow-lg">
                <p>Sheet dengan jurusan {{ selectedMajor }} belum ada</p>
            </div>

            <!-- Period selection and go button -->
            <div v-else class="periode p-2 bg-white rounded-lg shadow-lg">
                <h5 class="text-lg font-semibold mb-2">Pilih Periode:</h5>
                <Select v-model="periode" :options="per" optionLabel="periode" placeholder="Pilih Periode"
                    class="pilihSheet w-full" :filter="true" :showClear="true" required>
                    <template #value="slotProps">
                        <div v-if="slotProps.value" class="flex items-center">
                            <span class="text-lg font-semibold">{{ slotProps.value.periode }}</span>
                        </div>
                        <span v-else class="text-lg font-semibold">
                            {{ slotProps.placeholder }}
                        </span>
                    </template>
                    <template #option="slotProps">
                        <div class="flex items-center">
                            <span class="text-lg font-semibold">{{ slotProps.option.periode }}</span>
                        </div>
                    </template>
                </Select>

                <!-- Go button -->
                <div class="mt-4">
                    <Button v-if="role === 'SuperUser'" label="Go" class="p-button-success w-full"
                        @click="navigateToSuperUser" />
                    <Button v-else label="Go" class="p-button-success w-full" @click="navigateToSheet" />
                </div>
            </div>
        </div>
    </main>


    <!--   <router-link :to="{ name: 'Sheet', params: {idSheet: 27}}">Sheet</router-link>-->


    <!--  <router-link to="/upload">Upload</router-link>-->

</template>

<style scoped>
.bodi {
    width: 100vw;
    height: 100vh;
    padding: 3%;
}

.c1 {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.c1-1 {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.c2 {
    /* //display: flex; */
    margin-top: 3rem;
    gap: 1rem;
}

.custom-router-link {
    text-decoration: none;
    color: inherit;
}

.pilihSheet {
    width: 60%;
}

.periode {
    margin-top: 3rem;
    margin-bottom: 2rem;

    >select {
        width: 40%;
    }
}

.error-message {
    margin-top: 5px;
    color: red;
}
</style>
