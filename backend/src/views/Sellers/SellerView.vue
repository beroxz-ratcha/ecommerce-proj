<template>
  <div v-if="seller.id" class="animate-fade-in-down">
    <form @submit.prevent="onSubmit">
      <div class="bg-white px-4 pt-5 pb-4">
        <h1 class="text-2xl font-semibold pb-2">{{ title }}</h1>
        <CustomInput
          class="mb-2"
          v-model="seller.seller_name"
          label="ชื่อ Owner"
          :errors="errors.seller_name"
        />
        <CustomInput
          class="mb-2"
          v-model="seller.store_name"
          label="ชื่อร้านค้า"
          :errors="errors.store_name"
        />
        <CustomInput
          class="mb-2"
          v-model="seller.store_phone"
          label="เบอร์ติดต่อร้านค้า"
          :errors="errors.store_phone"
        />
        <CustomInput
          class="mb-2"
          v-model="seller.store_description"
          label="คำอธิบายเพิ่มเติม"
          :errors="errors.store_description"
        />
        <CustomInput
          class="mb-2"
          v-model="seller.store_address"
          label="ที่อยู่ร้าน/ที่อยู่ที่สามารถติดต่อได้ของเจ้าของร้าน"
          :errors="errors.store_address"
        />
        <!-- <CustomInput
          class="mb-2"
          v-model="seller.store_rating"
          label="Store Rating"
          type="number"
          :errors="errors.store_rating"
        /> -->
        <CustomInput
          type="checkbox"
          class="mb-2"
          v-model="seller.status"
          label="สถานะใช้งาน"
          :errors="errors.status"
        />
      </div>

      <footer class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
        <button
          type="submit"
          class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 text-base font-medium text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500"
        >
          ยืนยัน
        </button>
        <router-link
          :to="{ name: 'app.sellers' }"
          type="button"
          class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
          ref="cancelButtonRef"
        >
          ยกเลิก
        </router-link>
      </footer>
    </form>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue';
import store from '../../store';
import { useRoute, useRouter } from 'vue-router';
import CustomInput from '../../components/core/CustomInput.vue';

const router = useRouter();
const route = useRoute();

const title = ref('');
const errors = ref({});
const seller = ref({});
const loading = ref(false);

function onSubmit() {
  loading.value = true;
  if (seller.value.id) {
    store
      .dispatch('updateSeller', seller.value)
      .then((response) => {
        loading.value = false;
        if (response.status === 200) {
          store.commit('showToast', 'Seller has been successfully updated');
          store.dispatch('getSellers');
          router.push({ name: 'app.sellers' });
        }
      })
      .catch((err) => {
        errors.value = err.response.data.errors;
        loading.value = false;
      });
  } else {
    store
      .dispatch('createSeller', seller.value)
      .then((response) => {
        loading.value = false;
        if (response.status === 201) {
          store.commit('showToast', 'Seller has been successfully created');
          store.dispatch('getSellers');
          router.push({ name: 'app.sellers' });
        }
      })
      .catch((err) => {
        errors.value = err.response.data.errors;
        loading.value = false;
      });
  }
}

onMounted(() => {
  if (route.params.id) {
    store.dispatch('getSeller', route.params.id).then(({ data }) => {
      title.value = `แก้ไขผู้ขาย : "${data.store_name}"`;
      seller.value = data;
    });
  } else {
    title.value = 'สร้างผู้ขาย';
  }
});
</script>

<style scoped></style>
