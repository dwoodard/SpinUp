<script setup lang="ts">
import Layout from '@/Layouts/UI/Components.vue'
import { Button } from '@/Components/ui/button'
import { useToast } from '@/Components/ui/toast/use-toast'
import { Toaster } from '@/Components/ui/toast'

const { toast } = useToast()
</script>

<template>
  <Layout>
    <Toaster />

    <Button
      @click="
        () => {
          toast({
            title: 'Scheduled: Catch up',
            description: 'Friday, February 10, 2023 at 5:57 PM',
          })
        }
      "
    >
      Add to calendar
    </Button>
  </Layout>
</template>
