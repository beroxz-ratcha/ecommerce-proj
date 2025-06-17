<template>
  <div
    class="sidebar min-w-[160px] w-[160px] transition-all bg-indigo-500 text-white py-4 px-2 shadow-lg"
  >
    <h2 class="text-lg font-semibold mb-4 text-center">
      {{
        currentUser.role === 1
          ? 'Admin'
          : currentUser.role === 2
          ? 'Seller'
          : ''
      }}
    </h2>

    <router-link
      :to="{ name: 'app.dashboard' }"
      class="flex items-center rounded transition-colors hover:bg-black/30 px-2 py-2"
      active-class="bg-black/30"
    >
      <HomeIcon class="w-5 mr-2 text-gray-300" />
      <span class="text-sm">หน้าหลัก</span>
    </router-link>

    <router-link
      v-if="currentUser.role === 1"
      :to="{ name: 'app.categories' }"
      class="flex items-center rounded transition-colors hover:bg-black/30 px-2 py-2"
      active-class="bg-black/30"
    >
      <CollectionIcon class="w-5 mr-2 text-gray-300" />
      <span class="text-sm">หมวดหมู่</span>
    </router-link>

    <router-link
      v-if="currentUser.role === 2"
      :to="{ name: 'app.products' }"
      class="flex items-center rounded transition-colors hover:bg-black/30 px-2 py-2"
      active-class="bg-black/30"
    >
      <CubeIcon class="w-5 mr-2 text-gray-300" />
      <span class="text-sm">สินค้า</span>
    </router-link>

    <router-link
      v-if="currentUser.role === 2"
      :to="{ name: 'app.orders' }"
      class="flex items-center rounded transition-colors hover:bg-black/30 px-2 py-2"
      active-class="bg-black/30"
    >
      <ShoppingCartIcon class="w-5 mr-2 text-gray-300" />
      <span class="text-sm">คำสั่งซื้อ</span>
    </router-link>

    <router-link
      v-if="currentUser.role === 1"
      :to="{ name: 'app.users' }"
      class="flex items-center rounded transition-colors hover:bg-black/30 px-2 py-2"
      active-class="bg-black/30"
    >
      <UsersIcon class="w-5 mr-2 text-gray-300" />
      <span class="text-sm">ผู้ใช้ทั้งหมด</span>
    </router-link>

    <router-link
      v-if="currentUser.role === 1"
      :to="{ name: 'app.sellers' }"
      class="flex items-center rounded transition-colors hover:bg-black/30 px-2 py-2"
      active-class="bg-black/30"
    >
      <UserGroupIcon class="w-5 mr-2 text-gray-300" />
      <span class="text-sm">ผู้ขาย</span>
    </router-link>

    <router-link
      v-if="currentUser.role === 1"
      :to="{ name: 'app.customers' }"
      class="flex items-center rounded transition-colors hover:bg-black/30 px-2 py-2"
      active-class="bg-black/30"
    >
      <UserGroupIcon class="w-5 mr-2 text-gray-300" />
      <span class="text-sm">ลูกค้า</span>
    </router-link>

    <router-link
      v-if="currentUser.role === 1"
      :to="{ name: 'app.payments' }"
      class="flex items-center rounded transition-colors hover:bg-black/30 px-2 py-2"
      active-class="bg-black/30"
    >
      <CashIcon class="w-5 mr-2 text-gray-300" />
      <span class="text-sm">การชำระเงิน</span>
    </router-link>

    <router-link
      :to="{ name: 'reports.orders' }"
      class="flex items-center rounded transition-colors hover:bg-black/30 px-2 py-2"
      active-class="bg-black/30"
    >
      <ChartBarIcon class="w-5 mr-2 text-gray-300" />
      <span class="text-sm">รายงาน</span>
    </router-link>

    <router-link
      v-if="currentUser.role === 1"
      :to="{ name: 'app.settings' }"
      class="flex items-center rounded transition-colors hover:bg-black/30 px-2 py-2"
      active-class="bg-black/30"
    >
      <CogIcon class="w-5 mr-2 text-gray-300" />
      <span class="text-sm">การตั้งค่า</span>
    </router-link>
  </div>
</template>

<script setup>
import {
  HomeIcon,
  UserGroupIcon,
  UsersIcon,
  ChartBarIcon,
  CubeIcon,
  CollectionIcon,
  ShoppingCartIcon,
  CogIcon,
  CashIcon,
} from '@heroicons/vue/outline';

import store from '../store';
import { computed } from 'vue';

const currentUser = computed(() => store.state.user.data);
</script>

<style scoped>
.sidebar {
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
    0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.router-link-active {
  background-color: rgba(0, 0, 0, 0.3);
}
</style>
