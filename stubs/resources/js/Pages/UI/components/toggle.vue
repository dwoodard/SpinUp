<script setup lang="ts">
import Layout from '@/Layouts/UI/Components.vue'
import { Bold } from 'lucide-vue-next'

import { Toggle } from '@/Components/ui/toggle'
</script>

<template>
  <Layout>
    <Toggle aria-label="Toggle italic">
      <Bold class="h-4 w-4" />
    </Toggle>
  </Layout>
</template>
