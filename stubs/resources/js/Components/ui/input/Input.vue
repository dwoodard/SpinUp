<script setup>
import { useAttrs } from 'vue'
import { useVModel } from '@vueuse/core'
import { cn } from '@/lib/utils'

const attrs = useAttrs()

const props = defineProps({
  defaultValue: { type: [String, Number] },
  modelValue: { type: [String, Number] },
  class: { type: null },
  placeholder: { type: String },
  label: { type: String },
  error: { type: Object },
  name: { type: String },
  disabled: { type: Boolean },
  required: { type: Boolean },
})

const emits = defineEmits(['update:modelValue'])
//unique id for input
const uniqueID = `input-${Math.random().toString(36).substring(7)}`
const modelValue = useVModel(props, 'modelValue', emits, {
  passive: true,
  defaultValue: props.defaultValue,
})
</script>

<template>
  <div
    class="p-2 my-2 rounded-lg group"
    :class="{
      'border border-red-600': props.error,
      'border border-transparent': !props.error,
    }"
    v-motion="{
      initial: {
        opacity: 0,
        y: 5,
      },
      enter: {
        opacity: 1,
        y: 0,
      },
      //   leave: {},
      //   visible: {},
      //   visibleOnce: {},
      //   hovered: {},
      //   tapped: {},
      //   focused: {},
    }"
  >
    <label
      :for="uniqueID"
      v-if="$props.label"
      class="block text-sm font-medium mb-1"
      :class="{
        'text-red-600': props.error,
      }"
    >
      {{ $props.label }}
    </label>

    <input
      :id="uniqueID"
      v-bind="attrs"
      :name="$props.name"
      ref="input"
      :placeholder="$props.placeholder"
      v-model="modelValue"
      :required="$props.required"
      :class="
        cn(
          {
            'border-red-600': props.error,
            'focus:border-gray-500 focus:ring-1 focus:ring-gray-100':
              !props.error,
          },
          props.class
        )
      "
      :disabled="props.disabled"
    />

    <p v-if="props.error" class="mt-2 text-sm text-red-600 dark:text-red-400">
      {{ props.error }}
    </p>
  </div>
</template>

<style scoped>
input {
  @apply flex
         h-10
         w-full
         rounded-md
         border
         border-gray-200
         focus:border-gray-500
         focus:ring-0
         focus:ring-gray-100
         px-3
         py-2
         text-sm
         file:border-0
         file:bg-transparent
         file:text-sm
         file:font-medium
         placeholder:text-muted-foreground
         disabled:cursor-not-allowed
         disabled:opacity-50;
}
</style>
