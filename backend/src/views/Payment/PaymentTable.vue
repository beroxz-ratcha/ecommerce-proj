<template>
  <div class="bg-white p-4 rounded-lg shadow animate-fade-in-down">
    <div class="flex justify-between border-b-2 pb-3">
      <div class="flex items-center">
        <span class="whitespace-nowrap mr-3">Per Page</span>
        <select
          @change="getSettings(null)"
          v-model="perPage"
          class="appearance-none relative block w-24 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
        >
          <option value="5">5</option>
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
        <span class="ml-3">Found {{ settings.total }} data</span>
      </div>
      <div>
        <input
          v-model="search"
          @change="getSettings(null)"
          class="appearance-none relative block w-48 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
          placeholder="พิมพ์เพื่อค้นหาผู้ใช้"
        />
      </div>
    </div>

    <table class="table-auto w-full">
      <thead>
        <tr>
          <TableHeaderCell
            field="id"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            @click="sortSettings('id')"
          >
            ID
          </TableHeaderCell>
          <TableHeaderCell
            field="key"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            @click="sortSettings('key')"
          >
            ชื่อ
          </TableHeaderCell>
          <TableHeaderCell
            field="value"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            @click="sortSettings('value')"
          >
            ข้อมูล
          </TableHeaderCell>
          <TableHeaderCell
            field="created_at"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            @click="sortSettings('created_at')"
          >
            เพิ่มเมื่อวันที่
          </TableHeaderCell>
          <TableHeaderCell
            field="updated_at"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            @click="sortSettings('updated_at')"
          >
            แก้ไขเมื่อวันที่
          </TableHeaderCell>
          <TableHeaderCell field="actions"> การดำเนินการ </TableHeaderCell>
        </tr>
      </thead>
      <tbody v-if="settings.loading || !settings.data.length">
        <tr>
          <td colspan="6">
            <Spinner v-if="settings.loading" />
            <p v-else class="text-center py-8 text-gray-700">ไม่มีผู้ใช้</p>
          </td>
        </tr>
      </tbody>
      <tbody v-else>
        <tr v-for="setting of settings.data" :key="setting.id">
          <td class="border-b p-2 text-center">{{ setting.id }}</td>
          <td class="border-b p-2 text-center">
            {{ setting.key }}
          </td>
          <td
            class="border-b p-2 text-center max-w-[200px] whitespace-nowrap overflow-hidden text-ellipsis"
          >
            {{ setting.value }}
          </td>
          <td class="border-b p-2 text-center">
            {{ formatDateTime(setting.created_at) }}
          </td>
          <td class="border-b p-2 text-center">
            {{ formatDateTime(setting.updated_at) }}
          </td>
          <td class="border-b p-2 text-center">
            <Menu as="div" class="relative inline-block text-left">
              <div>
                <MenuButton
                  class="inline-flex items-center justify-center w-full justify-center rounded-full w-10 h-10 bg-black bg-opacity-0 text-sm font-medium text-white hover:bg-opacity-5 focus:bg-opacity-5 focus:outline-none focus-visible:ring-2 focus-visible:ring-white focus-visible:ring-opacity-75"
                >
                  <DotsVerticalIcon
                    class="h-5 w-5 text-indigo-500"
                    aria-hidden="true"
                  />
                </MenuButton>
              </div>

              <transition
                enter-active-class="transition duration-100 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
                leave-active-class="transition duration-75 ease-in"
                leave-from-class="transform scale-100 opacity-100"
                leave-to-class="transform scale-95 opacity-0"
              >
                <MenuItems
                  class="absolute z-10 right-0 mt-2 w-32 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
                >
                  <div class="px-1 py-1">
                    <MenuItem v-slot="{ active }">
                      <button
                        :class="[
                          active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                          'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                        ]"
                        @click="editSetting(setting)"
                      >
                        <PencilIcon
                          :active="active"
                          class="mr-2 h-5 w-5 text-indigo-400"
                          aria-hidden="true"
                        />
                        แก้ไข
                      </button>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                      <button
                        :class="[
                          active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                          'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                        ]"
                        @click="deleteSetting(setting)"
                      >
                        <TrashIcon
                          :active="active"
                          class="mr-2 h-5 w-5 text-indigo-400"
                          aria-hidden="true"
                        />
                        ลบ
                      </button>
                    </MenuItem>
                  </div>
                </MenuItems>
              </transition>
            </Menu>
          </td>
        </tr>
      </tbody>
    </table>

    <div
      v-if="!settings.loading"
      class="flex justify-between items-center mt-5"
    >
      <div v-if="settings.data.length">
        Showing from {{ settings.from }} to {{ settings.to }}
      </div>
      <nav
        v-if="settings.total > settings.limit"
        class="relative z-0 inline-flex justify-center rounded-md shadow-sm -space-x-px"
        aria-label="Pagination"
      >
        <!-- Current: "z-10 bg-indigo-50 border-indigo-500 text-indigo-600", Default: "bg-white border-gray-300 text-gray-500 hover:bg-gray-50" -->
        <a
          v-for="(link, i) of settings.links"
          :key="i"
          :disabled="!link.url"
          href="#"
          @click="getForPage($event, link)"
          aria-current="page"
          class="relative inline-flex items-center px-4 py-2 border text-sm font-medium whitespace-nowrap"
          :class="[
            link.active
              ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
              : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
            i === 0 ? 'rounded-l-md' : '',
            i === settings.links.length - 1 ? 'rounded-r-md' : '',
            !link.url ? ' bg-gray-100 text-gray-700' : '',
          ]"
          v-html="link.label"
        >
        </a>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import store from '../../store';
import Spinner from '../../components/core/Spinner.vue';
import { SETTINGS_PER_PAGE } from '../../constants';
import TableHeaderCell from '../../components/core/Table/TableHeaderCell.vue';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import dateTimeService from '../../services/dateTimeService';
import {
  DotsVerticalIcon,
  PencilIcon,
  TrashIcon,
} from '@heroicons/vue/outline';

const perPage = ref(SETTINGS_PER_PAGE);
const search = ref('');
const settings = computed(() => store.state.settings);
const sortField = ref('updated_at');
const sortDirection = ref('desc');

const setting = ref({});
const showSettingModal = ref(false);

const emit = defineEmits(['clickEdit']);

onMounted(() => {
  getSettings();
});

function getForPage(ev, link) {
  ev.preventDefault();
  if (!link.url || link.active) {
    return;
  }

  getSettings(link.url);
}

function getSettings(url = null) {
  store.dispatch('getSettings', {
    url,
    search: search.value,
    per_page: perPage.value,
    sort_field: sortField.value,
    sort_direction: sortDirection.value,
  });
}

function sortSettings(field) {
  if (field === sortField.value) {
    if (sortDirection.value === 'desc') {
      sortDirection.value = 'asc';
    } else {
      sortDirection.value = 'desc';
    }
  } else {
    sortField.value = field;
    sortDirection.value = 'asc';
  }

  getSettings();
}

function showAddNewModal() {
  showSettingModal.value = true;
}

function deleteSetting(setting) {
  if (!confirm(`คุณแน่ใจหรือไม่ ว่าต้องการลบข้อมูลนี้ ?`)) {
    return;
  }
  store.dispatch('deleteSetting', setting).then((res) => {
    store.commit('showToast', 'ลบข้อมูลเรียบร้อยแล้ว');
    store.dispatch('getSettings');
  });
}

function editSetting(p) {
  emit('clickEdit', p);
}

const formatDateTime = (dateString) => {
  return dateTimeService.formatDateTime(dateString);
};
</script>

<style scoped></style>
