import { createRouter, createWebHistory } from 'vue-router';
import AppLayout from '../components/AppLayout.vue';
import Login from '../views/Login.vue';
import Register from '../views/Register.vue';
import Dashboard from '../views/Dashboard.vue';
import Products from '../views/Products/Products.vue';
import Users from '../views/Users/Users.vue';
import Customers from '../views/Customers/Customers.vue';
import CustomerView from '../views/Customers/CustomerView.vue';
import Sellers from '../views/Sellers/Sellers.vue';
import SellerView from '../views/Sellers/SellerView.vue';
import Orders from '../views/Orders/Orders.vue';
import OrderView from '../views/Orders/OrderView.vue';
import RequestPassword from '../views/RequestPassword.vue';
import ResetPassword from '../views/ResetPassword.vue';
import NotFound from '../views/NotFound.vue';
import store from '../store';
import Report from '../views/Reports/Report.vue';
import OrdersReport from '../views/Reports/OrdersReport.vue';
import CustomersReport from '../views/Reports/CustomersReport.vue';
import ProductForm from '../views/Products/ProductForm.vue';
import Categories from '../views/Categories/Categories.vue';
import ProfileSeller from '../views/Profile/ProfileSeller.vue';
import ProfileAdmin from '../views/Profile/ProfileAdmin.vue';
import Setting from '../views/Setting/Setting.vue';
import Payment from '../views/Payment/Payment.vue';

const routes = [
  {
    path: '/',
    redirect: '/app',
  },
  {
    path: '/app',
    name: 'app',
    redirect: '/app/dashboard',
    component: AppLayout,
    meta: {
      requiresAuth: true,
    },
    children: [
      {
        path: 'dashboard',
        name: 'app.dashboard',
        component: Dashboard,
      },
      {
        path: 'products',
        name: 'app.products',
        component: Products,
        meta: { requiresAuth: true, roles: '2' },
      },
      {
        path: 'categories',
        name: 'app.categories',
        component: Categories,
        meta: { requiresAuth: true, roles: '1' },
      },
      {
        path: 'products/create',
        name: 'app.products.create',
        component: ProductForm,
        meta: { requiresAuth: true, roles: '2' },
      },
      {
        path: 'products/:id',
        name: 'app.products.edit',
        component: ProductForm,
        meta: { requiresAuth: true, roles: '2' },
        props: {
          id: (value) => /^\d+$/.test(value),
        },
      },
      {
        path: 'users',
        name: 'app.users',
        component: Users,
        meta: { requiresAuth: true, roles: '1' },
      },
      {
        path: 'setting',
        name: 'app.setting',
        component: Setting,
        meta: { requiresAuth: true, roles: '1' },
      },
      {
        path: 'payment',
        name: 'app.payment',
        component: Payment,
        meta: { requiresAuth: true, roles: '1' },
      },
      {
        path: 'customers',
        name: 'app.customers',
        component: Customers,
        meta: { requiresAuth: true, roles: '1' },
      },
      {
        path: 'customers/:id',
        name: 'app.customers.view',
        component: CustomerView,
        meta: { requiresAuth: true, roles: '1' },
      },
      {
        path: 'sellers',
        name: 'app.sellers',
        component: Sellers,
        meta: { requiresAuth: true, roles: '1' },
      },
      {
        path: 'sellers/:id',
        name: 'app.sellers.view',
        component: SellerView,
        meta: { requiresAuth: true, roles: '1' },
      },
      {
        path: 'sellers/profile',
        name: 'app.sellers.profile',
        component: ProfileSeller,
        meta: { requiresAuth: true, roles: '2' },
      },
      {
        path: 'orders',
        name: 'app.orders',
        component: Orders,
        meta: { requiresAuth: true, roles: '2' },
      },
      {
        path: 'orders/:id',
        name: 'app.orders.view',
        component: OrderView,
        meta: { requiresAuth: true, roles: '2' },
      },
      {
        path: '/report',
        name: 'reports',
        component: Report,
        meta: {
          requiresAuth: true,
        },
        children: [
          {
            path: 'orders/:date?',
            name: 'reports.orders',
            component: OrdersReport,
          },
          {
            path: 'customers/:date?',
            name: 'reports.customers',
            component: CustomersReport,
          },
        ],
      },
    ],
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: {
      requiresGuest: true,
    },
  },
  {
    path: '/register',
    name: 'register',
    component: Register,
    meta: {
      requiresGuest: true,
    },
  },
  {
    path: '/request-password',
    name: 'requestPassword',
    component: RequestPassword,
    meta: {
      requiresGuest: true,
    },
  },
  {
    path: '/reset-password/:token',
    name: 'resetPassword',
    component: ResetPassword,
    meta: {
      requiresGuest: true,
    },
  },
  {
    path: '/notfound',
    name: '404',
    component: NotFound,
  },
  {
    path: '/:pathMatch(.*)',
    name: 'notfound',
    component: NotFound,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !store.state.user.token) {
    next({ name: 'login' });
  } else if (to.meta.requiresGuest && store.state.user.token) {
    next({ name: 'app.dashboard' });
  } else if (to.meta.roles) {
    const userRole = sessionStorage.getItem('role');
    if (to.meta.roles !== userRole) {
      console.error('Access denied to this route');
      next({ name: '404' });
    } else {
      next();
    }
  } else {
    next();
  }
});

export default router;
