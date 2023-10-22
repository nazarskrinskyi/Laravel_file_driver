<template>
    <AuthenticatedLayout>
        <table class="min-w-full">
            <thead class="border border-b-black">
            <tr>
                <th class="font-semibold border border-r-black border-b-black">#</th>
                <th class="font-semibold border border-r-black border-b-black">Name</th>
                <th class="font-semibold border border-r-black border-b-black">Owner</th>
                <th class="font-semibold border border-r-black border-b-black">Last Modified</th>
                <th class="font-semibold border border-r-black border-b-black">Created</th>
                <th class="font-semibold border border-r-black border-b-black">Size</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="file in files.data" @dblclick.prevent="openFolder(file)" class="cursor-pointer transition duration-300 ease-in-out hover:bg-gray-300" :key="file.id">
                <td class="font-medium border border-r-black  border-b-black py-2 text-center">{{ file.id }}</td>
                <td class="p-2 font-medium border border-r-black  border-b-black">{{ file.name }}</td>
                <td class="p-2 font-medium border border-r-black  border-b-black">{{ file.owner }}</td>
                <td class="p-2 font-medium border border-r-black  border-b-black">{{ file.updated_at }}</td>
                <td class="p-2 font-medium border border-r-black  border-b-black">{{ file.created_at }}</td>
            </tr>
            </tbody>
        </table>
        <div v-if="!files.data.length" class="py-8 text-center font-semibold text-4xl text-gray-400">
            <h1>There no data in laravel driver</h1>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "../Layouts/AuthenticatedLayout.vue";
import {router} from "@inertiajs/vue3";

const {files} = defineProps({
    files: Object
})

function openFolder(file)
{
    if (!file.is_folder)
    {
        return;
    }
    router.visit(route('myFiles', {folder: file.path}))

}
</script>
