<template>
  <div v-if="order" class="p-6 bg-white shadow-md rounded-lg">
    <!--  Order Details-->
    <div>
      <h2
        class="flex justify-between items-center text-2xl font-semibold pb-4 border-b border-gray-300"
      >
        รายละเอียดการชำระเงิน
        <Status :payment="order" />
      </h2>
      <table class="w-full mt-4">
        <tbody>
          <tr>
            <td class="font-bold py-2 px-4">รหัสการทำรายงารชำระเงิน #</td>
            <td class="py-2 px-4">{{ order.payment_trans_id }}</td>
          </tr>
          <tr>
            <td class="font-bold py-2 px-4">วันที่ทำการสั่งซื้อ</td>
            <td class="py-2 px-4">{{ formatDateTime(order.created_at) }}</td>
          </tr>
          <tr>
            <td class="font-bold py-2 px-4">สถานะการชำระเงิน</td>
            <td class="py-2 px-4">
              <select
                v-model="order.payment_status"
                @change="onStatusChange"
                class="border rounded p-1"
              >
                <option
                  v-for="status of orderStatuses"
                  :value="status"
                  :key="status"
                >
                  {{ status }}
                </option>
              </select>
            </td>
          </tr>
          <tr>
            <td class="font-bold py-2 px-4">ราคารวม</td>
            <td class="py-2 px-4">
              {{ $filters.currencyTHB(order.total_amount) }}
            </td>
          </tr>
          <tr v-if="order.payslip_img">
            <td class="font-bold py-2 px-4">ใบเสร็จการชำระเงิน</td>
            <td class="py-2 px-4">
              <!-- Display the payslip image with a stylish border and shadow -->
              <img
                :src="getPayslipImgUrl(order.payslip_img)"
                alt="Payslip Image"
                class="max-w-full max-h-[300px] object-contain mx-auto rounded-lg shadow-lg border-2 border-gray-300 cursor-pointer"
                @click="openModal"
              />
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!--/  Order Details-->

    <!-- Modal for enlarged image -->
    <div
      v-if="isModalOpen"
      class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50"
      @click="closeModal"
    >
      <div class="relative">
        <img
          :src="getPayslipImgUrl(order.payslip_img)"
          alt="Payslip Image"
          class="max-w-full max-h-[80vh] object-contain rounded-lg"
        />
        <button
          @click.stop="closeModal"
          class="absolute top-2 right-2 text-white text-3xl font-bold"
        >
          ×
        </button>
      </div>
    </div>

    <!--  Payment Details-->
    <div v-if="order.payments.length > 0">
      <h2 class="text-2xl font-semibold mt-6 pb-4 border-b border-gray-300">
        รายการการชำระเงิน
      </h2>
      <table class="w-full mt-4">
        <thead>
          <tr>
            <th class="font-bold py-2 px-4">หมายเลขการชำระเงิน</th>
            <th class="font-bold py-2 px-4">รายการที่ #</th>
            <th class="font-bold py-2 px-4">จำนวนเงิน</th>
            <th class="font-bold py-2 px-4">สถานะการชำระเงิน</th>
            <th class="font-bold py-2 px-4">วันที่</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="payment in order.payments" :key="payment.id">
            <td class="py-2 px-4 text-center">
              {{ payment.payment_trans_id }}
            </td>
            <td class="py-2 px-4 text-center">
              {{ payment.order_id }}
            </td>
            <td class="py-2 px-4 text-center">
              {{ $filters.currencyTHB(payment.amount) }}
            </td>
            <td class="py-2 px-4 text-center">{{ payment.status }}</td>
            <td class="py-2 px-4 text-center">
              {{ formatDateTime(payment.created_at) }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!--/  Payment Details-->

    <!--  Customer Details-->
    <div>
      <h2 class="text-2xl font-semibold mt-6 pb-4 border-b border-gray-300">
        รายละเอียดลูกค้า
      </h2>
      <table class="w-full mt-4">
        <tbody>
          <tr>
            <td class="font-bold py-2 px-4">ชื่อ</td>
            <td class="py-2 px-4">
              {{ order.payments[0].order.customer.first_name }}
              {{ order.payments[0].order.customer.last_name }}
            </td>
          </tr>
          <tr>
            <td class="font-bold py-2 px-4">Email</td>
            <td class="py-2 px-4">
              {{ order.payments[0].order.customer.email }}
            </td>
          </tr>
          <tr>
            <td class="font-bold py-2 px-4">เบอร์โทรศัพท์</td>
            <td class="py-2 px-4">
              {{ order.payments[0].order.customer.phone }}
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <!--/  Customer Details-->

    <!--  Addresses Details-->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
      <div>
        <h2 class="text-2xl font-semibold pb-4 border-b border-gray-300">
          ที่อยู่สำหรับการเรียกเก็บเงิน
        </h2>
        <!--  Billing Address Details-->
        <div class="mt-2">
          {{ order.payments[0].order.customer.billingAddress.address1 }},
          {{ order.payments[0].order.customer.billingAddress.address2 }} <br />
          {{ order.payments[0].order.customer.billingAddress.city }},
          {{ order.payments[0].order.customer.billingAddress.zipcode }} <br />
          {{ order.payments[0].order.customer.billingAddress.state }},
          {{ order.payments[0].order.customer.billingAddress.country }} <br />
        </div>
        <!--/  Billing Address Details-->
      </div>
      <div>
        <h2 class="text-2xl font-semibold pb-4 border-b border-gray-300">
          ที่อยู่จัดส่ง
        </h2>
        <!--  Shipping Address Details-->
        <div class="mt-2">
          {{ order.payments[0].order.customer.shippingAddress.address1 }},
          {{ order.payments[0].order.customer.shippingAddress.address2 }} <br />
          {{ order.payments[0].order.customer.shippingAddress.city }},
          {{ order.payments[0].order.customer.shippingAddress.zipcode }} <br />
          {{ order.payments[0].order.customer.shippingAddress.state }},
          {{ order.payments[0].order.customer.shippingAddress.country }} <br />
        </div>
        <!--/  Shipping Address Details-->
      </div>
    </div>
    <!--/  Addresses Details-->
    <footer class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
      <router-link
        :to="{ name: 'app.payments' }"
        type="button"
        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 text-base font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500"
        ref="cancelButtonRef"
      >
        กลับ
      </router-link>
    </footer>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import store from '../../store';
import { useRoute } from 'vue-router';
import axiosClient from '../../axios.js';
import Status from './Status.vue';
import dateTimeService from '../../services/dateTimeService';

const route = useRoute();

const order = ref(null);
const orderStatuses = ref([]);
const isModalOpen = ref(false);

onMounted(() => {
  store.dispatch('getPaymentById', route.params.id).then(({ data }) => {
    order.value = data.data[0];
  });

  axiosClient
    .get(`/orders/statuses`)
    .then(({ data }) => (orderStatuses.value = data));
});

function openModal() {
  isModalOpen.value = true;
}

function closeModal() {
  isModalOpen.value = false;
}

function onStatusChange() {
  axiosClient
    .post(
      `/payments/change-status/${order.value.payment_trans_id}/${order.value.payment_status}`,
    )
    .then(({ data }) => {
      store.commit(
        'showToast',
        `เปลี่ยนสถานะการชำระเงินเป็น "${order.value.payment_status}" เรียบร้อยแล้ว`,
      );
    });
}

const formatDateTime = (dateString) => {
  return dateTimeService.formatDateTime(dateString);
};

function getPayslipImgUrl(img) {
  return `${import.meta.env.VITE_API_BASE_URL}/storage/${img}`;
}
</script>

<style scoped>
table {
  width: 100%;
  border-collapse: collapse;
}

td {
  border: 1px solid #e2e8f0;
  padding: 8px;
}

h2 {
  border-bottom: 2px solid #e2e8f0;
  padding-bottom: 8px;
}

.border-b {
  border-bottom: 2px solid #e2e8f0;
}
</style>
