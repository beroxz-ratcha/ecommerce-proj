<x-app-layout>
    <form action="{{ route('register') }}" method="post" class="max-w-md mx-auto p-6 my-16 bg-white shadow-md rounded-lg">
        @csrf

        <h2 class="text-gray-700 text-4xl font-semibold text-center mb-5">สร้างบัญชีใหม่</h2>
        <p class="text-center text-gray-500 mb-6">
            กรอกข้อมูลของคุณด้านล่าง หรือ
            <a href="{{ route('login') }}" class="text-sm text-indigo-700 hover:text-indigo-600">
                มีบัญชีอยู่แล้วใช่ไหม?
            </a>
        </p>

        @if (session('error'))
            <div class="py-2 px-3 bg-red-500 text-white mb-2 rounded">
                {{ session('error') }}
            </div>
        @endif

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <div class="mb-4">
            <x-input placeholder="ชื่อ" type="text" name="name" class="h-12 w-full" :value="old('name')" />
        </div>
        <div class="mb-4">
            <x-input placeholder="อีเมล" type="email" name="email" class="h-12 w-full" :value="old('email')" />
        </div>
        <div class="mb-4">
            <x-input placeholder="รหัสผ่าน" type="password" name="password" class="h-12 w-full" />
        </div>
        <div class="mb-4">
            <x-input placeholder="ยืนยันรหัสผ่าน" type="password" name="password_confirmation" class="h-12 w-full" />
        </div>

        <button class="btn-primary hover:btn-primary active:btn-primary w-full">
            ลงทะเบียน
        </button>
    </form>
</x-app-layout>
