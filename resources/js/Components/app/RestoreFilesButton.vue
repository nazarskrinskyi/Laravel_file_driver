<template>
    <SecondaryButton @click="onClick" class="mr-2">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 mr-2">
            <path fill-rule="evenodd" d="M21.53 9.53a.75.75 0 01-1.06 0l-4.72-4.72V15a6.75 6.75 0 01-13.5 0v-3a.75.75 0 011.5 0v3a5.25 5.25 0 1010.5 0V4.81L9.53 9.53a.75.75 0 01-1.06-1.06l6-6a.75.75 0 011.06 0l6 6a.75.75 0 010 1.06z" clip-rule="evenodd" />
        </svg>
        Restore
    </SecondaryButton>
    <ConfirmationModal :show="showConfirmDialog"
                       message="Are you sure you want to delete selected files?"
                       @cancel="onConfirmCancel"
                       @confirm="onConfirmConfirm">

    </ConfirmationModal>
</template>

<script setup>
// Imports
import {ref} from "vue";
import {useForm, usePage} from "@inertiajs/vue3";
import ConfirmationModal from "./ConfirmationModal.vue";
import {showErrorMessage, showSuccessNotification} from "../../event-mitt.js";
import SecondaryButton from "../SecondaryButton.vue";

// Uses
const page = usePage();
const restoreFilesForm = useForm({
    all: null,
    ids: [],
});
// Refs
const showConfirmDialog = ref(false)

// Props & Emit

const props = defineProps({
    restoreAll: {
        type: Boolean,
        required: false,
        default: false
    },
    restoreIds: {
        type: Array,
        required: false
    }
})

const emit = defineEmits(['restore'])

// Computed

// Methods

function onClick() {
    if (!props.restoreIds.length && !props.restoreAll) {
        showErrorMessage("You didn't selected any files or folders");
        return;
    }
    showConfirmDialog.value = true;
}

function onConfirmCancel() {
    showConfirmDialog.value = false;
}

function onConfirmConfirm() {
    if (props.restoreAll) restoreFilesForm.all = true;
    else restoreFilesForm.ids = props.restoreIds;
    console.log("restore", props.restoreAll, props.restoreIds);

    restoreFilesForm.post(route('file.restore'), {
        onSuccess: () => {
            showConfirmDialog.value = false;
            restoreFilesForm.reset();
            showSuccessNotification("You successfully restored " + props.restoreIds.length + ' files')
            props.restoreIds = [];
        },
        onError: error => {
            showErrorMessage(error)
        }
    })
}

// Hooks

</script>

<style scoped>

</style>


