<template>
  <Layout>
    <div>variant</div>
    <Button variant="default" class="mt-10" href="#">Default</Button>
    <Button variant="secondary" class="mt-10" href="#">Secondary</Button>
    <Button variant="destructive" class="mt-10" href="#">Destructive</Button>
    <Button variant="outline" class="mt-10" href="#">Outline</Button>
    <Button variant="" size="icon" href="#">
      <BellIcon aria-hidden="true" />
    </Button>
    <Button variant="link" class="mt-10" href="#">Link</Button>
    <Button variant="ghost" class="mt-10" href="#">Ghost</Button>
    <div>
      <div>size</div>
      <Button size="sm" href="#"> sm </Button>
      <Button href="#"> Default </Button>
      <Button size="lg" href="#"> lg </Button>
      <Button size="icon" href="#"> icon </Button>
    </div>
  </Layout>
</template>

<script setup>
import Layout from '@/Layouts/UI/Components.vue'
import { BellIcon } from '@heroicons/vue/24/outline'
import { Button } from '@/Components/ui/button'
</script>
