<script setup lang="ts">
import { type Ref, ref } from 'vue'
import {
  type DateValue,
  getLocalTimeZone,
  today,
} from '@internationalized/date'
import { Calendar } from '@/Components/ui/calendar'
import Layout from '@/Layouts/UI/Components.vue'
const value = ref(today(getLocalTimeZone())) as Ref<DateValue>
const value2 = ref(today(getLocalTimeZone())) as Ref<DateValue>
</script>

<template>
  <Layout>
    <div class="grid grid-cols-1 md:grid-cols-2">
      <div>
        <span>
          {{
            new Date(value).toLocaleDateString('en-US', {
              weekday: 'short',
              year: 'numeric',
              month: 'short',
              day: 'numeric',
            })
          }}
        </span>
        <Calendar
          v-model="value"
          :weekday-format="'short'"
          class="rounded-md border"
        />
      </div>
      <div>
        <span>
          {{
            new Date(value2).toLocaleDateString('en-US', {
              weekday: 'short',
              year: 'numeric',
              month: 'short',
              day: 'numeric',
            })
          }}
        </span>

        <Calendar
          v-model="value2"
          :weekday-format="'short'"
          class="rounded-md border bg-forground"
        />
      </div>
    </div>
  </Layout>
</template>
