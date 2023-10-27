
<template>
    <form class="flex items-center w-[700px] h-[80px]">
        <TextInput v-model="form.search" type="text" class="block w-full mr-2"
                    placeholder="What you are looking for..." autocomplete />
    </form>
</template>

<script setup>
//imports
import TextInput from "../TextInput.vue";
import {useForm} from "@inertiajs/vue3";


//refs

//props & emits
const form = useForm({
    search: '',
})

//methods

//computed

// hooks
</script>


<style scoped>

</style>
