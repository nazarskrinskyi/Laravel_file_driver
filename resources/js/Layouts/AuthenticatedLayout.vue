<template>
    <div class="h-screen bg-gray-50 flex w-full gap-4">
        <Navigation/>
        <main @drop.prevent="handleDrop" @dragleave.prevent="onDragLeave" @dragover.prevent="onDragOver"
              class="flex flex-col flex-1 px-4"
              :class="dragOver ? 'dropzone' : ''">
            <template v-if="dragOver" class="text-gray-500 text-sm py-8">
                Drag Files Here To Upload
            </template>
            <template v-else>
                <div class="flex items-center justify-between w-full">
                    <div>
                        <SearchForm/>
                    </div>
                    <div>
                        <UserSettingsDropdown/>
                    </div>
                </div>
                <div class="flex flex-col overflow-hidden">
                    <slot/>
                </div>
            </template>
        </main>
    </div>
    <ErrorMessageModal/>
    <Notification />
    <ProgressBar :form="fileUploadForm"/>
</template>

<style scoped>

.dropzone {
    width: 100%;
    height: 100%;
    color: #4a5568;
    border: 2px dashed black;
    display: flex;
    justify-content: center;
    align-items: center;
}
</style>

<script setup>
// imports
import Navigation from "../Components/app/Navigation.vue";
import SearchForm from "../Components/app/SearchForm.vue";
import UserSettingsDropdown from "../Components/app/UserSettingsDropdown.vue";
import {onMounted, ref} from "vue";
import {emitter, FILE_UPLOAD_STARTED, showErrorMessage, showSuccessNotification,} from "../event-mitt.js";
import {useForm, usePage} from "@inertiajs/vue3";
import ProgressBar from "../Components/app/ProgressBar.vue";
import ErrorMessageModal from "../Components/app/ErrorMessageModal.vue";
import Notification from "../Components/app/Notification.vue";


// Uses
const page = usePage();
const fileUploadForm = useForm({
    files: [],
    relative_paths: [],
    parent_id: null
})

// Refs
const dragOver = ref(false);

// Props & Emit

// Computed


// Methods
function onDragOver() {
    dragOver.value = true;
}

function onDragLeave() {
    dragOver.value = false;
}

function handleDrop(event) {
    dragOver.value = false;
    const items = event.dataTransfer.items;

    // Check if dropped items contain directories
    const folders = [];
    const files = [];
    for (let i = 0; i < items.length; i++) {
        const item = items[i].webkitGetAsEntry();
        if (item) {
            if (item.isDirectory) {
                folders.push(item);
            } else {
                files.push(item);
            }
        }
    }

    // Handle folder uploads
    if (folders.length > 0) {
        console.log(11)
        handleFolderUpload(folders);
    }

    // Handle file uploads
    if (files.length > 0) {
        uploadFiles(files);
    }
}

function handleFolderUpload(folders) {
    // Handle folder upload logic here
    // You can use a recursive function to handle files inside folders
    // For example:
    // function processFolder(folder) {
    //     // Handle files inside the folder using folder.createReader(), etc.
    //     // Recursively call processFolder() for subfolders if needed
    // }
    // for (const folder of folders) {
    //     processFolder(folder);
    // }
    // You need to implement the logic to handle files inside folders based on your requirements.
}

function uploadFiles(files) {

    fileUploadForm.parent_id = page.props.folder.id;
    fileUploadForm.files = files;
    fileUploadForm.relative_paths = [...files].map(file => file.webkitRelativePath);

    fileUploadForm.post(route('file.store'), {
        onSuccess: () => {
            showSuccessNotification('You successfully uploaded ' + fileUploadForm.files.length + ' files')
        },

        onFinish: () => {
            fileUploadForm.clearErrors();
            fileUploadForm.reset();
        },

        onError: errors => {
            let message = '';
            if (Object.keys(errors)) {
                message = errors[Object.keys(errors)[0]]
                console.log(message)
            } else {
                message = 'There is an Error during upload! Try again later.'
            }
            showErrorMessage(message);
        }
    })
}

//computed

// hooks
onMounted(() => {
    emitter.on(FILE_UPLOAD_STARTED, uploadFiles);
})

</script>
