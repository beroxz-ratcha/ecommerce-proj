<template>
  <div class="bg-white p-4 rounded-lg shadow animate-fade-in-down">
    <div class="flex justify-between border-b-2 pb-3">
      <div class="flex items-center">
        <span class="whitespace-nowrap mr-3">Per Page</span>
        <select
          @change="getPayments(null)"
          v-model="perPage"
          class="appearance-none relative block w-24 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
        >
          <option value="5">5</option>
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
        <span class="ml-3">Found {{ payments.total }} Payments</span>
      </div>
      <div>
        <input
          v-model="search"
          @change="getPayments(null)"
          class="appearance-none relative block w-48 px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm"
          placeholder="พิมพ์เพื่อค้นหาข้อมูล"
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
            @click="sortPayments('id')"
          >
            Transaction ID
          </TableHeaderCell>

          <TableHeaderCell
            field="title"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            @click="sortPayments('title')"
          >
            ประเภทการชำระ
          </TableHeaderCell>
          <TableHeaderCell
            field="price"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            @click="sortPayments('price')"
          >
            ราคา
          </TableHeaderCell>
          <TableHeaderCell
            field="quantity"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            @click="sortPayments('quantity')"
          >
            สถานะรายการ
          </TableHeaderCell>
          <TableHeaderCell
            field="image"
            :sort-field="sortField"
            :sort-direction="sortDirection"
          >
            รูปภาพ
          </TableHeaderCell>
          <TableHeaderCell
            field="updated_at"
            :sort-field="sortField"
            :sort-direction="sortDirection"
            @click="sortPayments('updated_at')"
          >
            แก้ไขล่าสุดเมื่อ
          </TableHeaderCell>
          <TableHeaderCell field="actions"> การดำเนินการ </TableHeaderCell>
        </tr>
      </thead>
      <tbody v-if="payments.loading || !payments.data.length">
        <tr>
          <td colspan="6">
            <Spinner v-if="payments.loading" />
            <p v-else class="text-center py-8 text-gray-700">ไม่มีข้อมูล</p>
          </td>
        </tr>
      </tbody>
      <tbody v-else>
        <tr v-for="payment of payments.data" :key="payment.payment_trans_id">
          <td class="border-b p-2">
            {{ payment.payment_trans_id }}
          </td>

          <td
            class="border-b p-2 text-center max-w-[200px] whitespace-nowrap overflow-hidden text-ellipsis"
          >
            {{ payment.status }}
          </td>
          <td class="border-b p-2 text-center">
            {{ $filters.currencyTHB(payment.total_amount) }}
          </td>
          <td class="border-b p-2 text-center">
            <Status :payment="payment" />
          </td>
          <!-- <td class="border-b p-2 text-center">
            {{ payment.payment_status }}
          </td> -->
          <td class="border-b p-2 text-center flex justify-center items-center">
            <img
              v-if="payment.payslip_img"
              class="w-16 h-16 object-cover"
              :src="'http://localhost:8000/storage/' + payment.payslip_img"
              :alt="payment.order_id"
            />
            <img
              v-else
              class="w-16 h-16 object-cover"
              src="../../assets/noimage.png"
            />
          </td>
          <td class="border-b p-2 text-center">
            {{ formatDateTime(payment.updated_at) }}
          </td>
          <td class="border-b p-2 text-center" style="justify-items: center">
            <router-link
              :to="{
                name: 'app.payments.view',
                params: { id: payment.payment_trans_id },
              }"
              class="w-8 h-8 rounded-full text-indigo-700 border border-indigo-700 flex justify-center items-center hover:text-white hover:bg-indigo-700"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="w-4 h-4"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"
                />
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                />
              </svg>
            </router-link>
          </td>
        </tr>
      </tbody>
    </table>

    <div
      v-if="!payments.loading"
      class="flex justify-between items-center mt-5"
    >
      <div v-if="payments.data.length">
        Showing from {{ payments.from }} to {{ payments.to }}
      </div>
      <nav
        v-if="payments.total > payments.limit"
        class="relative z-0 inline-flex justify-center rounded-md shadow-sm -space-x-px"
        aria-label="Pagination"
      >
        <!-- Current: "z-10 bg-indigo-50 border-indigo-500 text-indigo-600", Default: "bg-white border-gray-300 text-gray-500 hover:bg-gray-50" -->
        <a
          v-for="(link, i) of payments.links"
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
            i === payments.links.length - 1 ? 'rounded-r-md' : '',
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
import Status from './Status.vue';
import { PAYMENTS_PER_PAGE } from '../../constants';
import TableHeaderCell from '../../components/core/Table/TableHeaderCell.vue';
import { Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';
import dateTimeService from '../../services/dateTimeService';
import {
  DotsVerticalIcon,
  PencilIcon,
  TrashIcon,
} from '@heroicons/vue/outline';

const perPage = ref(PAYMENTS_PER_PAGE);
const search = ref('');
const payments = computed(() => store.state.payments);
const sortField = ref('updated_at');
const sortDirection = ref('desc');

const payment = ref({});
const showpaymentModal = ref(false);

const emit = defineEmits(['clickEdit']);

onMounted(() => {
  getPayments();
});

function getForPage(ev, link) {
  ev.preventDefault();
  if (!link.url || link.active) {
    return;
  }

  getPayments(link.url);
}

function getPayments(url = null) {
  store.dispatch('getPayments', {
    url,
    search: search.value,
    per_page: perPage.value,
    sort_field: sortField.value,
    sort_direction: sortDirection.value,
  });
}

function sortPayments(field) {
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

  getPayments();
}

function showAddNewModal() {
  showpaymentModal.value = true;
}

function deletePayment(payment) {
  if (!confirm(`คุณแน่ใจหรือไม่ ว่าต้องการลบข้อมูลนี้ ?`)) {
    return;
  }
  store.dispatch('deletePayment', payment).then((res) => {
    store.commit('showToast', 'ลบข้อมูลเรียบร้อยแล้ว');
    store.dispatch('getPayments');
  });
}

function editPayment(p) {
  emit('clickEdit', p);
}

const formatDateTime = (dateString) => {
  return dateTimeService.formatDateTime(dateString);
};
</script>

<style scoped></style>
