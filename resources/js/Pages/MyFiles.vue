<template>
    <AuthenticatedLayout>
        <nav>
            <ol class="flex items-center mb-3">
                <li v-for="ancestor in ancestors.data" :key="ancestor.id" class="inline-flex items-center">
                    <Link v-if="!ancestor.parent_id" :href="route('myFiles')"
                          class="inline-flex items-center text-sm font-medium text-gray-800 dark:text-gray-600 dark:hover:text-black dark:hover:font-semibold">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-1 text-gray-950">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        My Files
                    </Link>
                    <div v-else class="flex items-center">
                        <svg aria-hidden="true" class="w-6 h-6 text-gray-950" fill="currentColor" viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                  d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                  clip-rule="evenodd"></path>
                        </svg>
                        <Link :href="route('myFiles', {folder: ancestor.path})"
                              class="ml-1 text-sm font-medium text-gray-800 md:ml-2 dark:text-gray-600 dark:hover:text-black dark:hover:font-semibold">
                            {{ ancestor.name }}
                        </Link>
                    </div>
                </li>
            </ol>
        </nav>


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
            <tr v-for="file in files.data" @dblclick.prevent="openFolder(file)"
                class="cursor-pointer transition duration-300 ease-in-out hover:bg-gray-300" :key="file.id">
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
//imports
import AuthenticatedLayout from "../Layouts/AuthenticatedLayout.vue";
import {router} from "@inertiajs/vue3";
import {Link} from "@inertiajs/vue3";
//refs

//props & emits
const {files} = defineProps({
    files: Object,
    ancestors: Object
})
//methods
function openFolder(file) {
    if (!file.is_folder) {
        return;
    }
    router.visit(route('myFiles', {folder: file.path}))
}
//computed

// hooks



</script>
