<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar'
import Layout from '@/Layouts/UI/Components.vue'
</script>

<template>
  <Layout>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-1 md:gap-4">
      <Avatar>
        <AvatarImage src="https://github.com/radix-vue.png" alt="@radix-vue" />
        <AvatarFallback>CN</AvatarFallback>
      </Avatar>

      <Avatar v-for="n in 3" :key="n">
        <AvatarImage src="#" alt="@radix-vue" />
        <AvatarFallback>FB</AvatarFallback>
      </Avatar>
    </div>
  </Layout>
</template>
