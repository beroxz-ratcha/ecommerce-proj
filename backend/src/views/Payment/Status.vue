<template>
  <span
    class="py-1 px-2 rounded"
    :class="{
      'text-white bg-green-500': payment.payment_status === 'Completed',
      'text-white bg-blue-500': payment.payment_status === 'Paid',
      'text-white bg-orange-400': payment.payment_status === 'Shipped',
      'text-white bg-red-600': payment.payment_status === 'Cancelled',
      'text-white bg-gray-500': payment.payment_status === 'Unpaid',
      'text-white bg-yellow-500': payment.payment_status === 'Delivered',
      'text-white bg-gray-500':
        payment.payment_status === 'Waiting For Confirmation',
      'text-black': ![
        'Completed',
        'Paid',
        'Shipped',
        'Cancelled',
        'Unpaid',
        'Delivered',
        'Waiting For Confirmation',
      ].includes(payment.payment_status),
    }"
    >{{ payment.payment_status }}</span
  >
</template>

<script setup>
const { payment } = defineProps({
  payment: Object,
});
</script>

<style scoped></style>
