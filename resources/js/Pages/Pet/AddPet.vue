<script setup>
import { ref, computed } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import Button from 'primevue/button';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BigCard from '@/Components/BigCard.vue';
import MultiSelect from 'primevue/multiselect';
import InputError from '@/Components/InputError.vue';
const props = defineProps({
    categories: Array,
    tags: Array,
    statuses: Array
});
const form = useForm({
    name: '',
    category: null,
    tags: [],
    status: null
});



const submitForm = () => {
    form.post(route('pets.store'), {
        onSuccess: () => {
            form.reset();
        }
    });
};
</script>

<template>
    <AuthenticatedLayout>
       <Head title="Add Pet" />
    <BigCard>

        <h2 class="text-2xl font-bold mb-4">Add New Pet</h2>
        <form @submit.prevent="submitForm">
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <InputText id="name" v-model="form.name" class="mt-1 block w-full" required />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>
            <div class="mb-4">
                <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                <Dropdown id="category" v-model="form.category" :options="categories" optionLabel="name" class="mt-1 block w-full" required />
                <InputError :message="form.errors.category" class="mt-2" />
            </div>
            <div class="mb-4">
                <label for="tags" class="block text-sm font-medium text-gray-700">Tags</label>
                <MultiSelect id="tags" v-model="form.tags" :options="tags" optionLabel="name" class="mt-1 block w-full" multiple />
                <InputError :message="form.errors.tags" class="mt-2" />
            </div>
            <div class="mb-4">
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <Dropdown id="status" v-model="form.status" :options="statuses" class="mt-1 block w-full" required />
                <InputError :message="form.errors.status" class="mt-2" />
            </div>
            <Button label="Submit" type="submit" class="mt-4" />
        </form>
    </BigCard>
</AuthenticatedLayout>
</template>

<style scoped>
/* Tailwind CSS is used for styling */
</style>
