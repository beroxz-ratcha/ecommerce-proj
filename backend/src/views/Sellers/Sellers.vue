<template>
  <div class="flex items-center justify-between mb-3">
    <h1 class="text-3xl font-semibold">ข้อมูลผู้ขาย</h1>
  </div>
  <SellersTable @clickEdit="editSeller" />
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import store from '../../store';
import SellersTable from './SellersTable.vue';

const DEFAULT_SELLER = {};

const sellers = computed(() => store.state.sellers);

const sellerModel = ref({ ...DEFAULT_SELLER });
const showSellerModal = ref(false);

function showAddNewModal() {
  showSellerModal.value = true;
}

function editSeller(c) {
  store.dispatch('getSeller', c.id).then(({ data }) => {
    sellerModel.value = data;
    showAddNewModal();
  });
}

function onModalClose() {
  sellerModel.value = { ...DEFAULT_SELLER };
}
</script>

<style scoped></style>
