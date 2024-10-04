<script setup>
import { useCounterStore } from '@/stores/counter'

const counter = useCounterStore();
</script>

<template>
    <div class="counter-example">
        <h3>Counter example: </h3>
        <p>Count is: {{ counter.count }}.</p>
        <p>Double count: {{ counter.doubleCount }}</p>
        <div class="btn-group">
            <button class="btn btn-primary" @click="counter.increment">Increment</button>
            <button class="btn btn-secondary" @click="counter.reset">Reset</button>
        </div>
    </div>
</template>

<style scoped>
.counter-example {
    background-color: #f0f0f0;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
}
</style>