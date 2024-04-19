<script setup lang="ts">
import Layout from '@/Layouts/UI/Components.vue'

import { Terminal } from 'lucide-vue-next'
import { Alert, AlertDescription, AlertTitle } from '@/Components/ui/alert'
</script>

<template>
  <Layout>
    <Alert>
      <Terminal class="h-4 w-4" />
      <AlertTitle>Heads up!</AlertTitle>
      <AlertDescription>
        You can add components to your app using the cli.
      </AlertDescription>
    </Alert>
  </Layout>
</template>
