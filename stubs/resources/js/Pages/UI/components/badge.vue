<script setup lang="ts">
import Layout from '@/Layouts/UI/Components.vue'

import { Badge } from '@/Components/ui/badge'
</script>

<template>
  <Layout>
    <Badge>Badge</Badge>
    <Badge variant="default">Default</Badge>
    <Badge variant="secondary">Secondary</Badge>
    <Badge variant="destructive">Destructive</Badge>
    <Badge variant="outline">Outline</Badge>
  </Layout>
</template>
