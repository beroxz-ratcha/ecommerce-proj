<x-app-layout>
    {{-- testtt --}}
    <h1>Payment Summary</h1>

    <h3>Order Items:</h3>
    <ul>
        @foreach ($cartItems as $item)
            <li>{{ $item['name'] }} - ฿{{ number_format($item['total_price'], 2) }}</li>
        @endforeach
    </ul>

    <p><strong>Total Amount: </strong> ฿{{ number_format($totalPrice, 2) }}</p>

    <form action="{{ route('payment.bank') }}" method="GET">
        @csrf
        <button type="submit">Pay via Bank</button>
    </form>

    <form action="{{ route('payment.qrcode') }}" method="POST">
        @csrf
        <input type="hidden" name="amount" value="{{ $totalPrice }}">
        <button type="submit">Pay via QR Code</button>
    </form>
</x-app-layout>
