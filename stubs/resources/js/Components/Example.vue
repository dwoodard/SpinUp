<script setup lang="ts">

</script>

<template>


<button>
        <div
         v-motion
          :initial="{ scale: 1 }"
           :enter="{
            scale: 1.2,
             transition: {
            repeat: Infinity,
            repeatType: 'mirror'
             } }" >
            hey there 👋
        </div>
    </button>
</template>

<style scoped>
</style>
