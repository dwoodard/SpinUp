<script setup lang="ts">
import Layout from '@/Layouts/UI/Components.vue'
import { Label } from '@/Components/ui/label'
import { Switch } from '@/Components/ui/switch'
</script>

<template>
  <Layout>
    <div class="flex items-center space-x-2">
      <Switch id="airplane-mode" />
      <Label for="airplane-mode">Airplane Mode</Label>
    </div>
  </Layout>
</template>
