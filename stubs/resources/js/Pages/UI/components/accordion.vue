<script setup lang="ts">
import Layout from '@/Layouts/UI/Components.vue'
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from '@/Components/ui/accordion'

const accordionItems = [
  {
    value: 'item-1',
    title: 'Is it accessible?',
    content: 'Yes. It adheres to the WAI-ARIA design pattern.',
  },
  {
    value: 'item-2',
    title: 'Is it unstyled?',
    content:
      "Yes. It's unstyled by default, giving you freedom over the look and feel.",
  },
  {
    value: 'item-3',
    title: 'Can it be animated?',
    content: 'Yes! You can use the transition prop to configure the animation.',
  },
]
</script>

<template>
  <Layout>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-10">
      <div class="">
        <Accordion type="single" collapsible>
          <AccordionItem v-for="i in 3" :key="i" :value="`item-${i}`">
            <AccordionTrigger>{{ i }}. Item</AccordionTrigger>
            <AccordionContent>
              Yes. It adheres to the WAI-ARIA design pattern.
            </AccordionContent>
          </AccordionItem>
        </Accordion>
      </div>

      <Accordion
        type="single"
        class="w-full"
        collapsible
        defaultActive="item-1"
      >
        <AccordionItem
          v-for="item in accordionItems"
          :key="item.value"
          :value="item.value"
        >
          <AccordionTrigger>{{ item.title }}</AccordionTrigger>
          <AccordionContent>
            {{ item.content }}
          </AccordionContent>
        </AccordionItem>
      </Accordion>
    </div>
  </Layout>
</template>
