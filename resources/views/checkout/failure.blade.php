<x-app-layout>
    <div class="w-[400px] mx-auto bg-red-500 py-2 px-3 text-white rounded">
        <h1>การชำระเงินของคุณไม่ประสบความสำเร็จ, โปรดตรวจสอบอีกครั้ง!!</h1>
        <p>{{ $message ?? '' }}</p>
    </div>

    <script>
        setTimeout(function() {
            window.location.href = "{{ route('order.index') }}";
        }, 1500);
    </script>
</x-app-layout>
