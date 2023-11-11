<template>
    <SecondaryButton @click="download" class="bg-blue-300 hover:bg-blue-400 mr-2">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6  mr-2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
        </svg>
        Download
    </SecondaryButton>

</template>

<script setup>
// Imports
import {ref} from "vue";
import SecondaryButton from "../SecondaryButton.vue";

// Uses
import {useForm, usePage} from "@inertiajs/vue3";
import {httpGet} from "../../Helper/http-helper.js";
import {showErrorMessage, showSuccessNotification} from "../../event-mitt.js";

const page = usePage();
const downloadFilesForm = useForm({
    all: null,
    ids: [],
    parent_id: null
});

// Refs
const showDeleteDialog = ref(false)

// Props & Emit
const props = defineProps({
    all: {
        type: Boolean,
        required: false,
        default: false
    },
    ids: {
        type: Array,
        required: false
    }
})

// Computed

// Methods


function download()
{
    console.log(props.all)
    if (!props.all && props.ids.length === 0) {
        showErrorMessage("You didn't selected any files or folders");
        return;
    }
    const url_params = new URLSearchParams();
    url_params.append('parent_id', page.props.folder.id)
    if (props.all) {
        url_params.append('all', props.all ? 1 : 0)
    }
    else {
        props.ids.forEach(id => url_params.append('ids[]', id))
    }
    httpGet(route('file.download') + "?" + url_params.toString())
        .then(res => {
            if (!res.url) return;

            const file = document.createElement('a');
            file.download = res.file_name;
            file.href = res.url;
            file.click();
            showSuccessNotification('You successfully downloaded ' + props.ids.length + ' files');
            props.ids = [];
        })
        .catch(error => {
            showErrorMessage(error);
        })
}

// Hooks

</script>

<style scoped>

</style>


