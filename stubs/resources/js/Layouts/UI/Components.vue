<script setup>
import { Button } from '@/Components/ui/button'
import { Input } from '@/Components/ui/input'
import { router, Link, usePage } from '@inertiajs/vue3'
import { defineProps } from 'vue'
const page = usePage()

// using page props to get both the component and components array, find the index and get the prev and next components then navigate to them

const prevComponent = () => {
  return page.props.components[
    page.props.components.indexOf(page.props.component) - 1
  ]
}
const nextComponent = () => {
  return page.props.components[
    page.props.components.indexOf(page.props.component) + 1
  ]
}

const next = () => {
  const index = page.props.components.indexOf(page.props.component)
  const next = page.props.components[index + 1]
  if (next) {
    router.visit(`/ui/components/${next}`)
  }
}

const prev = () => {
  const index = page.props.components.indexOf(page.props.component)
  const prev = page.props.components[index - 1]
  if (prev) {
    router.visit(`/ui/components/${prev}`)
  }
}
</script>

<template>
  <div class="h-screen bg-background pt-3">
    <div class="container max-w-3xl mx-auto">
      <div
        class="flex justify-between items-center h-8 bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4"
      >
        <h1 class="text-2xl font-semibold text-gray-900">
          {{ $page.props.component }}
        </h1>

        <datalist id="components">
          <option v-for="component in $page.props.components" :key="component" :value="component" >
            {{ component }}
          </option>
        </datalist>

        <div class="">
          <div class="flex rounded-md shadow-sm align-middle">
            <Button @click="prev" variant="outline"
              >< {{ prevComponent() }}
            </Button>

            <Button @click="next" variant="outline">
              {{ nextComponent() }}
              >
            </Button>

            <Input
              id="search"
              type="search"
              @keypress.enter="
                router.visit(`/ui/components/${$event.target.value}`)
              "
              list="components"
              placeholder="Search for a component"
              class="border border-gray-300 rounded p-0"
            />
          </div>
        </div>

        <Button as-child variant="outline">
          <Link :href="route(`ui.index`)">Back</Link>
        </Button>

        <slot name="header" />

        <!-- components -->
      </div>

      <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <slot />
      </div>
    </div>
  </div>
</template>
