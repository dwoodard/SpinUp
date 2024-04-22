<script setup lang="ts">
import Layout from '@/Layouts/UI/Components.vue'
import { Input } from '@/Components/ui/input'
import { toTitleCase } from '@/lib/helper'
import { ref, reactive } from 'vue'
import { vMaska } from 'maska'

const inputs = [
  // 'button',
  // 'checkbox',
  'color',
  'date',
  'datetime-local',
  'email',
  'file',
  'hidden',
  'image',
  'month',
  'number',
  'password',
  // 'radio',
  // 'range',
  'reset',
  'search',
  'submit',
  'tel',
  'text',
  'time',
  'url',
  'week',
]
</script>

<template>
  <Layout>
    <form>
      <div class="grid grid-cols-2">
        <Input
          required
          :error="'Please check your input.'"
          help-text="Please enter your phone number."
          label="phone"
          v-maska
          @maska="onMaska"
          data-maska="###-###-####"
          type="tel"
          placeholder="Phone"
        />

        <Input
          label="Email"
          type="email"
          placeholder="Email"
          pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$"
          help-text="Please enter a valid email address."
        />
      </div>
      <div class="grid">
        <div v-for="input in inputs">
          <Input :type="input" :label="input" />
        </div>
      </div>
    </form>
  </Layout>
</template>
