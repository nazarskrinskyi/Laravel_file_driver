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
import {emitter, FILE_UPLOAD_STARTED, showErrorMessage,} from "../event-mitt.js";
import {useForm, usePage} from "@inertiajs/vue3";
import ProgressBar from "../Components/app/ProgressBar.vue";
import ErrorMessageModal from "../Components/app/ErrorMessageModal.vue";


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
    console.log(event)
    dragOver.value = false;
    const files = event.dataTransfer.files;
    console.log(files)


    uploadFiles(files);
}

function uploadFiles(files) {
    console.log(files);

    fileUploadForm.parent_id = page.props.folder.id;
    fileUploadForm.files = files;
    fileUploadForm.relative_paths = [...files].map(file => file.webkitRelativePath);

    fileUploadForm.post(route('file.store'), {

        onFinish: () => {
            fileUploadForm.clearErrors();
            fileUploadForm.reset();
        },

        onError: errors => {
            console.log(errors)
            let message = '';
            console.log(Object.keys(errors))
            if (Object.keys(errors)) {
                message = errors[Object.keys(errors)[0]]
                console.log(message)
            } else {
                message = 'There is an Error during upload! Try again later.'
            }
            console.log(message);
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
