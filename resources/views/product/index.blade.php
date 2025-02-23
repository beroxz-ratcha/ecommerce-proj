<?php
/** @var \Illuminate\Database\Eloquent\Collection $products */
$categoryList = \App\Models\Category::getActiveAsTree();
?>

<x-app-layout>
    <!-- Header Section -->
    <div class="container mx-auto mt-4 mb-6">
        <h1 class="text-gray-700 text-2xl font-bold">ร้านค้า</h1>
        <div class="mt-2">
            <a href="{{ route('home') }}" class="text-gray-700 hover:text-indigo-500">หน้าหลัก</a>
            <span class="text-gray-700">/</span>
            <span class="text-gray-700">ร้านค้า</span>
        </div>
    </div>
    <div class="flex gap-2 items-center p-3 pb-0" x-data="{
        selectedSort: '{{ request()->get('sort', '-updated_at') }}',
        searchKeyword: '{{ request()->get('search') }}',
        updateUrl() {
            const params = new URLSearchParams(window.location.search)
            if (this.selectedSort && this.selectedSort !== '-updated_at') {
                params.set('sort', this.selectedSort)
            } else {
                params.delete('sort')
            }
    
            if (this.searchKeyword) {
                params.set('search', this.searchKeyword)
            } else {
                params.delete('search')
            }
            window.location.href = window.location.origin + window.location.pathname + '?' +
                params.toString();
        }
    }">
        <form action="" method="GET" class="flex-1" @submit.prevent="updateUrl">
            <x-input type="text" name="search" placeholder="ค้นหาสินค้า" x-model="searchKeyword" />
        </form>
        <x-input x-model="selectedSort" @change="updateUrl" type="select" name="sort"
            class="w-full focus:border-indigo-500 focus:ring-indigo-600 border-gray-300 rounded">
            <option value="price">ราคาต่ำสุดก่อน</option>
            <option value="-price">ราคาสูงสุดก่อน</option>
            <option value="title">เรียงตามตัวอักษร: จาก A ถึง Z, จาก ก ถึง ฮ</option>
            <option value="-title">เรียงตามตัวอักษร: จาก Z ถึง A, จาก ฮ ถึง ก</option>
            <option value="-updated_at">รายการล่าสุด</option>
            <option value="updated_at">รายการเก่าสุด</option>
        </x-input>
        <x-category-list :category-list="$categoryList" />
    </div>

    <?php if ( $products->count() === 0 ): ?>
    <div class="text-center text-gray-600 py-16 text-xl">
        There are no products published
    </div>
    <?php else: ?>
    <div class="grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 p-3">
        @foreach ($products as $product)
            <!-- Product Item -->
            <div x-data="productItem({{ json_encode([
                'id' => $product->id,
                'slug' => $product->slug,
                'image' => $product->image ?: '/img/noimage.png',
                'title' => $product->title,
                'price' => $product->price,
                'addToCartUrl' => route('cart.add', $product),
            ]) }})"
                class="border border-1 border-gray-200 rounded-md transition-colors bg-white relative">
                <a href="{{ route('product.view', $product->slug) }}"
                    class="aspect-w-3 aspect-h-2 block overflow-hidden">
                    <img :src="product.image" alt=""
                        class="object-cover rounded-lg hover:scale-105 hover:rotate-1 transition-transform" />
                </a>
                <div class="p-4">
                    <h3 class="text-lg">
                        <a href="{{ route('product.view', $product->slug) }}">
                            {{ $product->title }}
                        </a>
                    </h3>
                    <h5 class="font-bold">฿{{ number_format($product->price, 2) }}</h5>
                </div>
                @if ($product->is_promotion)
                    <span
                        class="absolute top-2 right-2 bg-red-600 text-white text-xs font-bold py-1 px-3 rounded rotate-12">
                        Hot
                    </span>
                @endif
                <div class="flex justify-end py-3 px-4">
                    <button
                        class="bg-white hover:bg-gray-100 text-gray-800 font-semibold py-2 px-4 border border-emerald-500 rounded shadow flex items-center space-x-2"
                        @click="addToCart()">
                        <i class="fas fa-cart-plus mr-2"></i>
                        เพิ่มลงในตะกร้า
                    </button>
                </div>
            </div>
            <!--/ Product Item -->
        @endforeach
    </div>
    {{ $products->appends(['sort' => request('sort'), 'search' => request('search')])->links() }}
    <?php endif; ?>
</x-app-layout>
