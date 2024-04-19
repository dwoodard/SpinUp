<script setup lang="ts">
import Layout from '@/Layouts/UI/Components.vue'
import { ref } from 'vue'
import {
  PinInput,
  PinInputGroup,
  PinInputInput,
} from '@/Components/ui/pin-input'

const value = ref<string[]>([])
const handleComplete = (e: string[]) => alert(e.join(''))
</script>

<template>
  <Layout>
    <div>
      <PinInput
        id="pin-input"
        v-model="value"
        placeholder="○"
        @complete="handleComplete"
      >
        <PinInputGroup>
          <PinInputInput v-for="(id, index) in 5" :key="id" :index="index" />
        </PinInputGroup>
      </PinInput>
    </div>
  </Layout>
</template>
