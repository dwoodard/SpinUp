<script setup lang="ts">
import Layout from '@/Layouts/UI/Components.vue'
import { ref } from 'vue'
import {
  TagsInput,
  TagsInputInput,
  TagsInputItem,
  TagsInputItemDelete,
  TagsInputItemText,
} from '@/Components/ui/tags-input'

const modelValue = ref(['Apple', 'Banana'])
</script>

<template>
  <Layout>
    <TagsInput v-model="modelValue">
      <TagsInputItem v-for="item in modelValue" :key="item" :value="item">
        <TagsInputItemText />
        <TagsInputItemDelete />
      </TagsInputItem>
      <TagsInputInput placeholder="Tags..." />
    </TagsInput>
  </Layout>
</template>
