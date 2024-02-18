<template>
    <div class="bg-gray-900 h-full">
        <Disclosure as="nav" class="bg-gray-800" v-slot="{ open }">
            <div class="mx-auto max-w-7xl px-2 sm:px-6 lg:px-8">
                <div class="relative flex h-16 items-center justify-between">
                    <div class="absolute inset-y-0 left-0 flex items-center sm:hidden">
                        <!-- Mobile menu button-->
                        <DisclosureButton class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                            <span class="absolute -inset-0.5" />
                            <span class="sr-only">Open main menu</span>
                            <Bars3Icon v-if="!open" class="block h-6 w-6" aria-hidden="true" />
                            <XMarkIcon v-else class="block h-6 w-6" aria-hidden="true" />
                        </DisclosureButton>
                    </div>
                    <div class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start">
                        <div class="flex flex-shrink-0 items-center">
                            <img class="h-8 w-auto" src="https://tailwindui.com/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company" />
                        </div>
                        <div class="hidden sm:ml-6 sm:block">
                            <div class="flex space-x-4">
                                <a v-for="item in navigation" :key="item.name" :href="item.href" :target="item.target" :class="[
                                    item.current
                                        ? 'bg-gray-900 text-white'
                                        : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                                    'rounded-md px-3 py-2 text-sm font-medium',
                                ]" :aria-current="item.current ? 'page' : undefined">{{ item.name }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0">
                        <button type="button" class="relative rounded-full bg-gray-800 p-1 text-gray-400 hover:text-white focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                            <span class="absolute -inset-1.5" />
                            <span class="sr-only">View notifications</span>
                            <BellIcon class="h-6 w-6" aria-hidden="true" />
                        </button>

                        <!-- Profile dropdown -->
                        <Menu as="div" class="relative ml-3">
                            <MenuButton class="relative flex rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800">
                                <span class="absolute -inset-1.5" />
                                <span class="sr-only">Open user menu</span>
                                <img class="h-8 w-8 rounded-full" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="" />
                            </MenuButton>

                            <transition enter-active-class="transition ease-out duration-100" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100" leave-active-class="transition ease-in duration-100" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95">
                                <MenuItems class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                    <MenuItem v-slot="{ active }">
                                    <a :href="route('profile.edit')" :class="[
                                        active ? 'bg-gray-100' : '',
                                        'block px-4 py-2 text-sm text-gray-700',
                                    ]">Your Profile</a>
                                    </MenuItem>
                                    <MenuItem v-slot="{ active }">
                                    <a :href="route('user-settings.index')" :class="[
                                        active ? 'bg-gray-100' : '',
                                        'block px-4 py-2 text-sm text-gray-700',
                                    ]">Settings</a>
                                    </MenuItem>

                                    <MenuItem v-if="user" v-slot="{ active }">
                                    <Link :href="route('logout')" method="post" :class="[
                                        active ? 'bg-gray-100' : '',
                                        'block px-4 py-2 text-sm text-gray-700',
                                    ]">
                                    Sign out
                                    </Link>
                                    </MenuItem>

                                    <MenuItem v-else v-slot="{ active }">
                                    <Link :href="route('login')" :class="[
                                        active ? 'bg-gray-100' : '',
                                        'block px-4 py-2 text-sm text-gray-700',
                                    ]">
                                    Sign in
                                    </Link>
                                    </MenuItem>
                                </MenuItems>
                            </transition>
                        </Menu>
                    </div>
                </div>
            </div>

            <DisclosurePanel class="sm:hidden">
                <div class="space-y-1 px-2 pb-3 pt-2">
                    <DisclosureButton v-for="item in navigation" :key="item.name" as="a" :href="item.href" :class="[
                        item.current
                            ? 'bg-gray-900 text-white'
                            : 'text-gray-300 hover:bg-gray-700 hover:text-white',
                        'block rounded-md px-3 py-2 text-base font-medium',
                    ]" :aria-current="item.current ? 'page' : undefined">{{ item.name }}</DisclosureButton>
                </div>
            </DisclosurePanel>
        </Disclosure>




        <main>
            <!-- Hero section -->
            <div class="relative isolate overflow-hidden">
                <svg class="absolute inset-0 -z-10 h-full w-full stroke-white/10 [mask-image:radial-gradient(100%_100%_at_top_right,white,transparent)]" aria-hidden="true">
                    <defs>
                        <pattern id="983e3e4c-de6d-4c3f-8d64-b9761d1534cc" width="200" height="200" x="50%" y="-1" patternUnits="userSpaceOnUse">
                            <path d="M.5 200V.5H200" fill="none" />
                        </pattern>
                    </defs>
                    <svg x="50%" y="-1" class="overflow-visible fill-gray-800/20">
                        <path d="M-200 0h201v201h-201Z M600 0h201v201h-201Z M-400 600h201v201h-201Z M200 800h201v201h-201Z" stroke-width="0" />
                    </svg>
                    <rect width="100%" height="100%" stroke-width="0" fill="url(#983e3e4c-de6d-4c3f-8d64-b9761d1534cc)" />
                </svg>
                <div class="absolute left-[calc(50%-4rem)] top-10 -z-10 transform-gpu blur-3xl sm:left-[calc(50%-18rem)] lg:left-48 lg:top-[calc(50%-30rem)] xl:left-[calc(50%-24rem)]" aria-hidden="true">
                    <div class="aspect-[1108/632] w-[69.25rem] bg-gradient-to-r from-[#80caff] to-[#4f46e5] opacity-20" style="
                            clip-path: polygon(
                                73.6% 51.7%,
                                91.7% 11.8%,
                                100% 46.4%,
                                97.4% 82.2%,
                                92.5% 84.9%,
                                75.7% 64%,
                                55.3% 47.5%,
                                46.5% 49.4%,
                                45% 62.9%,
                                50.3% 87.2%,
                                21.3% 64.1%,
                                0.1% 100%,
                                5.4% 51.1%,
                                21.4% 63.9%,
                                58.9% 0.2%,
                                73.6% 51.7%
                            );
                        " />
                </div>
                <div class="mx-auto max-w-7xl px-6 pb-24 pt-10 sm:pb-40 lg:flex lg:px-8 lg:pt-40">
                    <div class="mx-auto max-w-2xl flex-shrink-0 lg:mx-0 lg:max-w-xl lg:pt-8">
                        <div class="mt-24 sm:mt-32 lg:mt-16">
                            <a href="#" class="inline-flex space-x-6">
                                <span class="rounded-full bg-indigo-500/10 px-3 py-1 text-sm font-semibold leading-6 text-indigo-400 ring-1 ring-inset ring-indigo-500/20">Latest updates</span>
                                <span class="inline-flex items-center space-x-2 text-sm font-medium leading-6 text-gray-300">
                                    <span>Just shipped v1.0</span>
                                    <ChevronRightIcon class="h-5 w-5 text-gray-500" aria-hidden="true" />
                                </span>
                            </a>
                        </div>
                        <h1 class="mt-10 text-4xl font-bold tracking-tight text-white sm:text-6xl">
                            SpinUp Template
                        </h1>
                        <p class="mt-6 text-lg leading-8 text-gray-300">
                            Anim aute id magna aliqua ad ad non deserunt sunt.
                            Qui irure qui lorem cupidatat commodo. Elit sunt
                            amet fugiat veniam occaecat fugiat aliqua.
                        </p>
                        <div class="mt-10 flex items-center gap-x-6">
                            <a href="#" class="rounded-md bg-indigo-500 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-400">Get started</a>
                            <a href="#" class="text-sm font-semibold leading-6 text-white">Live demo <span aria-hidden="true">→</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <div class="flex items-center justify-center w-full h-8">
            <Example />
        </div>


    </div>
</template>

<script setup>
import Example from '@/Components/Example.vue';


import { ChevronRightIcon } from '@heroicons/vue/20/solid';

import { Link, usePage } from '@inertiajs/vue3';
import {
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems

} from '@headlessui/vue';
import { Bars3Icon, BellIcon, XMarkIcon } from '@heroicons/vue/24/outline';

const page = usePage();
const user = page.props.auth.user;

defineProps({

    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

const navigation = [
    { name: 'Welcome', href: '#', current: true },
    { name: 'Dashboard', href: route('admin.dashboard'), current: false },
    { name: 'Telescope', href: '/telescope', target: '_blank', current: false },
];


</script>



