<script setup lang="ts">
import Layout from '@/Layouts/UI/Components.vue'
import { Bold, Italic, Underline } from 'lucide-vue-next'

import { ToggleGroup, ToggleGroupItem } from '@/Components/ui/toggle-group'
</script>

<template>
  <Layout>
    <ToggleGroup type="multiple">
      <ToggleGroupItem value="bold" aria-label="Toggle bold">
        <Bold class="h-4 w-4" />
      </ToggleGroupItem>
      <ToggleGroupItem value="italic" aria-label="Toggle italic">
        <Italic class="h-4 w-4" />
      </ToggleGroupItem>
      <ToggleGroupItem value="underline" aria-label="Toggle underline">
        <Underline class="h-4 w-4" />
      </ToggleGroupItem>
    </ToggleGroup>
  </Layout>
</template>
