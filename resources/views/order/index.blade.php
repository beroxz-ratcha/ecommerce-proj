<?php
/** @var \Illuminate\Database\Eloquent\Collection $orders */
?>

<x-app-layout>
    <div class="container mx-auto lg:w-2/3 p-5">
        <h1 class="text-gray-800 text-3xl font-bold mb-6">คำสั่งซื้อของฉัน</h1>
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <table class="table-auto w-full text-left border-collapse">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="text-center text-gray-600 py-3 px-4">คำสั่งซื้อ #</th>
                        <th class="text-center text-gray-600 py-3 px-4">วันที่</th>
                        <th class="text-center text-gray-600 py-3 px-4">สถานะ</th>
                        <th class="text-center text-gray-600 py-3 px-4">ยอดรวม</th>
                        <th class="text-center text-gray-600 py-3 px-4">รายการ</th>
                        <th class="text-center text-gray-600 py-3 px-4">การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $order)
                        <tr class="border-b hover:bg-gray-100 transition duration-200">
                            <td class="py-2 px-4 text-center">
                                <a href="{{ route('order.view', $order) }}"
                                    class="text-indigo-600 hover:text-indigo-500 font-semibold">
                                    #{{ $order->id }}
                                </a>
                            </td>
                            <td class="py-2 px-4 text-center whitespace-nowrap">
                                {{ $order->created_at->format('d/m/Y H:m:s') }}</td>
                            <td class="py-2 px-4 text-center">
                                <span
                                    class="text-white py-1 px-2 rounded {{ $order->isPaid() ? 'bg-emerald-500' : ($order->isDelivered() ? 'bg-yellow-500' : 'bg-gray-400') }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="py-2 px-4 text-center">฿{{ number_format($order->total_price, 2) }}</td>
                            <td class="py-2 px-4 text-center whitespace-nowrap">{{ $order->items_count }} item(s)</td>
                            <td class="py-2 px-4 flex justify-center">
                                @if (!$order->isPaid() && !$order->isDelivered())
                                    <form action="{{ route('cart.checkout-order', $order) }}" method="POST">
                                        @csrf
                                        <button
                                            class="flex items-center py-1 px-3 bg-indigo-600 text-white rounded hover:bg-indigo-500 transition duration-200">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                            Pay
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
</x-app-layout>
