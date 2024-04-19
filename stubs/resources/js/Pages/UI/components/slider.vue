<script setup lang="ts">
import Layout from '@/Layouts/UI/Components.vue'
import { cn } from '@/lib/utils'
//badge
import { Badge } from '@/Components/ui/badge'

import { Slider } from '@/Components/ui/slider'
// reactive
import { ref } from 'vue'

const modelValue = ref([33])
</script>

<template>
  <Layout>
    <!-- badge -->
    <Badge class="mb-4">{{ modelValue[0] }}</Badge>
    <Slider
      v-model="modelValue"
      :min="100"
      :max="2000"
      :step="1"
      :class="cn('w-3/3', $attrs.class ?? '')"
    />
  </Layout>
</template>
