<template>
  <SecondaryButton @click="onClick" class="mr-2">

    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
         class="w-6 h-6 mr-2">
      <path stroke-linecap="round" stroke-linejoin="round"
            d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z"/>
    </svg>
    Share
  </SecondaryButton>
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
      showConfirmDialog.value = false
      emit('restore')
      showSuccessNotification('Selected files have been restored')
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


