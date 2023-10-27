

<template>
    <MenuItem v-slot="{ active }">
        <button
            :class="[
                  active ? 'bg-black  text-white' : 'text-gray-900',
                  'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                ]" class="relative"
        >
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 7.5h-.75A2.25 2.25 0 004.5 9.75v7.5a2.25 2.25 0 002.25 2.25h7.5a2.25 2.25 0 002.25-2.25v-7.5a2.25 2.25 0 00-2.25-2.25h-.75m0-3l-3-3m0 0l-3 3m3-3v11.25m6-2.25h.75a2.25 2.25 0 012.25 2.25v7.5a2.25 2.25 0 01-2.25 2.25h-7.5a2.25 2.25 0 01-2.25-2.25v-.75" />
            </svg>
            <input @change="onChange" class="absolute top-0 left-0 right-0 bottom-0 opacity-0 w-full cursor-pointer" type="file" multiple>
            Upload Files
        </button>
    </MenuItem>
</template>

<script setup lang="ts">
//import
import {MenuItem} from "@headlessui/vue";
import {emitter, FILE_UPLOAD_STARTED} from "../../event-mitt.js";

//refs

//props & emits

//methods
function onChange(event)
{
    emitter.emit(FILE_UPLOAD_STARTED, event.target.files)
}
//computed

// hooks

</script>

<style scoped>

</style>
