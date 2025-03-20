<template>
  <div>
    <div class="flex items-center justify-between mb-3">
      <h1 class="text-3xl font-semibold">การชำระเงิน</h1>
    </div>
    <PaymentTable @clickEdit="editSetting" />
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import store from '../../store';
import PaymentTable from './PaymentTable.vue';

const DEFAULT_PAYMENT = {};

const payments = computed(() => store.state.payments);

const paymentModel = ref({ ...DEFAULT_PAYMENT });
const showPaymentModal = ref(false);

function showAddNewModal() {
  showPaymentModal.value = true;
}

function editSetting(u) {
  paymentModel.value = u;
  showAddNewModal();
}

function onModalClose() {
  paymentModel.value = { ...DEFAULT_PAYMENT };
}
</script>

<style scoped></style>
