<script setup>
import { ref, computed, onMounted } from "vue";
import { useRouter } from "vue-router";
import { FilterMatchMode } from "@primevue/core";

const router = useRouter();
// Filters
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
    jurusan: { value: null, matchMode: FilterMatchMode.IN },
    tipe: { value: null, matchMode: FilterMatchMode.IN },
    periode: { value: null, matchMode: FilterMatchMode.IN },
});
// Selected Row and Fields
const selectedRow = ref(null);
const selectedMajor = ref(null); // Selected major
const periode = ref(null); // Selected period
const loading = ref(true); // Loading state (initially true)
const availableSheets = ref([]); // Stores available sheets fetched from the API

// Variabel for filter options
const jurusanOptions = computed(() =>
    Array.from(new Set(availableSheets.value.map((sheet) => sheet.jurusan)))
);
const tipeOptions = computed(() =>
    Array.from(new Set(availableSheets.value.map((sheet) => sheet.tipe)))
);
const periodeOptions = computed(() =>
    Array.from(new Set(availableSheets.value.map((sheet) => sheet.periode)))
);

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
    let data = availableSheets.value;

    // Apply global search filter
    if (filters.value.global.value) {
        const searchText = filters.value.global.value.toLowerCase();
        data = data.filter((row) => {
            return (
                row.jurusan.toLowerCase().includes(searchText) ||
                row.tipe.toLowerCase().includes(searchText) ||
                row.periode.toLowerCase().includes(searchText)
            );
        });
    }

    // Apply column-specific filters
    if (filters.value.jurusan.value && filters.value.jurusan.value.length > 0) {
        data = data.filter((row) =>
            filters.value.jurusan.value.includes(row.jurusan)
        );
    }
    if (filters.value.tipe.value && filters.value.tipe.value.length > 0) {
        data = data.filter((row) =>
            filters.value.tipe.value.includes(row.tipe)
        );
    }
    if (filters.value.periode.value && filters.value.periode.value.length > 0) {
        data = data.filter((row) =>
            filters.value.periode.value.includes(row.periode)
        );
    }

    return data;
});

// Clear Filter
const clearFilter = () => {
    filters.value.global.value = null;
    filters.value.jurusan.value = null;
    filters.value.tipe.value = null;
    filters.value.periode.value = null;
};

// Row Select Event
const onRowSelect = (event) => {
    selectedRow.value = event.data; // Set the selected row
    selectedMajor.value = event.data.jurusan; // Set the selected major
    periode.value = event.data.periode;
    console.log(selectedMajor.value);
    console.log(periode.value)

    // Directly navigate to the sheet route
    router.push({
        name: "Sheet",
        params: {
            jurusan: encodeURIComponent(selectedMajor.value),
            periode: encodeURIComponent(periode.value),
        },
    });
    console.log("Navigating to Sheet:", event.data);
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
    <main
        class="w-full min-h-screen flex flex-col items-center justify-center bg-white rounded-lg shadow-lg"
    >
        <!-- Your content here -->
        <div class="flex flex-col gap-4 w-full max-w-4xl">
            <div class="card">
                <!-- Loading State -->

                <!-- Table (only rendered after data is fetched) -->
                <div class="top-0 bg-white">
                    <DataTable
                        :value="filteredData"
                        v-model:selection="selectedRow"
                        v-model:filters="filters"
                        paginator
                        :rows="10"
                        datakey="id"
                        selectionMode="single"
                        filterDisplay="row"
                        :globalFilterFields="['jurusan', 'periode', 'tipe']"
                        tableStyle="min-width: 50rem"
                        @row-select="onRowSelect"
                    >
                        <template #header>
                            <div class="flex justify-between">
                                <div>
                                    <Button
                                        style="width: auto"
                                        type="button"
                                        icon="pi pi-filter-slash"
                                        label="Clear"
                                        outlined
                                        @click="clearFilter"
                                    />
                                </div>
                                <IconField>
                                    <InputIcon>
                                        <i class="pi pi-search" />
                                    </InputIcon>
                                    <InputText
                                        v-model="filters['global'].value"
                                        placeholder="Keyword Search"
                                    />
                                </IconField>
                            </div>
                        </template>
                        <template #empty> No customers found. </template>
                        <!-- Column Jurusan -->
                        <Column
                            field="jurusan"
                            header="Jurusan"
                            :showFilterMenu="false"
                            style="width: 25%"
                        >
                            <template #body="{ data }">
                                {{ data.jurusan }}
                            </template>
                            <template #filter="{ filterModel, filterCallback }">
                                <MultiSelect
                                    v-model="filterModel.value"
                                    @change="filterCallback()"
                                    :options="jurusanOptions"
                                    placeholder="Select Jurusan"
                                    style="min-width: 14rem"
                                />
                            </template>
                        </Column>

                        <!-- Column Tipe -->
                        <Column
                            field="tipe"
                            header="Tipe"
                            :showFilterMenu="false"
                            style="width: 25%"
                        >
                            <template #body="{ data }">
                                {{ data.tipe }}
                            </template>
                            <template #filter="{ filterModel, filterCallback }">
                                <MultiSelect
                                    v-model="filterModel.value"
                                    @change="filterCallback()"
                                    :options="tipeOptions"
                                    placeholder="Select Tipe"
                                    style="min-width: 14rem"
                                />
                            </template>
                        </Column>

                        <!-- Column Periode -->
                        <Column
                            field="periode"
                            header="Period"
                            :showFilterMenu="false"
                            style="width: 25%"
                        >
                            <template #body="{ data }">
                                {{ data.periode }}
                            </template>
                            <template #filter="{ filterModel, filterCallback }">
                                <MultiSelect
                                    v-model="filterModel.value"
                                    @change="filterCallback()"
                                    :options="periodeOptions"
                                    placeholder="Select Period"
                                    style="min-width: 14rem"
                                />
                            </template>
                        </Column>
                    </DataTable>
                </div>

                <!-- Display selected row data -->
                <div v-if="selectedRow" class="mt-4">
                    <h3>Selected Row:</h3>
                    <p><strong>Faculty:</strong> {{ selectedRow.faculty }}</p>
                    <p><strong>Major:</strong> {{ selectedRow.major }}</p>
                    <p><strong>Period:</strong> {{ periode }}</p>
                    <Button
                        v-if="role === 'SuperUser'"
                        label="Go"
                        class="p-button-success w-full"
                        @click="navigateToSuperUser"
                    />
                    <Button
                        v-else
                        label="Go"
                        class="p-button-success w-full"
                        @click="navigateToSheet"
                    />
                </div>
            </div>
        </div>
    </main>
</template>

<style scoped>
/* Add your custom styles here */
</style>
