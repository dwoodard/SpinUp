<script setup lang="ts">
import Layout from '@/Layouts/UI/Components.vue'
import { ScrollArea } from '@/Components/ui/scroll-area'
import { Separator } from '@/Components/ui/separator'

const tags = Array.from({ length: 5000 }).map(
  (_, i, a) => `v1.2.0-beta.${a.length - i}`
)
</script>

<template>
  <Layout>
    <ScrollArea class="h-72 w-64 rounded-md border">
      <div class="p-4">
        <h4 class="mb-4 text-sm font-medium leading-none">Tags</h4>
        <div v-for="tag in tags" :key="tag">
          <div class="text-sm">
            {{ tag }}
          </div>
          <Separator class="my-2" />
        </div>
      </div>
    </ScrollArea>
  </Layout>
</template>
