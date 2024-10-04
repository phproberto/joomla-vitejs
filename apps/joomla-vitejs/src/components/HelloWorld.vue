<script setup>
import CounterExample from './CounterExample.vue'
import { useCounterStore } from '@/stores/counter'
defineProps({
  msg: {
    type: String,
    required: true
  }
});

const counter = useCounterStore();
</script>

<template>
  <div class="greetings">
    <h1 class="green">{{ msg }}</h1>
    <h3>
      You’ve successfully created a project with
      <a href="https://vitejs.dev/" target="_blank" rel="noopener">Vite</a> +
      <a href="https://vuejs.org/" target="_blank" rel="noopener">Vue 3</a>.
    </h3>
    <CounterExample />
  </div>
</template>

