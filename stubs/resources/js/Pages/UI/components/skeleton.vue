<script setup lang="ts">
import Layout from '@/Layouts/UI/Components.vue'
import { Skeleton } from '@/Components/ui/skeleton'
</script>

<template>
  <Layout>
    <div class="flex items-center space-x-4 bg-slate-500 p-4">
      <Skeleton class="h-12 w-12 rounded-full" />
      <div class="space-y-2">
        <Skeleton class="h-4 w-[250px]" />
        <Skeleton class="h-4 w-[200px]" />
      </div>
    </div>
  </Layout>
</template>
