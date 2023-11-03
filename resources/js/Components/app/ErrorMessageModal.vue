
<template>
    <Modal :show="show" max-width="md">
        <div class="p-6">
            <h2 class="mb-2 text-xl text-red-500 font-semibold">Error</h2>
            <p>{{message}}</p>
            <div class="flex justify-end">
                <PrimaryButton @click="close">OK</PrimaryButton>
            </div>
        </div>
    </Modal>
</template>

<script setup lang="ts">
import Modal from "../Modal.vue";
import {onMounted, ref} from "vue";
import PrimaryButton from "../PrimaryButton.vue";
import {emitter, SHOW_ERROR_MESSAGE} from "../../event-mitt.js";

//imports

//refs

//props & emits
const show = ref(false);
const message = ref('');

const emit = defineEmits(['close'])
//methods
function close() {
    show.value = false;
    message.value = '';
}

//computed

// hooks
onMounted(() => {
    emitter.on(SHOW_ERROR_MESSAGE, ({message: msg}) => {
        show.value = true;
        message.value = msg;
    })
});
</script>

<style scoped>

</style>
