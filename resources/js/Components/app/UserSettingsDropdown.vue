<template>
    <div class="w-[100px] text-right">
        <Menu as="div" class="relative inline-block text-left">
            <div>
                <MenuButton
                    class="inline-flex w-full justify-center bg-gray-900 rounded-md bg-opacity-20 px-4 py-2 text-sm font-semibold  hover:bg-opacity-30 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75"
                >
                    {{ $page.props.auth.user.name }}
                    <ChevronDownIcon
                        class="ml-2 -mr-1 h-5 w-5 text-black-900 hover:text-violet-200"
                        aria-hidden="true"
                    />
                </MenuButton>
            </div>

            <transition
                enter-active-class="transition duration-100 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-in"
                leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0"
            >
                <MenuItems
                    class="absolute right-0 mt-2 w-32 origin-top-right divide-y divide-gray-600 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                >
                    <div class="px-1 py-1">
                        <MenuItem v-slot="{ active }">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                <button :class="[active ? 'bg-black text-white' : 'text-gray-600','group flex w-full items-center rounded-md px-2 py-2 text-sm',]"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor" class="w-6 h-6 pr-2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Profile
                                </button>
                            </ResponsiveNavLink>
                        </MenuItem>
                        <MenuItem v-slot="{ active }">
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                <button
                                    :class="[active ? 'bg-black text-white' : 'text-gray-900','group flex w-full items-center rounded-md px-2 py-2 text-sm', ]"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                         stroke-width="1.5" stroke="currentColor" class="w-6 h-6 pr-2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                                    </svg>
                                    Logout
                                </button>
                            </ResponsiveNavLink>
                        </MenuItem>
                    </div>

                </MenuItems>
            </transition>
        </Menu>
    </div>
</template>

<script setup>
import {Menu, MenuButton, MenuItems, MenuItem} from '@headlessui/vue'
import {ChevronDownIcon} from '@heroicons/vue/20/solid'
import ResponsiveNavLink from "../ResponsiveNavLink.vue";


</script>
