<script setup>
import Navigation from "../Components/app/Navigation.vue";
import SearchForm from "../Components/app/SearchForm.vue";
import UserSettingsDropdown from "../Components/app/UserSettingsDropdown.vue";
</script>

<template>
    <div class="h-screen bg-gray-50 flex w-full gap-4">
        <Navigation/>
        <main class="flex flex-col flex-1 px-4 overflow-hidden">
            <div class="flex items-center justify-between w-full">
                <div>
                    <SearchForm/>
                </div>
                <div>
                    <UserSettingsDropdown/>
                </div>
            </div>
            <div class="flex flex-col overflow-hidden">
                <slot />
            </div>
        </main>
    </div>
</template>
