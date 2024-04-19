<script setup lang="ts">
import Layout from '@/Layouts/UI/Components.vue'
import { Label } from '@/Components/ui/label'
import { RadioGroup, RadioGroupItem } from '@/Components/ui/radio-group'
</script>

<template>
  <Layout>
    <RadioGroup default-value="comfortable">
      <div class="flex items-center space-x-2">
        <RadioGroupItem id="r1" value="default" />
        <Label for="r1">Default</Label>
      </div>
      <div class="flex items-center space-x-2">
        <RadioGroupItem id="r2" value="comfortable" />
        <Label for="r2">Comfortable</Label>
      </div>
      <div class="flex items-center space-x-2">
        <RadioGroupItem id="r3" value="compact" />
        <Label for="r3">Compact</Label>
      </div>
    </RadioGroup>
  </Layout>
</template>
