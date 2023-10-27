
<template>
    <Menu as="div" class="relative inline-block text-left py-2 ml-4">
        <div>
            <MenuButton
                class="inline-flex w-full justify-center rounded-md bg-black bg-opacity-20 px-4 py-2 text-sm font-semibold text-gray-900  hover:bg-opacity-30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75"
            >
                Create New
                <ChevronDownIcon
                    class="ml-2 -mr-1 h-5 w-5 text-black hover:text-violet-200"
                    aria-hidden="true"
                />
            </MenuButton>
        </div>

        <transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <MenuItems
                class="absolute right-0 mt-2 w-40 origin-top-right divide-y divide-gray-600 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
            >
                <div class="px-1 py-1">
                    <MenuItem v-slot="{ active }">
                        <a href="#" @click.prevent="showCreateFolderProcess"
                            :class="[
                  active ? 'bg-black  text-white' : 'text-gray-600',
                  'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                ]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            New Folder
                        </a>
                    </MenuItem>
                </div>

                <div class="px-1 py-1">
                    <FileUploadMenu />
                    <FolderUploadMenu />
                </div>
            </MenuItems>
        </transition>
    </Menu>
    <CreateFolderModal v-model="ShowCreateFolderModal" />
</template>


<script setup lang="ts">
// imports
import {Menu, MenuButton, MenuItem, MenuItems} from "@headlessui/vue";
import {ChevronDownIcon} from "@heroicons/vue/20/solid";
import CreateFolderModal from "./CreateFolderModal.vue";
import {ref} from "vue";
import FileUploadMenu from "./FileUploadMenu.vue";
import FolderUploadMenu from "./FolderUploadMenu.vue";

//refs
const ShowCreateFolderModal = ref(false);

//props & emits

//methods
function showCreateFolderProcess() {
    ShowCreateFolderModal.value = true;
}

//computed

// hooks

</script>

<style scoped>

</style>
