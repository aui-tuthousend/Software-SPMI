<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

// Data
const sheetData = ref([
    { faculty: "F. Teknologi Industri", major: "Teknik Mesin", period: "2023" },
    { faculty: "F. Teknologi Industri", major: "Teknik Industri", period: "2023" },
    { faculty: "F. Teknologi Industri", major: "Teknik Kimia", period: "2022" },
    { faculty: "F. Teknik Sipil & Perencanaan", major: "Teknik Sipil", period: "2023" },
    { faculty: "F. Teknik Sipil & Perencanaan", major: "Arsitektur", period: "2022" },
    { faculty: "F. Teknik Elektro dan Teknologi Informasi", major: "Teknik Elektro", period: "2023" },
    { faculty: "F. Teknik Elektro dan Teknologi Informasi", major: "Teknik Informatika", period: "2022" },
    { faculty: "F. Teknik Elektro dan Teknologi Informasi", major: "Sistem Informasi", period: "2023" },
]);

// Filters
const filters = ref({
    global: { value: null, matchMode: "contains" },
});

// Selected Row and Fields
const selectedRow = ref(null);
const selectedMajor = ref(null); // Selected major
const periode = ref(null); // Selected period
const loading = ref(true); // Loading state (initially true)
const availableSheets = ref([]); // Stores available sheets fetched from the API

// Role (for conditional rendering)
const role = ref("User"); // Change this to "SuperUser" to test the SuperUser button

// Fetch data on component mount
onMounted(async () => {
    try {
        const response = await fetch("/api/getAllSheet");
        const data = await response.json();
        availableSheets.value = data; // Update available sheets with fetched data
    } catch (error) {
        console.error("Error fetching sheet data:", error);
    } finally {
        loading.value = false; // Set loading to false after fetch completes
    }
});

// Filtered Data
const filteredData = computed(() => {
    let data = sheetData.value;

    // Apply global search filter
    if (filters.value.global.value) {
        const searchText = filters.value.global.value.toLowerCase();
        data = data.filter((row) => {
            return (
                row.faculty.toLowerCase().includes(searchText) ||
                row.major.toLowerCase().includes(searchText) ||
                row.period.toLowerCase().includes(searchText)
            );
        });
    }

    // Filter rows to only show those with available sheets
    if (availableSheets.value.length > 0) {
        data = data.filter((row) =>
            availableSheets.value.some(
                (sheet) =>
                    sheet.jurusan === row.major && sheet.periode.includes(row.period)
            )
        );
    }

    return data;
});

// Clear Filter
const clearFilter = () => {
    filters.value.global.value = null;
};

// Row Select Event
const onRowSelect = (event) => {
    selectedRow.value = event.data; // Set the selected row
    selectedMajor.value = event.data.major; // Set the selected major

    // Find the matching sheet to get the correct periode
    const matchingSheet = availableSheets.value.find(
        (sheet) =>
            sheet.jurusan === event.data.major && sheet.periode.includes(event.data.period)
    );
    periode.value = matchingSheet ? matchingSheet.periode : null; // Set the selected period
    console.log("Selected Row:", event.data);
};

// Navigate to Sheet
const navigateToSheet = () => {
    if (selectedMajor.value && periode.value) {
        const faculty = sheetData.value.find(
            (row) => row.major === selectedMajor.value
        )?.faculty;

        if (faculty) {
            router.push({
                name: "Sheet",
                params: {
                    faculty: encodeURIComponent(faculty),
                    jurusan: encodeURIComponent(selectedMajor.value),
                    periode: encodeURIComponent(periode.value),
                },
            });
        } else {
            console.error("Faculty not found for the selected major.");
        }
    } else {
        console.error("No major or period selected.");
    }
};

// Navigate to SuperUser (example)
const navigateToSuperUser = () => {
    console.log("Navigating to SuperUser page...");
    // Add your SuperUser navigation logic here
};
</script>

<template>
    <main class="w-full min-h-screen flex flex-col items-center justify-center bg-white rounded-lg shadow-lg">
        <!-- Your content here -->
        <div class="flex flex-col gap-4 w-full max-w-4xl">
            <div class="card">
                <!-- Loading State -->
                <div v-if="loading" class="mt-4">
                    <p>Loading sheet data...</p>
                </div>

                <!-- Table (only rendered after data is fetched) -->
                <div v-else>
                    <DataTable :value="filteredData" v-model:selection="selectedRow" selectionMode="single"
                        removableSort filterDisplay="menu" tableStyle="min-width: 50rem" @row-select="onRowSelect">
                        <template #header>
                            <div class="flex justify-between">
                                <Button type="button" icon="pi pi-filter-slash" label="Clear" outlined
                                    @click="clearFilter" />
                                <IconField>
                                    <InputIcon>
                                        <i class="pi pi-search" />
                                    </InputIcon>
                                    <InputText v-model="filters['global'].value" placeholder="Keyword Search" />
                                </IconField>
                            </div>
                        </template>
                        <Column field="faculty" header="Faculty" sortable style="width: 25%"></Column>
                        <Column field="major" header="Major" sortable style="width: 25%"></Column>
                        <Column field="period" header="Period" sortable style="width: 25%"></Column>
                    </DataTable>

                    <!-- Display selected row data -->
                    <div v-if="selectedRow" class="mt-4">
                        <h3>Selected Row:</h3>
                        <p><strong>Faculty:</strong> {{ selectedRow.faculty }}</p>
                        <p><strong>Major:</strong> {{ selectedRow.major }}</p>
                        <p><strong>Period:</strong> {{ periode }}</p>
                        <Button v-if="role === 'SuperUser'" label="Go" class="p-button-success w-full"
                            @click="navigateToSuperUser" />
                        <Button v-else label="Go" class="p-button-success w-full" @click="navigateToSheet" />
                    </div>
                </div>
            </div>
        </div>
    </main>
</template>

<style scoped>
/* Add your custom styles here */
</style>