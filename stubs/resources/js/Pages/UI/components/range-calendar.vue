<script setup lang="ts">
import Layout from '@/Layouts/UI/Components.vue'
import { type Ref, ref } from 'vue'
import type { DateRange } from 'radix-vue'
import { getLocalTimeZone, today } from '@internationalized/date'
import { RangeCalendar } from '@/Components/ui/range-calendar'

const start = today(getLocalTimeZone())
const end = start.add({ days: 7 })

const value = ref({
  start,
  end,
}) as Ref<DateRange>
</script>

<template>
  <Layout>
    <div class="grid grid-cols-2">
      <RangeCalendar v-model="value" class="rounded-md border" />
      <div>
        <pre class="text-xs">{{ JSON.stringify(value, null, 2) }}</pre>
      </div>
    </div>
  </Layout>
</template>
