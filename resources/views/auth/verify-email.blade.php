<x-app-layout>
    <div class="w-[400px] mx-auto py-32">

        <div class="mb-4 text-base text-gray-600">
            {{ __('Thanks for signing up! ก่อนเริ่มต้น โปรดยืนยันที่อยู่อีเมลของคุณโดยคลิกลิงก์ที่เราเพิ่งส่งให้คุณทางอีเมล หากคุณไม่ได้รับอีเมล เราจะส่งอีเมลฉบับใหม่ให้คุณ') }}
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <x-button>
                        {{ __('ส่งอีเมลยืนยันอีกครั้ง') }}
                    </x-button>
                </div>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900">
                    {{ __('ออกจากระบบ') }}
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
