<template>
    <AuthenticatedLayout>
        <nav class="flex justify-end">
            <div class="flex mb-3 justify-end mr-8 pt-1">
                <RestoreFilesButton :restoreAll="allSelected" :restoreIds="selectedIds" @restore="onRestore"/>
                <DeleteFilesForeverButton :delete-all="allSelected" :delete-ids="selectedIds" @delete="onDelete"/>
            </div>
        </nav>
        <div class="flex-1 overflow-auto">
            <table class="min-w-full">
                <thead class="border border-b-black">
                <tr>
                    <th class="font-semibold border border-r-black border-b-black p-3">
                        <Checkbox v-model:checked="allSelected" @change="onAllSelectedChange"
                                  class="cursor-pointer p-3"></Checkbox>
                    </th>
                    <th class="font-semibold border border-r-black border-b-black">Name</th>
                    <th class="font-semibold border border-r-black border-b-black">Path</th>
                    <th class="font-semibold border border-r-black border-b-black">Size</th>
                    <th class="font-semibold border border-r-black border-b-black">Last Modified</th>
                    <th class="font-semibold border border-r-black border-b-black">Created</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="file in allFiles.data"
                    :class="selected[file.id] || allSelected ? 'bg-blue-200' : file.is_folder === 1 ? 'bg-amber-100' : ''"
                    @click="toggleFileSelect($event, file)"
                    @change="onSelectedCheckboxChange(file)"
                    class="cursor-pointer transition duration-300 ease-in-out hover:bg-gray-300" :key="file.id">
                    <td class="font-medium border border-r-black border-b-black relative">
                        <Checkbox :checked="selected[file.id] || allSelected" v-model="selected[file.id]"
                                  @change="onSelectedCheckboxChange(file)"
                                  class="cursor-pointer absolute top-[40%] left-[40%] p-2"></Checkbox>
                    </td>
                    <td class="cursor-pointer p-2 font-medium border border-r-black  border-b-black"><span
                        class="flex items-center"><FileIcon :file="file" class="pl-3"/><span class="pl-4">{{
                            file.name
                        }}</span></span></td>
                    <td class="p-2 font-medium border border-r-black  border-b-black">{{ file.path }}</td>
                    <td class="p-2 font-medium border border-r-black  border-b-black">{{ file.size }}</td>
                    <td class="p-2 font-medium border border-r-black  border-b-black">{{ file.updated_at }}</td>
                    <td class="p-2 font-medium border border-r-black  border-b-black">{{ file.created_at }}</td>
                </tr>
                </tbody>
            </table>
            <div v-if="!allFiles.data.length" class="py-8 text-center font-semibold text-4xl text-gray-400">
                <h1>There no data in laravel driver</h1>
            </div>
            <div ref="loadMoreIntersect">

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
//imports
import AuthenticatedLayout from "../Layouts/AuthenticatedLayout.vue";
import FileIcon from "../Components/app/FileIcon.vue";
import {computed, onMounted, onUpdated, ref} from "vue";
import {httpGet} from "../Helper/http-helper.js";
import Checkbox from "../Components/Checkbox.vue";
import RestoreFilesButton from "../Components/app/RestoreFilesButton.vue";
import DeleteFilesForeverButton from "../Components/app/DeleteFilesForeverButton.vue";

//refs
const allSelected = ref(false)
const selected = ref({})
const loadMoreIntersect = ref(null)
const allFiles = ref({
    data: props.files.data,
    next: props.files.links.next
})
//props & emits
const props = defineProps({
    files: Object,
    folder: Object,
    ancestors: Object
})

//methods

function toggleFileSelect(event, file) {
    if (event.shiftKey) {
        selected.value[file.id] = !selected.value[file.id];
        let folders = [];
        let files = [];
        allFiles.value.data.forEach(file => {
            for (let id of Object.keys(selected.value)) {
                if (file.id === id && file.is_folder) {
                    folders.push(file.id);
                } else if (file.id === id && !file.is_folder) {
                    files.push(file.id);
                }

            }
        })

        if (files.length > 1) {
            allFiles.value.data.forEach(file => {
                if (!file.is_folder) {
                    if (file.id >= Math.min(...files) && file.id <= Math.max(...files)) {
                        selected.value[file.id] = true;
                    }
                }
            })
        }
        if (folders.length > 1) {
            allFiles.value.data.forEach(folder => {
                if (folder.is_folder) {
                    if (folder.id >= Math.min(...folders) && folder.id <= Math.max(...folders)) {
                        selected.value[folder.id] = true;
                    }
                }
            })
        }
        if (folders.length > 0 && files.length > 0) {
            allFiles.value.data.forEach(file => {
                if (file.is_folder) {
                    if (file.id > 0 && file.id <= Math.max(...folders)) {
                        selected.value[file.id] = true;
                    }
                }
                if (!file.is_folder) {
                    if (file.id >= Math.min(...files)) {
                        selected.value[file.id] = true;
                    }
                }
            })
        }

        if (files.length > 2 || folders.length > 2) {
            selected.value = {};
            files.length = 0;
            folders.length = 0;
        }

    } else selected.value[file.id] = !selected.value[file.id];
}

function onAllSelectedChange() {
    allFiles.value.data.forEach(file => {
        selected.value[file.id] = allSelected.value;
    })
}

function onSelectedCheckboxChange(file) {
    if (!selected.value[file.id]) allSelected.value = false;
    else {
        let check = true;
        allFiles.value.data.forEach(file => {
            if (!selected.value[file.id]) {
                check = false;
            }
        })
        allSelected.value = check;
    }
}

function loadMoreFiles() {
    if (allFiles.value.next === null) {
        return;
    }
    httpGet(allFiles.value.next)
        .then(res => {
            if (res.data && Array.isArray(res.data)) {
                allFiles.value.data = [...allFiles.value.data, ...res.data]
                allFiles.value.next = res.links.next;
            } else {
                console.error("Invalid or empty response data");
            }
        })
        .catch(error => {
            console.error(error); // Handle errors that occur during the request
        });
}

function onDelete() {
    selected.value = {};
    allSelected.value = false;
}

function onRestore() {
    selected.value = {};
    allSelected.value = false;
}
//computed
const selectedIds = computed(() => {
    return Object.keys(selected.value).filter(fileId => selected.value[fileId]);
});

// hooks

onUpdated(() => {
    allFiles.value = {
        data: props.files.data,
        next: props.files.links.next
    }
})
onMounted(() => {
    const observer = new IntersectionObserver((entries) => entries.forEach(entry => entry.isIntersecting && loadMoreFiles()), {
        rootMargin: "-250px 0px 0px 0px"
    })
    observer.observe(loadMoreIntersect.value)
})

</script>
