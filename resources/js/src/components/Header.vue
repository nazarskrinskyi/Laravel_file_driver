<script setup>
</script>

<template>
    <header class="header d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom">
        <router-link :to="{ name: 'home' }" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <svg class="bi me-2" width="40" height="32"><use xlink:href="#bootstrap"></use></svg>
            <span class="fs-4">Laravel + Vue</span>
        </router-link>

        <ul class="flex nav nav-pills">
            <li class="nav-item"><router-link :to="{ name: 'home' }" class="nav-link" aria-current="page">Home</router-link></li>
            <li class="nav-item"><router-link :to="{ name: 'info' }" class="nav-link">Info</router-link></li>
            <li class="nav-item"><router-link :to="{ name: 'about' }" class="nav-link">About</router-link></li>
        </ul>
    </header>
</template>

<style scoped>
.header {
    background: whitesmoke;
}
</style>
