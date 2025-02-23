<x-app-layout>
    <div class="container lg:w-2/3 xl:w-2/3 mx-auto p-4">
        <!-- Header Section -->
        <div class="mb-6">
            <h1 class="text-gray-700 text-3xl font-bold">ตะกร้าสินค้า</h1>
            <nav class="mt-2">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-indigo-500">หน้าหลัก</a>
                <span class="text-gray-700">/</span>
                <span class="text-gray-700">ตะกร้าสินค้า</span>
            </nav>
        </div>

        <div x-data="{
            cartItems: {{ json_encode(
                $products->map(
                    fn($product) => [
                        'id' => $product->id,
                        'slug' => $product->slug,
                        'image' => $product->image ?: '/img/noimage.png',
                        'title' => $product->title,
                        'price' => $product->price,
                        'quantity' => $cartItems[$product->id]['quantity'],
                        'sellerId' => $product->seller_id,
                        'href' => route('product.view', $product->slug),
                        'removeUrl' => route('cart.remove', $product),
                        'updateQuantityUrl' => route('cart.update-quantity', $product),
                        'seller' => $sellers[$product->seller_id] ?? 'Unknown Seller',
                    ],
                ),
            ) }},
            get groupedCartItems() {
                const groups = {};
                this.cartItems.forEach(item => {
                    if (!groups[item.sellerId]) {
                        groups[item.sellerId] = [];
                    }
                    groups[item.sellerId].push(item);
                });
                return groups;
            },
            get cartTotal() {
                return this.cartItems.reduce((accum, next) => accum + next.price * next.quantity, 0).toFixed(2);
            },
        }" class="bg-white p-6 rounded-lg shadow-md">

            <!-- Product Items Grouped by Seller -->
            <template x-if="Object.keys(groupedCartItems).length">
                <div>
                    <template x-for="[sellerId, products] of Object.entries(groupedCartItems)" :key="sellerId">
                        <div class="border-b py-4">
                            <h2 class="text-xl font-semibold mb-4"><span x-text="products[0].seller"></span></h2>
                            <template x-for="product of products" :key="product.id">
                                <div x-data="productItem(product)" class="border-b py-4">
                                    <div class="flex flex-col sm:flex-row items-center gap-4">
                                        <a :href="product.href"
                                            class="w-36 h-32 flex items-center justify-center overflow-hidden">
                                            <img :src="product.image" class="object-cover h-full" alt="" />
                                        </a>
                                        <div class="flex flex-col justify-between flex-1">
                                            <div class="flex justify-between mb-2">
                                                <h3 x-text="product.title" class="text-lg font-semibold"></h3>
                                                <span class="text-lg font-semibold"
                                                    x-text="formatPrice(product.price)"></span>
                                            </div>
                                            {{-- <div class="text-sm text-gray-500" x-text="product.seller"></div> --}}
                                            <div class="flex justify-between items-center">
                                                <div class="flex items-center">
                                                    Qty:
                                                    <input type="number" min="1" x-model="product.quantity"
                                                        @change="changeQuantity()"
                                                        class="ml-3 py-1 border border-gray-300 rounded focus:outline-none focus:ring focus:ring-purple-600 w-16" />
                                                </div>
                                                <a href="#" @click.prevent="removeItemFromCart()"
                                                    class="text-indigo-700 hover:text-indigo-600">ลบ</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <div class="border-t border-gray-300 pt-4">
                        <div class="flex justify-between mb-2">
                            <span class="font-semibold">ยอดรวม</span>
                            <span class="text-xl" x-text="formatPrice(cartTotal)"></span>
                        </div>
                        <p class="text-gray-500 mb-6">
                            ค่าขนส่งและภาษีจะคำนวณที่หน้าชำระเงิน
                        </p>

                        <form action="{{ route('cart.checkout') }}" method="post">
                            @csrf
                            <div class="flex justify-between mb-4">
                                <span class="font-semibold">วิธีการชำระเงิน</span>
                            </div>
                            <div class="mb-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    {{-- <button type="submit" class="btn-primary w-full py-3 text-lg">
                                        Credit Card
                                    </button> --}}
                                    <a href="{{ route('payment.bank') }}"
                                        class="bg-white hover:bg-gray-100 text-gray-800 font-semibold py-2 px-4 border border-emerald-500 rounded shadow text-center w-full py-3 text-lg flex items-center justify-center gap-2">
                                        <i class="fas fa-university"></i>
                                        ชำระผ่านบัญชีธนาคาร/QR Code PromptPay
                                    </a>

                                    <a href="{{ route('cart.delivery') }}"
                                        class="bg-white hover:bg-gray-100 text-gray-800 font-semibold py-2 px-4 border border-emerald-500 rounded shadow text-center w-full py-3 text-lg flex items-center justify-center gap-2">
                                        <i class="fas fa-wallet"></i>
                                        เก็บเงินปลายทาง
                                    </a>
                                </div>
                            </div>
                        </form>
                        <a href="{{ route('shop') }}" class="inline-block mt-4 text-indigo-600 hover:underline">
                            กลับไปหน้าร้านค้า
                        </a>
                    </div>
                </div>
            </template>
            <template x-if="!Object.keys(groupedCartItems).length">
                <div class="text-center py-8 text-gray-500">
                    คุณไม่มีสินค้าในตะกร้า
                </div>
            </template>
        </div>
    </div>
</x-app-layout>

<script>
    function formatPrice(price) {
        if (price === null || price === undefined) {
            return '฿0.00';
        }
        return `฿${new Intl.NumberFormat('th-TH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(price)}`;
    }
</script>
