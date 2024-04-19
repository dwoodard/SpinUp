<script setup lang="ts">
import Layout from '@/Layouts/UI/Components.vue'
import { Button } from '@/Components/ui/button'
import {
  Tooltip,
  TooltipContent,
  TooltipProvider,
  TooltipTrigger,
} from '@/Components/ui/tooltip'
</script>

<template>
  <Layout>
    <TooltipProvider>
      <Tooltip>
        <TooltipTrigger as-child>
          <Button variant="outline"> Hover </Button>
        </TooltipTrigger>
        <TooltipContent>
          <p>Add to library</p>
        </TooltipContent>
      </Tooltip>
    </TooltipProvider>
  </Layout>
</template>
