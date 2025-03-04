<x-app-layout>
    <div class="w-[400px] mx-auto">

        <div class="mb-4 text-sm text-gray-600">
            {{ __('นี่คือพื้นที่ปลอดภัยของแอปพลิเคชัน โปรดยืนยันรหัสผ่านของคุณก่อนดำเนินการต่อ') }}
        </div>

        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <!-- Password -->
            <div>
                <x-label for="password" :value="__('รหัสผ่าน')" />

                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="current-password" />
            </div>

            <div class="flex justify-end mt-4">
                <x-button>
                    {{ __('ยืนยัน') }}
                </x-button>
            </div>
        </form>
    </div>
</x-app-layout>
