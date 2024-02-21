<template>
  <div>
    <TransitionRoot as="template" :show="sidebarOpen">
      <Dialog
        as="div"
        class="relative z-50 lg:hidden"
        @close="sidebarOpen = false"
      >
        <TransitionChild
          as="template"
          enter="transition-opacity ease-linear duration-300"
          enter-from="opacity-0"
          enter-to="opacity-100"
          leave="transition-opacity ease-linear duration-300"
          leave-from="opacity-100"
          leave-to="opacity-0"
        >
          <div class="fixed inset-0 bg-gray-900/80" />
        </TransitionChild>

        <div class="fixed inset-0 flex">
          <TransitionChild
            as="template"
            enter="transition ease-in-out duration-300 transform"
            enter-from="-translate-x-full"
            enter-to="translate-x-0"
            leave="transition ease-in-out duration-300 transform"
            leave-from="translate-x-0"
            leave-to="-translate-x-full"
          >
            <DialogPanel class="relative mr-16 flex w-full max-w-xs flex-1">
              <TransitionChild
                as="template"
                enter="ease-in-out duration-300"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="ease-in-out duration-300"
                leave-from="opacity-100"
                leave-to="opacity-0"
              >
                <div
                  class="absolute left-full top-0 flex w-16 justify-center pt-5"
                >
                  <button
                    type="button"
                    class="-m-2.5 p-2.5"
                    @click="sidebarOpen = false"
                  >
                    <span class="sr-only">Close sidebar</span>
                    <XMarkIcon class="h-6 w-6 text-white" aria-hidden="true" />
                  </button>
                </div>
              </TransitionChild>

              <!-- Sidebar component, swap this element with another sidebar if you like -->
              <div
                class="flex grow flex-col gap-y-5 overflow-y-auto bg-secondary px-6 pb-2"
              >
                <div class="flex h-16 shrink-0 items-center">
                  <ApplicationLogo
                    class="h-10 w-auto dark:text-gray-600 text-gray-600"
                  />
                </div>

                <!-- SM Nav -->
                <nav class="flex flex-1 flex-col">
                  <ul role="list" class="flex flex-1 flex-col gap-y-7">
                    <li>
                      <ul role="list" class="-mx-2 space-y-1">
                        <li v-for="item in navigation" :key="item.name">
                          <Link
                            v-if="item.target !== '_blank'"
                            :href="item.href"
                            :class="[
                              item.current
                                ? 'bg-background text-primary hover:bg-secondary'
                                : 'text-primary bg-background hover:text-primary hover:bg-secondary',
                              'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold',
                            ]"
                          >
                            <component
                              :is="item.icon"
                              :class="[
                                item.current
                                  ? 'text-primary'
                                  : 'text-gray-400 group-hover:text-primary',
                                'h-6 w-6 shrink-0',
                              ]"
                              aria-hidden="true"
                            />

                            {{ item.name }}
                          </Link>

                          <a
                            v-else
                            :href="item.href"
                            :class="[
                              item.current
                                ? 'bg-background text-primary'
                                : 'text-primary bg-background hover:text-primary hover:bg-secondary',
                              'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold',
                            ]"
                          >
                            <component
                              :is="item.icon"
                              :class="[
                                item.current
                                  ? 'text-primary'
                                  : 'text-gray-400 group-hover:text-primary',
                                'h-6 w-6 shrink-0',
                              ]"
                              aria-hidden="true"
                            />

                            {{ item.name }}
                          </a>
                        </li>
                      </ul>
                    </li>
                  </ul>
                </nav>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </Dialog>
    </TransitionRoot>

    <!-- Static sidebar for desktop -->
    <div
      class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col"
    >
      <!-- Sidebar component, swap this element with another sidebar if you like -->
      <div
        class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-background px-6"
      >
        <div class="flex h-16 shrink-0 items-center">
          <Link href="/">
            <ApplicationLogo
              class="h-10 w-auto dark:text-gray-600 text-gray-600"
            />
          </Link>
        </div>
        <!-- LG Nav -->
        <nav class="flex flex-1 flex-col">
          <ul role="list" class="flex flex-1 flex-col gap-y-7">
            <li>
              <ul role="list" class="-mx-2 space-y-1">
                <li v-for="item in navigation" :key="item.name">
                  <Link
                    v-if="item.target !== '_blank'"
                    :href="item.href"
                    :class="[
                      item.current
                        ? 'bg-background text-primary'
                        : 'text-primary bg-background hover:text-primary hover:bg-secondary',
                      'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold',
                    ]"
                  >
                    <component
                      :is="item.icon"
                      :class="[
                        item.current
                          ? 'text-primary'
                          : 'text-gray-400 group-hover:text-primary',
                        'h-6 w-6 shrink-0',
                      ]"
                      aria-hidden="true"
                    />
                    {{ item.name }}
                  </Link>

                  <a
                    v-else
                    :href="item.href"
                    target="_blank"
                    :class="[
                      item.current
                        ? 'text-primary'
                        : 'text-gray-400 group-hover:text-primary',
                      'group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold',
                    ]"
                  >
                    <component
                      :is="item.icon"
                      :class="[
                        item.current
                          ? 'text-primary'
                          : 'text-gray-400 group-hover:text-primary',
                        'h-6 w-6 shrink-0',
                      ]"
                      aria-hidden="true"
                    />

                    {{ item.name }}
                  </a>
                </li>
              </ul>
            </li>

            <li class="-mx-6 mt-auto">
              <Menu as="div" class="relative ml-3">
                <MenuButton
                  class="relative flex rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800"
                >
                  <span class="absolute -inset-1.5" />
                  <span class="sr-only">Open user menu</span>
                  <img
                    class="h-8 w-8 rounded-full"
                    src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                    alt=""
                  />
                </MenuButton>
              </Menu>
            </li>
          </ul>
        </nav>
      </div>
    </div>

    <div
      class="sticky top-0 z-40 flex items-center gap-x-6 bg-secondary px-4 py-4 shadow-sm sm:px-6 lg:hidden"
    >
      <button
        type="button"
        class="-m-2.5 p-2.5 text-gray-700 lg:hidden"
        @click="sidebarOpen = true"
      >
        <span class="sr-only">Open sidebar</span>
        <Bars3Icon class="h-6 w-6 text-primary" aria-hidden="true" />
      </button>

      <div class="flex-1 text-sm font-semibold leading-6 text-primary">
        Dashboard
      </div>

      <Menu as="div" class="relative ml-3">
        <MenuButton
          class="relative flex rounded-full bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800"
        >
          <span class="absolute -inset-1.5" />
          <span class="sr-only">Open user menu</span>
          <img
            class="h-8 w-8 rounded-full"
            src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
            alt=""
          />
        </MenuButton>

        <transition
          enter-active-class="transition ease-out duration-100"
          enter-from-class="transform opacity-0 scale-95"
          enter-to-class="transform opacity-100 scale-100"
          leave-active-class="transition ease-in duration-100"
          leave-from-class="transform opacity-100 scale-100"
          leave-to-class="transform opacity-0 scale-95"
        >
          <MenuItems
            class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
          >
            <MenuItem v-slot="{ active }">
              <a
                href="#"
                :class="[
                  active ? 'bg-gray-100' : '',
                  'block px-4 py-2 text-sm text-gray-700',
                ]"
              >
                Your Profile
              </a>
            </MenuItem>
            <MenuItem v-slot="{ active }">
              <a
                :href="route('user-settings.index')"
                :class="[
                  active ? 'bg-gray-100' : '',
                  'block px-4 py-2 text-sm text-gray-700',
                ]"
                >Settings</a
              >
            </MenuItem>

            <MenuItem v-if="user" v-slot="{ active }">
              <Link
                :href="route('logout')"
                method="post"
                :class="[
                  active ? 'bg-gray-100' : '',
                  'block px-4 py-2 text-sm text-gray-700',
                ]"
              >
                Sign out
              </Link>
            </MenuItem>

            <MenuItem v-else v-slot="{ active }">
              <Link
                :href="route('login')"
                :class="[
                  active ? 'bg-gray-100' : '',
                  'block px-4 py-2 text-sm text-gray-700',
                ]"
              >
                Sign in
              </Link>
            </MenuItem>
          </MenuItems>
        </transition>
      </Menu>
    </div>

    <main class="lg:pl-72">
      <header
        class="hidden lg:block shadow-sm"
        v-if="$slots.header"
        :class="{
          'lg:hidden': sidebarOpen,
          'lg:sticky top-0 z-40 bg-background-600': !sidebarOpen,
        }"
        d
      >
        <slot name="header" />
      </header>
      <!-- Main area -->
      <slot />
    </main>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue'
import {
  Dialog,
  DialogPanel,
  TransitionChild,
  TransitionRoot,
  Menu,
  MenuButton,
  MenuItem,
  MenuItems,
} from '@headlessui/vue'
import {
  Bars3Icon,
  CalendarIcon,
  HomeIcon,
  UsersIcon,
} from '@heroicons/vue/24/outline'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'

const sidebarOpen = ref(false)
const navigation = [
  {
    name: 'Dashboard',
    href: route('admin.dashboard'),
    icon: HomeIcon,
    current: route().current('admin.dashboard'),
  },
  {
    name: 'Users',
    href: route('admin.users.index'),
    icon: UsersIcon,
    current: route().current('admin.users.index'),
  },
  {
    name: 'Telescope',
    target: '_blank',
    href: route('telescope'),
    icon: CalendarIcon,
    current: route().current('telescope'),
  },
]
</script>
