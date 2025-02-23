import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import { get, post } from './http.js';

Alpine.plugin(collapse);

window.Alpine = Alpine;

document.addEventListener('alpine:init', async () => {
  Alpine.data('toast', () => ({
    visible: false,
    delay: 5000,
    percent: 0,
    interval: null,
    timeout: null,
    message: null,
    type: null,
    close() {
      this.visible = false;
      clearInterval(this.interval);
    },
    show(message, type = 'success') {
      this.visible = true;
      this.message = message;
      this.type = type;

      if (this.interval) {
        clearInterval(this.interval);
        this.interval = null;
      }
      if (this.timeout) {
        clearTimeout(this.timeout);
        this.timeout = null;
      }

      this.timeout = setTimeout(() => {
        this.visible = false;
        this.timeout = null;
      }, this.delay);
      const startDate = Date.now();
      const futureDate = Date.now() + this.delay;
      this.interval = setInterval(() => {
        const date = Date.now();
        this.percent = ((date - startDate) * 100) / (futureDate - startDate);
        if (this.percent >= 100) {
          clearInterval(this.interval);
          this.interval = null;
        }
      }, 30);
    },
  }));

  Alpine.data('productItem', (product) => {
    return {
      product,
      addToCart(quantity = 1) {
        post(this.product.addToCartUrl, { quantity })
          .then((result) => {
            this.$dispatch('cart-change', { count: result.count });
            this.$dispatch('notify', {
              message: 'สินค้าถูกเพิ่มเข้าในตะกร้า',
            });
          })
          .catch((response) => {
            console.log(response);
            this.$dispatch('notify', {
              message:
                response.message ||
                'เกิดข้อผิดพลาดที่เซิร์ฟเวอร์ กรุณาลองอีกครั้ง',
              type: 'error',
            });
          });
      },
      removeItemFromCart() {
        post(this.product.removeUrl).then((result) => {
          this.$dispatch('notify', {
            message: 'สินค้าถูกนำออกจากตะกร้า',
          });
          this.$dispatch('cart-change', { count: result.count });
          this.cartItems = this.cartItems.filter((p) => p.id !== product.id);
        });
      },
      changeQuantity() {
        post(this.product.updateQuantityUrl, { quantity: product.quantity })
          .then((result) => {
            this.$dispatch('cart-change', { count: result.count });
            this.$dispatch('notify', {
              message: 'ปริมาณสินค้าถูกแก้ไข',
            });
          })
          .catch((response) => {
            this.$dispatch('notify', {
              message:
                response.message ||
                'เกิดข้อผิดพลาดที่เซิร์ฟเวอร์ กรุณาลองอีกครั้ง',
              type: 'error',
            });
          });
      },
    };
  });
});

Alpine.start();
