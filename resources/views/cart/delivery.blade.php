<x-app-layout>
    <!-- Header Section -->
    <div class="container mx-auto my-10 px-4">
        <h1 class="text-3xl font-bold text-gray-700 mb-6">รายละเอียดการจัดส่ง</h1>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Your Order Details Panel -->
            <div class="w-full bg-white rounded-lg shadow p-4">
                <h2 class="text-xl font-semibold mb-4 text-gray-800">รายละเอียดคำสั่งซื้อของคุณ</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-left table-auto">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-gray-600 font-medium">สินค้า</th>
                                <th class="px-4 py-2 text-gray-600 font-medium text-center">จำนวน</th>
                                <th class="px-4 py-2 text-gray-600 font-medium text-right">ราคารวม</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                @php
                                    // Calculate the total for each product
                                    $quantity = $orderItems[$product->id]['quantity'];
                                    $total = $product->price * $quantity;
                                @endphp
                                <tr class="border-b">
                                    <td class="px-4 py-2 flex items-center">
                                        <!-- Image Container -->
                                        <div class="w-36 h-32 flex items-center justify-center overflow-hidden">
                                            <img src="{{ $product->image }}" class="object-cover h-full" alt="">
                                        </div>
                                        <span class="ml-4">{{ $product->title }}</span>
                                    </td>
                                    <td class="px-4 py-2 text-center">{{ $quantity }}</td>
                                    <td class="px-4 py-2 text-right">{{ number_format($total, 2) }} บาท</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-100">
                            <tr>
                                <td class="px-4 py-2 font-semibold">ยอดรวมทั้งหมด</td>
                                <td></td>
                                <td class="px-4 py-2 text-right font-bold text-emerald-600">
                                    {{ number_format($totalAmount, 2) }}
                                    บาท</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Submit Button -->
                <form action="{{ route('cart.delivered') }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit"
                        class="w-full btn-primary text-white font-semibold py-2 rounded-lg shadow transition">
                        ยืนยันคำสั่งซื้อสำหรับการจัดส่ง
                    </button>
                </form>
                <a href="{{ route('cart.index') }}" class="inline-block mt-4 text-indigo-600 hover:underline">
                    กลับไปที่หน้าตะกร้าสินค้า
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
