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

    <NavLink
      v-if="currentUser.role === 1"
      to="app.dashboard"
      :icon="HomeIcon"
      text="หน้าหลัก"
    />
    <NavLink
      v-if="currentUser.role === 2"
      to="app.dashboard"
      :icon="HomeIcon"
      text="หน้าหลัก"
    />
    <NavLink
      v-if="currentUser.role === 1"
      to="app.categories"
      :icon="CollectionIcon"
      text="หมวดหมู่"
    />
    <NavLink
      v-if="currentUser.role === 2"
      to="app.products"
      :icon="CubeIcon"
      text="สินค้า"
    />
    <NavLink
      v-if="currentUser.role === 2"
      to="app.orders"
      :icon="ShoppingCartIcon"
      text="คำสั่งซื้อ"
    />
    <NavLink
      v-if="currentUser.role === 1"
      to="app.users"
      :icon="UsersIcon"
      text="ผู้ใช้ทั้งหมด"
    />
    <NavLink
      v-if="currentUser.role === 1"
      to="app.sellers"
      :icon="UserGroupIcon"
      text="ผู้ขาย"
    />
    <NavLink
      v-if="currentUser.role === 1"
      to="app.customers"
      :icon="UserGroupIcon"
      text="ลูกค้า"
    />
    <NavLink
      v-if="currentUser.role === 1"
      to="app.payments"
      :icon="CashIcon"
      text="การชำระเงิน"
    />
    <NavLink to="reports.orders" :icon="ChartBarIcon" text="รายงาน" />
    <NavLink
      v-if="currentUser.role === 1"
      to="app.settings"
      :icon="CogIcon"
      text="การตั้งค่า"
    />
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

const NavLink = {
  props: ['to', 'icon', 'text'],
  template: `
    <router-link
      :to="{ name: to }"
      class="flex items-center rounded transition-colors hover:bg-black/30"
      style="padding: 10px"
      active-class="bg-black/30"
    >
      <span class="mr-2 text-gray-300">
        <component :is="icon" class="w-5" />
      </span>
      <span class="text-sm">{{ text }}</span>
    </router-link>
  `,
};
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
