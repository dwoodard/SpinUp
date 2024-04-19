<script setup lang="ts">
import Layout from '@/Layouts/UI/Components.vue'
import { Checkbox } from '@/Components/ui/checkbox'
import { Label } from '@/Components/ui/label'
</script>

<template>
  <Layout>
    <div>
      <div class="flex items-center space-x-2">
        <Checkbox id="terms" />
        <Label for="terms">Accept terms and conditions</Label>
      </div>
    </div>
  </Layout>
</template>
