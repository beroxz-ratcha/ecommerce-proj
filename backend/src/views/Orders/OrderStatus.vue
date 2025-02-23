<template>
  <span
    class="text-white py-1 px-2 rounded"
    :class="{
      'bg-green-500': order.status === 'Completed',
      'bg-blue-500': order.status === 'Paid',
      'bg-orange-400': order.status === 'Shipped',
      'bg-red-600': order.status === 'Cancelled',
      'bg-gray-500': order.status === 'Unpaid',
      'bg-yellow-500': order.status === 'Delivered',
      'bg-gray-500': order.status === 'Waiting For Confirmation',
    }"
    >{{ order.status }}</span
  >
</template>

<script setup>
const { order } = defineProps({
  order: Object,
});
</script>

<style scoped></style>
