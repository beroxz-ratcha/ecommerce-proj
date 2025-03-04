<template>
  <div class="bg-white md:h-screen">
    <div class="grid md:grid-cols-2 items-center gap-8 h-full">
      <div class="max-md:order-1 p-4 bg-gray-50 h-full">
        <img
          src="/signup.svg"
          class="lg:max-w-[90%] w-full h-full object-contain block mx-auto"
          alt="register-image"
        />
      </div>

      <div class="flex items-center p-6 h-full w-full">
        <form
          class="md:max-w-md w-full mx-auto"
          method="POST"
          @submit.prevent="register"
        >
          <div class="mb-12">
            <h3 class="text-4xl font-extrabold text-indigo-600">สร้างบัญชี</h3>
          </div>

          <div
            v-if="errorMsg"
            class="flex items-center justify-between py-3 px-5 bg-red-500 text-white rounded"
          >
            {{ errorMsg }}
            <span
              @click="errorMsg = ''"
              class="w-8 h-8 flex items-center justify-center rounded-full transition-colors cursor-pointer hover:bg-[rgba(0,0,0,0.2)]"
            >
              <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-6 w-6"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </span>
          </div>

          <div class="mt-6">
            <label class="text-gray-800 text-xs block mb-2">ชื่อ</label>
            <div class="relative flex items-center">
              <input
                id="name"
                name="name"
                type="text"
                autocomplete="name"
                required=""
                v-model="newUser.name"
                class="w-full bg-transparent text-sm border-b border-gray-300 focus:border-indigo-500 px-2 py-3 outline-none"
                placeholder="กรอกชื่อ"
              />
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="#bbb"
                stroke="#bbb"
                class="w-[18px] h-[18px] absolute right-2"
                viewBox="0 0 24 24"
              >
                <circle cx="10" cy="7" r="6" data-original="#000000"></circle>
                <path
                  d="M14 15H6a5 5 0 0 0-5 5 3 3 0 0 0 3 3h12a3 3 0 0 0 3-3 5 5 0 0 0-5-5zm8-4h-2.59l.3-.29a1 1 0 0 0-1.42-1.42l-2 2a1 1 0 0 0 0 1.42l2 2a1 1 0 0 0 1.42 0 1 1 0 0 0 0-1.42l-.3-.29H22a1 1 0 0 0 0-2z"
                  data-original="#000000"
                ></path>
              </svg>
            </div>
          </div>
          <div class="mt-6">
            <label class="text-gray-800 text-xs block mb-2">ชื่อร้านค้า</label>
            <div class="relative flex items-center">
              <input
                id="store_name"
                name="store_name"
                type="text"
                autocomplete="off"
                required=""
                v-model="newUser.store_name"
                class="w-full bg-transparent text-sm border-b border-gray-300 focus:border-indigo-500 px-2 py-3 outline-none"
                placeholder="กรอกชื่อร้านค้า"
              />
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="#bbb"
                stroke="#bbb"
                class="w-[18px] h-[18px] absolute right-2"
                viewBox="0 0 24 24"
                stroke-width="1.5"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z"
                />
              </svg>
            </div>
          </div>
          <div class="mt-6">
            <label class="text-gray-800 text-xs block mb-2"
              >หมายเลขโทรศัพท์ของร้าน</label
            >
            <div class="relative flex items-center">
              <input
                id="store_phone"
                name="store_phone"
                type="tel"
                autocomplete="tel"
                required
                v-model="newUser.store_phone"
                class="w-full bg-transparent text-sm border-b border-gray-300 focus:border-indigo-500 px-2 py-3 outline-none"
                placeholder="กรอกหมายเลขโทรศัพท์ของร้าน"
                maxlength="10"
                pattern="[0-9]{10}"
              />

              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="#bbb"
                stroke="#bbb"
                class="w-[18px] h-[18px] absolute right-2"
                viewBox="0 0 24 24"
                stroke-width="1.5"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"
                />
              </svg>
            </div>
          </div>
          <div class="mt-6">
            <label class="text-gray-800 text-xs block mb-2">อีเมล</label>
            <div class="relative flex items-center">
              <input
                id="email-address"
                name="email"
                type="email"
                autocomplete="email"
                required=""
                v-model="newUser.email"
                class="w-full bg-transparent text-sm border-b border-gray-300 focus:border-indigo-500 px-2 py-3 outline-none"
                placeholder="กรอกอีเมล"
              />
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="#bbb"
                stroke="#bbb"
                class="w-[18px] h-[18px] absolute right-2"
                viewBox="0 0 682.667 682.667"
              >
                <defs>
                  <clipPath id="a" clipPathUnits="userSpaceOnUse">
                    <path d="M0 512h512V0H0Z" data-original="#000000"></path>
                  </clipPath>
                </defs>
                <g
                  clip-path="url(#a)"
                  transform="matrix(1.33 0 0 -1.33 0 682.667)"
                >
                  <path
                    fill="none"
                    stroke-miterlimit="10"
                    stroke-width="40"
                    d="M452 444H60c-22.091 0-40-17.909-40-40v-39.446l212.127-157.782c14.17-10.54 33.576-10.54 47.746 0L492 364.554V404c0 22.091-17.909 40-40 40Z"
                    data-original="#000000"
                  ></path>
                  <path
                    d="M472 274.9V107.999c0-11.027-8.972-20-20-20H60c-11.028 0-20 8.973-20 20V274.9L0 304.652V107.999c0-33.084 26.916-60 60-60h392c33.084 0 60 26.916 60 60v196.653Z"
                    data-original="#000000"
                  ></path>
                </g>
              </svg>
            </div>
          </div>
          <div class="mt-6">
            <label class="text-gray-800 text-xs block mb-2">รหัสผ่าน</label>
            <div class="relative flex items-center">
              <input
                id="password"
                name="password"
                type="password"
                autocomplete="password"
                required=""
                v-model="newUser.password"
                class="w-full bg-transparent text-sm border-b border-gray-300 focus:border-indigo-500 px-2 py-3 outline-none"
                placeholder="กรอกรหัสผ่าน"
              />
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="#bbb"
                stroke="#bbb"
                class="w-[18px] h-[18px] absolute right-2 cursor-pointer"
                viewBox="0 0 128 128"
              >
                <path
                  d="M64 104C22.127 104 1.367 67.496.504 65.943a4 4 0 0 1 0-3.887C1.367 60.504 22.127 24 64 24s62.633 36.504 63.496 38.057a4 4 0 0 1 0 3.887C126.633 67.496 105.873 104 64 104zM8.707 63.994C13.465 71.205 32.146 96 64 96c31.955 0 50.553-24.775 55.293-31.994C114.535 56.795 95.854 32 64 32 32.045 32 13.447 56.775 8.707 63.994zM64 88c-13.234 0-24-10.766-24-24s10.766-24 24-24 24 10.766 24 24-10.766 24-24 24zm0-40c-8.822 0-16 7.178-16 16s7.178 16 16 16 16-7.178 16-16-7.178-16-16-16z"
                  data-original="#000000"
                ></path>
              </svg>
            </div>
          </div>
          <div class="mt-6">
            <label class="text-gray-800 text-xs block mb-2"
              >ยืนยันรหัสผ่าน</label
            >
            <div class="relative flex items-center">
              <input
                id="confirmPassword"
                name="confirmPassword"
                type="password"
                autocomplete="confirmPassword"
                required=""
                v-model="newUser.confirmPassword"
                class="w-full bg-transparent text-sm border-b border-gray-300 focus:border-indigo-500 px-2 py-3 outline-none"
                placeholder="กรอกรหัสผ่าน"
              />
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="#bbb"
                stroke="#bbb"
                class="w-[18px] h-[18px] absolute right-2 cursor-pointer"
                viewBox="0 0 128 128"
              >
                <path
                  d="M64 104C22.127 104 1.367 67.496.504 65.943a4 4 0 0 1 0-3.887C1.367 60.504 22.127 24 64 24s62.633 36.504 63.496 38.057a4 4 0 0 1 0 3.887C126.633 67.496 105.873 104 64 104zM8.707 63.994C13.465 71.205 32.146 96 64 96c31.955 0 50.553-24.775 55.293-31.994C114.535 56.795 95.854 32 64 32 32.045 32 13.447 56.775 8.707 63.994zM64 88c-13.234 0-24-10.766-24-24s10.766-24 24-24 24 10.766 24 24-10.766 24-24 24zm0-40c-8.822 0-16 7.178-16 16s7.178 16 16 16 16-7.178 16-16-7.178-16-16-16z"
                  data-original="#000000"
                ></path>
              </svg>
            </div>
          </div>
          <div class="mt-12">
            <button
              type="submit"
              :disabled="loading"
              class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              :class="{
                'cursor-not-allowed bg-indigo-500': loading,
                'hover:bg-indigo-700': !loading,
              }"
            >
              <template v-if="loading">
                <svg
                  class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                >
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                  ></circle>
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                  ></path>
                </svg>
                Loading...
              </template>
              <template v-else> ยืนยันการสร้างบัญชี </template>
            </button>
            <p class="text-sm mt-6 text-center text-gray-800">
              มีบัญชีอยู่แล้ว?
              <a
                href="/login"
                class="text-indigo-600 font-semibold hover:underline ml-1"
                >เข้าสู่ระบบที่นี่</a
              >
            </p>
          </div>
        </form>
      </div>
    </div>
  </div>
  <Toast />
</template>

<script setup>
import { ref } from 'vue';
import GuestLayout from '../components/GuestLayout.vue';
import Toast from '../components/core/Toast.vue';
import store from '../store';
import router from '../router';

let loading = ref(false);
let errorMsg = ref('');

const newUser = {
  name: '',
  email: '',
  password: '',
  confirmPassword: '',
  store_phone: '',
  store_name: '',
};

function register() {
  loading.value = true;

  if (newUser.password !== newUser.confirmPassword) {
    loading.value = false;
    errorMsg.value = 'Passwords do not match';
    return;
  }

  store
    .dispatch('register', newUser)
    .then((response) => {
      loading.value = false;
      if (response.status === 201) {
        store.commit('showToast', 'Registration successful');
        setTimeout(() => {
          router.push({ name: 'login' });
        }, 1500);
      }
    })
    .catch((err) => {
      loading.value = false;
      errorMsg.value = err.response.data.message;
    });
}
</script>
