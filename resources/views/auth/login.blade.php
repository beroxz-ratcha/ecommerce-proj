<x-app-layout>
    <form method="POST" action="{{ route('login') }}" class="max-w-md mx-auto p-6 my-16 bg-white shadow-md rounded-lg">
        <h2 class="text-gray-700 text-4xl font-semibold text-center mb-5">
            เข้าสู่ระบบ
        </h2>
        <p class="text-gray-500 text-center mb-6">
            กรอกข้อมูลของคุณด้านล่าง หรือ
            <a href="{{ route('register') }}" class="text-sm text-indigo-700 hover:text-indigo-600">
                สร้างบัญชีใหม่
            </a>
        </p>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        @csrf
        <div class="mb-4">
            <x-input type="email" name="email" placeholder="ที่อยู่อีเมล/บัญชีผู้ใช้" class="h-12 w-full"
                :value="old('email')" />
        </div>
        <div class="mb-4">
            <x-input type="password" name="password" placeholder="รหัสผ่าน" class="h-12 w-full" :value="old('password')" />
        </div>
        <div class="flex justify-between items-center mb-5">
            <div class="flex items-center">
                {{-- <input id="loginRememberMe" type="checkbox"
                    class="mr-3 rounded border-gray-300 text-green-600 focus:ring-green-500" />
                <label class="text-sm" for="loginRememberMe">Remember Me</label> --}}
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm text-indigo-700 hover:text-indigo-600">
                    ลืมรหัสผ่าน?
                </a>
            @endif
        </div>
        <button class="btn-primary hover:btn-primary active:btn-primary w-full">
            เข้าสู่ระบบ
        </button>
    </form>
</x-app-layout>
