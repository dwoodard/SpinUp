<script setup>
// define props
import { defineProps } from 'vue'

import { Button } from '@/Components/ui/button'
import {
  Card,
  CardContent,
  CardDescription,
  CardFooter,
  CardHeader,
  CardTitle,
} from '@/Components/ui/card'
// inertia vue 3 Link
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  components: {
    type: Array,
    required: true,
  },
})
</script>

<template>
  <div class="container">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">UI Components</h1>

    <div class="container">
      <h2>shadcn-vue</h2>
      <!-- grid-auto-flow: column; -->
      <div
        :key="component"
        class="grid grid-cols-1 gap-4 sm:grid-cols-3 md:grid-cols-4"
      >
        <Button as-child variant="outline" v-for="component in components">
          <Link :href="route(`ui.${component}`)">{{ component }}</Link>
        </Button>
      </div>
    </div>
  </div>
</template>

<
