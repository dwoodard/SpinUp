<script setup lang="ts">
import Layout from '@/Layouts/UI/Components.vue'

import { AspectRatio } from '@/Components/ui/aspect-ratio'
</script>

<template>
  <Layout>
    <div class="grid grid-cols-3">
      <div>
        <label> 16:9 </label>
        <AspectRatio :ratio="16 / 9" class="bg-muted">
          <img
            src="https://images.unsplash.com/photo-1588345921523-c2dcdb7f1dcd?w=800&dpr=2&q=80"
            alt="Photo by Drew Beamer"
            class="rounded-md object-cover w-full h-full"
          />
        </AspectRatio>
      </div>

      <div>
        <label> 4:3 </label>
        <AspectRatio :ratio="4 / 3" class="bg-muted">
          <img
            src="https://images.unsplash.com/photo-1588345921523-c2dcdb7f1dcd?w=800&dpr=2&q=80"
            alt="Photo by Drew Beamer"
            class="rounded-md object-cover w-full h-full"
          />
        </AspectRatio>
      </div>

      <div>
        <label>4:1</label>
        <AspectRatio :ratio="4 / 1" class="bg-muted">
          <img
            src="https://images.unsplash.com/photo-1588345921523-c2dcdb7f1dcd?w=800&dpr=2&q=80"
            alt="Photo by Drew Beamer"
            class="rounded-md object-cover w-full h-full"
          />
        </AspectRatio>
      </div>
    </div>
  </Layout>
</template>
