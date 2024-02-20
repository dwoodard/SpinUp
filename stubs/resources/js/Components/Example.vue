<script setup lang="ts">

</script>

<template>


<button>
         <div v-motion="{
        initial: { scale: 1 },
        enter: { scale: 1.2, transition: { repeat: Infinity, repeatType: 'mirror', ease: 'easeInOut', duration: 500 } }
            }">
            hey there 👋
        </div>


    </button>
</template>

<style scoped>
</style>
