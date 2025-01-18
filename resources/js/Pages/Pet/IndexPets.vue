<script setup>
import { ref, onMounted } from 'vue';
import { Head, usePage, router } from '@inertiajs/vue3';
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import Button from 'primevue/button';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BigCard from '@/Components/BigCard.vue';
import NavLink from '@/Components/NavLink.vue';
const props = defineProps({
    pets: Array
});
const tagsTemplate = (rowData) => {
    return rowData.tags.map(tag => tag.name).join(', ');
};

const statuses = usePage().props.status;
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex gap-4 flex-wrap">
            <NavLink  v-for="status in statuses" 
            :active="route().current('pets.index',[status])" 
            :href="route('pets.index', [status])" class="inline-block">{{status}} pets</NavLink>
        </div>
        </template>

        <Head title="Pets" />
        <BigCard>
            <h2 class="text-2xl font-bold mb-4">Pets List</h2>
            <DataTable :value="pets" class="p-datatable-gridlines" paginator :rows="10" :rowsPerPageOptions="[5, 10, 20]">
                <Column field="name" header="Name" />
                <Column field="category.name" header="Category" />
                <Column header="Tags">
                    <template #body="slotProps">
                        {{ tagsTemplate(slotProps.data) }}
                    </template>
                </Column>
                <Column field="status" header="Status" />
                <Column header="Actions" body="actionTemplate">

                    <template #body="slotProps">
                        <Button label="Edit" @click.prevent="router.get(route('pets.edit',[slotProps.data.id]))" class="mr-2" />

                    </template>
                </Column>

            </DataTable>
        </BigCard>
    </AuthenticatedLayout>
</template>
