<script setup lang="ts">
import Layout from '@/Layouts/UI/Components.vue'
import { toast } from 'vue-sonner'
import { Toaster } from '@/Components/ui/sonner'

import { Button } from '@/Components/ui/button'
</script>

<template>
  <Layout>
    <Toaster
      :duration="5000"
      :max="5"
      :transition="true"
      :transitionDuration="1200"
    />

    <Button
      variant="outline"
      @click="
        () => {
          toast('Event has been created', {
            description: 'Sunday, December 03, 2023 at 9:00 AM',
            action: {
              label: 'Undo',
              onClick: () => console.log('Undo'),
            },
          })
        }
      "
    >
      Add to calendar
    </Button>
  </Layout>
</template>
