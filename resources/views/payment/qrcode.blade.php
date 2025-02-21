<x-app-layout>
    <h1>PromptPay QR Code</h1>
    <p>Amount: ฿{{ number_format($amount, 2) }}</p>
    <div>{!! $qrCode !!}</div>

    <form action="{{ route('payment.summary') }}" method="POST">
        @csrf
        <button type="submit">Confirm Payment</button>
    </form>
</x-app-layout>
