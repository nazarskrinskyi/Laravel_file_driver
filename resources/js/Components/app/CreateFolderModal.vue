<template>
    <modal :show="modelValue" max-width="sm">
        <div class="p-6">
            <div class="flex flex-1 items-center justify-center">
                <h2 class="font-medium text-gray-900 text-xl">Create New Folder</h2>
                <button
                    class="absolute top-0 mt-2 right-0 mr-2 bg-transparent hover:bg-red-500 text-red-700 font-semibold hover:text-white border border-red-500 hover:border-transparent rounded"
                    @click.prevent="cancelProcess">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

            </div>
            <div class="mt-6">
                <InputLabel for="folderName" value="Folder Name" class="font-semibold text-xl pb-2 sr-only"/>
                <TextInput type="text" v-model="form.name" placeholder="Type It Here..." id="folderName"
                           class="w-full flex flex-1" ref="folderNameInput"
                           :class="form.errors.name ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                           @keyup.enter="createFolder" @keyup.esc="cancelProcess"/>
                <InputError :message="form.errors.name" class="mt-1" v-if="form.errors"/>
                <div class="flex flex-1 justify-end">
                    <PrimaryButton type="submit" class="mt-3" :class="{'opacity-25': form.processing}" @click.prevent="createFolder" :disable="form.processing">Save</PrimaryButton>
                </div>
            </div>
        </div>
    </modal>
</template>

<script setup lang="ts">

import Modal from "../Modal.vue";
import InputLabel from "../InputLabel.vue";
import TextInput from "../TextInput.vue";
import PrimaryButton from "../PrimaryButton.vue";
import {useForm} from "@inertiajs/vue3";
import InputError from "../InputError.vue";
import {ref} from "vue";

const {modelValue} = defineProps({
    modelValue: Boolean
})


const emit = defineEmits(['update:modelValue'])

const form = useForm({
    name: '',
    errors: {
        name: ''
    }
});
const formNameInput = ref(null);

function createFolder() {
    form.post(route('folder.create'), {
        preserveScroll: true,
        onSuccess: () => {
            cancelProcess()

            //need to add success notification
        },
        onError: () => {
            formNameInput.value.focus()
        }
    })
}

function cancelProcess() {
    emit('update:modelValue')
    form.clearErrors()
    form.reset()
}

</script>
<style scoped>

</style>
