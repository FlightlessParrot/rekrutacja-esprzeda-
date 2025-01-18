<script setup>
import { ref, computed, onMounted } from 'vue';
import { useForm, Head, usePage, router } from '@inertiajs/vue3';
import InputText from 'primevue/inputtext';
import Dropdown from 'primevue/dropdown';
import Button from 'primevue/button';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import BigCard from '@/Components/BigCard.vue';
import MultiSelect from 'primevue/multiselect';
import InputError from '@/Components/InputError.vue';
import FileUpload from 'primevue/fileupload';
import Divider from 'primevue/divider';

const props = defineProps({
    pet: Object,
    categories: Array,
    tags: Array,
});

const form = useForm({
    name: props.pet.name,
    category: props.pet.category,
    tags: props.pet.tags,
    status: props.pet.status
});
const fileForm = useForm({
    image: null
})
const statuses = usePage().props.status;
const submitForm = () => {
    form.patch(route('pets.update', props.pet.id), {
        onSuccess: () => {
            form.reset();
        }
    });
};
const fileupload = ref();
const handleImageUpload = () => {
    console.log('I work')
    console.log(fileupload.value.files[0])
    fileForm.image = fileupload.value.files[0];
    fileForm.post(route('pets.file.upload', [props.pet.id]))
}
</script>

<template>
    <AuthenticatedLayout>

        <Head title="Edit Pet" />
        <BigCard>

            <h2 class="text-2xl font-bold mb-4">Edit Pet</h2>
            <form @submit.prevent="submitForm">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <InputText id="name" v-model="form.name" class="mt-1 block w-full" required />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>
                <div class="mb-4">
                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                    <Dropdown id="category" v-model="form.category" :options="categories" optionLabel="name"
                        class="mt-1 block w-full" required />
                    <InputError :message="form.errors.category" class="mt-2" />
                </div>
                <div class="mb-4">
                    <label for="tags" class="block text-sm font-medium text-gray-700">Tags</label>
                    <MultiSelect id="tags" v-model="form.tags" :options="tags" optionLabel="name"
                        class="mt-1 block w-full" multiple />
                    <InputError :message="form.errors.tags" class="mt-2" />
                </div>
                <div class="mb-4">
                    <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                    <Dropdown id="status" v-model="form.status" :options="statuses" class="mt-1 block w-full"
                        required />
                    <InputError :message="form.errors.status" class="mt-2" />
                </div>
                <Button label="Submit" type="submit" class="mt-4" />
            </form>
            <Divider />
            <h3 class="text-lg font-bold">Send pet photo</h3>
            <form v-on:submit.prevent="handleImageUpload" enctype="multipart/form-data">
                <div class="mb-4">
                    <FileUpload ref="fileupload"  :maxFileSize="1000000" id="image" class="mt-1" mode="basic" 
                        name="image[]" accept="image/*" required />
                    <InputError :message="fileForm.errors.image" class="mt-2" />
                </div>
                <Button label="Upload Image" type="submit" class="mt-4" />
                <Divider />
                <h3 class="text-lg font-bold">Delete pet</h3>
                <Button severity='danger' class="inline-block mt-2" @click.prevent="router.delete(route('pets.destroy',pet.id))">Usuń</Button>
            </form>

        </BigCard>
    </AuthenticatedLayout>
</template>
