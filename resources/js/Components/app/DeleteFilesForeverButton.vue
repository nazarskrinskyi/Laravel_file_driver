<template>
    <button @click="onClick"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 mr-2">
            <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm3 10.5a.75.75 0 000-1.5H9a.75.75 0 000 1.5h6z" clip-rule="evenodd" />
        </svg>

        Delete Forever
    </button>
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

// Uses
const page = usePage();
const deleteFilesForeverForm = useForm({
    all: null,
    ids: [],
    parent_id: null
});
// Refs
const showConfirmDialog = ref(false)

// Props & Emit
const props = defineProps({
    deleteAll: {
        type: Boolean,
        required: false,
        default: false
    },
    deleteIds: {
        type: Array,
        required: false
    }
})

const emit = defineEmits(['delete'])

// Computed

// Methods

function onClick() {
    if (!props.deleteIds.length && !props.deleteAll) {
        showErrorMessage("You didn't selected any files or folders");
        return;
    }
    showConfirmDialog.value = true;
}

function onConfirmCancel() {
    showConfirmDialog.value = false;
}

function onConfirmConfirm() {
    deleteFilesForeverForm.parent_id = page.props.folder.id;
    if (props.deleteAll) deleteFilesForeverForm.all = true;
    else deleteFilesForeverForm.ids = props.deleteIds;
    console.log("Delete", props.deleteAll, props.deleteIds);

    deleteFilesForeverForm.delete(route('file.delete-forever'), {
        onSuccess: () => {
            showConfirmDialog.value = false;
            deleteFilesForeverForm.reset();
            showSuccessNotification("You successfully deleted " + props.deleteIds.length + ' files')
            props.deleteIds = [];
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


