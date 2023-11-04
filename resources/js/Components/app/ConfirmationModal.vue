<template>
    <Modal :show="show" max-width="md">
        <div class="p-6">
            <h2 class="text-2xl font-semibold text-center">Please Confirm</h2>
            <p>{{ message }}</p>
            <div class="mt-4 flex justify-end">
                <div class="pr-3">
                    <PrimaryButton @click="emit('confirm')" @keyup.enter="emit('confirm')">Confirm</PrimaryButton>
                </div>
                <div>
                    <DangerButton @click="emit('cancel')" @keyup.esc="emit('cancel')">Cancel</DangerButton>
                </div>
            </div>
        </div>
    </Modal>
</template>

<script setup>
// Imports
import Modal from "../Modal.vue";
import DangerButton from "../DangerButton.vue";
import PrimaryButton from "../PrimaryButton.vue";

// Uses

// Refs

// Props & Emit
const props = defineProps({
    show: Boolean,
    message: String
})
const emit = defineEmits(['cancel', 'confirm'])

// Computed

// Methods

// Hooks

</script>

<style scoped>

</style>
