<script setup lang="ts">
import Layout from '@/Layouts/UI/Components.vue'
import { ref, watchEffect } from 'vue'
import { Progress } from '@/Components/ui/progress'

const progress = ref(10)

watchEffect((cleanupFn) => {
  const timer = setTimeout(() => (progress.value = 66), 1500)
  cleanupFn(() => clearTimeout(timer))
})
</script>

<template>
  <Layout>
    <Progress v-model="progress" />
  </Layout>
</template>
