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

const DEFAULT_SETTING = {};

const settings = computed(() => store.state.settings);

const settingModel = ref({ ...DEFAULT_SETTING });
const showSettingModal = ref(false);

function showAddNewModal() {
  showSettingModal.value = true;
}

function editSetting(u) {
  settingModel.value = u;
  showAddNewModal();
}

function onModalClose() {
  settingModel.value = { ...DEFAULT_SETTING };
}
</script>

<style scoped></style>
