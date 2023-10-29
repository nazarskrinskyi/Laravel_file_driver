<template>
    <span>
        <img class="w-16" v-if="file.is_folder" :src="`/images/icon/folder.png`">
        <template v-else>
            <img class="max-w-full" v-if="isImage(file)" :src="`/images/icon/image.png`">
            <img class="max-w-full" v-else-if="isPDF(file)" :src="`/images/icon/pdf.png`">
            <img class="max-w-full" v-else-if="isAudio(file)" :src="`/images/icon/audio.png`">
            <img class="max-w-full" v-else-if="isVideo(file)" :src="`/images/icon/video.png`">
            <img class="max-w-full" v-else-if="isWord(file)" :src="`/images/icon/word.png`">
            <img class="max-w-full" v-else-if="isExcel(file)" :src="`/images/icon/excel.png`">
            <img class="max-w-full" v-else-if="isZip(file)" :src="`/images/icon/zip.png`">
            <img class="max-w-full" v-else-if="isText(file)" :src="`/images/icon/txt-file.png`">
            <img class="max-w-full" v-else :src="`/images/icon/attach-file.png`">
        </template>
    </span>

</template>

<script setup>
// Imports
import {isAudio, isExcel, isImage, isPDF, isText, isVideo, isWord, isZip} from "@/Helper/file-helper.js";

// Uses

// Refs

// Props & Emit

const {file} = defineProps({
    file: Object
})

// Computed

// Methods

// Hooks

</script>

<style scoped>

</style>
