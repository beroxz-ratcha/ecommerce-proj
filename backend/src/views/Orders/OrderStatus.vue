<template>
  <span
    class="py-1 px-2 rounded"
    :class="{
      'text-white bg-green-500': order.status === 'Completed',
      'text-white bg-blue-500': order.status === 'Paid',
      'text-white bg-orange-400': order.status === 'Shipped',
      'text-white bg-red-600': order.status === 'Cancelled',
      'text-white bg-gray-500': order.status === 'Unpaid',
      'text-white bg-yellow-500': order.status === 'Delivered',
      'text-white bg-gray-500': order.status === 'Waiting For Confirmation',
      'text-black': ![
        'Completed',
        'Paid',
        'Shipped',
        'Cancelled',
        'Unpaid',
        'Delivered',
        'Waiting For Confirmation',
      ].includes(order.status),
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
