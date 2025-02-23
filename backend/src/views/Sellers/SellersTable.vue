<template>
  <div class="bg-white p-4 rounded-lg shadow animate-fade-in-down">
    <div class="flex justify-between border-b-2 pb-3">
      <div class="flex items-center">
        <span class="whitespace-nowrap mr-3">Per Page</span>
        <select
          @change="getSellers(null)"
          v-model="perPage"
          class="appearance-none relative block w-24 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
        >
          <option value="5">5</option>
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
        <span class="ml-3">Found {{ sellers.total }} sellers</span>
      </div>
      <div>
        <input
          v-model="search"
          @change="getSellers(null)"
          class="appearance-none relative block w-48 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
          placeholder="พิมพ์เพื่อค้นหาผู้ขาย"
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
            @click="sortSellers('id')"
          >
            ID
          </TableHeaderCell>
          <TableHeaderCell
            field="store_name"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            @click="sortSellers('store_name')"
          >
            ชื่อร้านค้า
          </TableHeaderCell>
          <TableHeaderCell
            field="seller_name"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            @click="sortSellers('seller_name')"
          >
            ชื่อ Owner
          </TableHeaderCell>
          <TableHeaderCell
            field="store_phone"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            @click="sortSellers('store_phone')"
          >
            เบอร์ติดต่อร้านค้า
          </TableHeaderCell>
          <!-- <TableHeaderCell
            field="store_rating"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            @click="sortSellers('store_rating')"
          >
            Store Rating
          </TableHeaderCell> -->
          <TableHeaderCell
            field="status"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            @click="sortSellers('status')"
          >
            สถานะใช้งาน
          </TableHeaderCell>
          <TableHeaderCell
            field="created_at"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            @click="sortSellers('created_at')"
          >
            วันที่สมัครเข้าใช้งาน
          </TableHeaderCell>
          <TableHeaderCell field="actions"> การดำเนินการ </TableHeaderCell>
        </tr>
      </thead>
      <tbody v-if="sellers.loading || !sellers.data.length">
        <tr>
          <td colspan="8">
            <Spinner v-if="sellers.loading" />
            <p v-else class="text-center py-8 text-gray-700">ไม่มีผู้ขาย</p>
          </td>
        </tr>
      </tbody>
      <tbody v-else>
        <tr v-for="(seller, index) of sellers.data" :key="seller.id">
          <td class="border-b p-2 text-center">{{ seller.id }}</td>
          <td class="border-b p-2 text-center">{{ seller.store_name }}</td>
          <td class="border-b p-2 text-center">{{ seller.seller_name }}</td>
          <td class="border-b p-2 text-center">{{ seller.store_phone }}</td>
          <!-- <td class="border-b p- text-center">{{ seller.store_rating }}</td> -->
          <td class="border-b p-2 text-center">{{ seller.status }}</td>
          <td class="border-b p-2 text-center">{{ seller.created_at }}</td>
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
                      <router-link
                        :to="{
                          name: 'app.sellers.view',
                          params: { id: seller.id },
                        }"
                        :class="[
                          active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                          'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                        ]"
                      >
                        <PencilIcon
                          :active="active"
                          class="mr-2 h-5 w-5 text-indigo-400"
                          aria-hidden="true"
                        />
                        แก้ไข
                      </router-link>
                    </MenuItem>
                    <MenuItem v-slot="{ active }">
                      <button
                        :class="[
                          active ? 'bg-indigo-600 text-white' : 'text-gray-900',
                          'group flex w-full items-center rounded-md px-2 py-2 text-sm',
                        ]"
                        @click="deleteSeller(seller)"
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

    <div v-if="!sellers.loading" class="flex justify-between items-center mt-5">
      <div v-if="sellers.data.length">
        Showing from {{ sellers.from }} to {{ sellers.to }}
      </div>
      <nav
        v-if="sellers.total > sellers.limit"
        class="relative z-0 inline-flex justify-center rounded-md shadow-sm -space-x-px"
        aria-label="Pagination"
      >
        <!-- Current: "z-10 bg-indigo-50 border-indigo-500 text-indigo-600", Default: "bg-white border-gray-300 text-gray-500 hover:bg-gray-50" -->
        <a
          v-for="(link, i) of sellers.links"
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
            i === sellers.links.length - 1 ? 'rounded-r-md' : '',
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
import { SELLERS_PER_PAGE } from '../../constants';
import TableHeaderCell from '../../components/core/Table/TableHeaderCell.vue';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import {
  DotsVerticalIcon,
  PencilIcon,
  TrashIcon,
} from '@heroicons/vue/outline';

const perPage = ref(SELLERS_PER_PAGE);
const search = ref('');
const sellers = computed(() => store.state.sellers);
const sortField = ref('updated_at');
const sortDirection = ref('desc');

const seller = ref({});
const showSellerModal = ref(false);

const emit = defineEmits(['clickEdit']);

onMounted(() => {
  getSellers();
});

function getForPage(ev, link) {
  ev.preventDefault();
  if (!link.url || link.active) {
    return;
  }

  getSellers(link.url);
}

function getSellers(url = null) {
  store.dispatch('getSellers', {
    url,
    search: search.value,
    per_page: perPage.value,
    sort_field: sortField.value,
    sort_direction: sortDirection.value,
  });
}

function sortSellers(field) {
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

  getSellers();
}

function showAddNewModal() {
  showSellerModal.value = true;
}

function deleteSeller(seller) {
  if (!confirm(`คุณแน่ใจหรือไม่ว่าต้องการลบผู้ขายนี้ ?`)) {
    return;
  }
  store.dispatch('deleteSeller', seller).then((res) => {
    store.commit('showToast', 'ลบผู้ขายเรียบร้อยแล้ว');
    store.dispatch('getSellers');
  });
}
</script>

<style scoped></style>
