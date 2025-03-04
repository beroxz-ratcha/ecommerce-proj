<template>
  <div class="bg-white md:h-screen">
    <div class="grid md:grid-cols-2 items-center gap-8 h-full">
      <div class="max-md:order-1 p-4 bg-gray-50 h-full">
        <img
          src="/signin.svg"
          class="lg:max-w-[90%] w-full h-full object-contain block mx-auto"
          alt="login-image"
        />
      </div>

      <div class="flex items-center p-6 h-full w-full">
        <form
          class="md:max-w-md w-full mx-auto"
          method="POST"
          @submit.prevent="login"
        >
          <div class="mb-12">
            <h3 class="text-3xl font-extrabold text-indigo-600">
              Welcome to the <br />
              Admin & Seller Portal
            </h3>
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
          <input type="hidden" name="remember" value="true" />

          <div class="mt-8">
            <div class="relative flex items-center">
              <input
                id="email-address"
                name="email"
                type="email"
                autocomplete="email"
                required=""
                v-model="user.email"
                class="w-full text-sm border-b border-gray-300 focus:border-indigo-600 px-2 py-3 outline-none"
                placeholder="อีเมล"
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

          <div class="mt-8">
            <div class="relative flex items-center">
              <input
                id="password"
                name="password"
                :type="isPasswordVisible ? 'text' : 'password'"
                autocomplete="current-password"
                required
                v-model="user.password"
                class="w-full text-sm border-b border-gray-300 focus:border-indigo-600 px-2 py-3 outline-none"
                placeholder="รหัสผ่าน"
              />
              <svg
                v-if="isPasswordVisible"
                xmlns="http://www.w3.org/2000/svg"
                fill="#bbb"
                stroke="#bbb"
                class="w-[18px] h-[18px] absolute right-2 cursor-pointer"
                viewBox="0 0 16 16"
                @click="isPasswordVisible = !isPasswordVisible"
              >
                <!-- ไอคอน "eye-slash" -->
                <path
                  d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7 7 0 0 0-2.79.588l.77.771A6 6 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755q-.247.248-.517.486z"
                  stroke-width="0.25"
                />
                <path
                  d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829"
                  stroke-width="0.25"
                />
                <path
                  d="M3.35 5.47q-.27.24-.518.487A13 13 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7 7 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12z"
                  stroke-width="0.25"
                />
              </svg>

              <svg
                v-else
                xmlns="http://www.w3.org/2000/svg"
                fill="#bbb"
                stroke="#bbb"
                class="w-[18px] h-[18px] absolute right-2 cursor-pointer"
                viewBox="0 0 128 128"
                @click="isPasswordVisible = !isPasswordVisible"
              >
                <!-- ไอคอน "eye" -->
                <path
                  d="M64 104C22.127 104 1.367 67.496.504 65.943a4 4 0 0 1 0-3.887C1.367 60.504 22.127 24 64 24s62.633 36.504 63.496 38.057a4 4 0 0 1 0 3.887C126.633 67.496 105.873 104 64 104zM8.707 63.994C13.465 71.205 32.146 96 64 96c31.955 0 50.553-24.775 55.293-31.994C114.535 56.795 95.854 32 64 32 32.045 32 13.447 56.775 8.707 63.994zM64 88c-13.234 0-24-10.766-24-24s10.766-24 24-24 24 10.766 24 24-10.766 24-24 24zm0-40c-8.822 0-16 7.178-16 16s7.178 16 16 16 16-7.178 16-16-7.178-16-16-16z"
                  data-original="#000000"
                ></path>
              </svg>
            </div>
          </div>

          <div class="flex flex-wrap items-center justify-between gap-4 mt-6">
            <div class="flex items-center">
              <!-- <input
                id="remember-me"
                name="remember-me"
                type="checkbox"
                v-model="user.remember"
                class="h-4 w-4 shrink-0 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
              <label for="remember-me" class="text-gray-800 ml-3 block text-sm">
                Remember me
              </label> -->
            </div>
            <div class="text-sm">
              <router-link
                :to="{ name: 'requestPassword' }"
                class="text-indigo-500 text-sm hover:underline"
              >
                ลืมรหัสผ่านใช่ไหม?
              </router-link>
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
              <template v-else> เข้าสู่ระบบ </template>
            </button>
            <p class="text-gray-800 text-sm text-center mt-6">
              ถ้ายังไม่มีบัญชี?
              <a
                href="/register"
                class="text-indigo-600 font-semibold hover:underline ml-1 whitespace-nowrap"
                >ลงทะเบียนที่นี่</a
              >
            </p>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import GuestLayout from '../components/GuestLayout.vue';
import store from '../store';
import router from '../router';

let loading = ref(false);
let errorMsg = ref('');

let isPasswordVisible = ref(false);

const user = {
  email: '',
  password: '',
  remember: false,
};

function login() {
  loading.value = true;
  store
    .dispatch('login', user)
    .then(() => {
      loading.value = false;
      router.push({ name: 'app.dashboard' });
    })
    .catch(({ response }) => {
      loading.value = false;
      errorMsg.value = response.data.message;
    });
}
</script>
