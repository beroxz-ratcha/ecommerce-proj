<template>
  <div class="flex items-center justify-between mb-3">
    <h1 class="text-3xl font-semibold">การตั้งค่า</h1>
    <button
      type="button"
      @click="showAddNewModal()"
      class="py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
    >
      เพิ่ม Config Setting
    </button>
  </div>
  <SettingTable @clickEdit="editSetting" />
  <SettingModal
    v-model="showSettingModal"
    :setting="settingModel"
    @close="onModalClose"
  />
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import store from '../../store';
import SettingTable from './SettingTable.vue';
import SettingModal from './SettingModal.vue';

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
