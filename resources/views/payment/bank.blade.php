<x-app-layout>
    <div class="container mx-auto my-10 px-4">
        <h1 class="text-3xl font-bold text-gray-700 mb-6">ชำระผ่านบัญชีธนาคาร/QR Code PromptPay</h1>

        <!-- รายละเอียดคำสั่งซื้อ -->
        <div class="bg-white rounded-lg shadow p-6 mb-6 overflow-x-auto">
            <h2 class="text-xl font-semibold mb-4">รายละเอียดคำสั่งซื้อของคุณ</h2>
            <table class="w-full min-w-[600px] text-left table-auto">
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
                            $quantity = $orderItems[$product->id]['quantity'];
                            $total = $product->price * $quantity;
                        @endphp
                        <tr class="border-b">
                            <td class="px-4 py-2 flex items-center space-x-4">
                                <div class="w-24 h-20 flex items-center justify-center overflow-hidden">
                                    <img src="{{ $product->image }}" class="object-cover h-full" alt="">
                                </div>
                                <span>{{ $product->title }}</span>
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
                        <td class="px-4 py-2 text-right font-bold text-emerald-600">{{ number_format($totalAmount, 2) }}
                            บาท</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- <div class="bg-white rounded-lg shadow p-6 mb-6 text-center">
            <h2 class="text-xl font-semibold mb-4">สแกนเพื่อชำระเงิน</h2>
            <img src="{{ asset('qrcodes/payment_qr.png') }}" alt="QR Code" class="w-64 h-64 mx-auto mb-4">
            <!-- QR Code Display base64 -->
            <img src="data:image/png;base64,{{ $qrCodeData }}" alt="QR Code" class="w-64 h-64 mx-auto mb-4">

            <p class="text-gray-600">ยอดเงิน: <strong>{{ number_format($totalAmount, 2) }} บาท</strong></p>
        </div> --}}

        <!-- QR Code สำหรับชำระเงิน -->
        <div class="flex flex-col sm:flex-row gap-6">
            <!-- QR Code Payment Section -->
            <div
                class="bg-white rounded-lg shadow-lg p-8 mb-8 text-center w-full sm:w-1/2 flex flex-col justify-between">
                <h2 class="text-2xl font-bold text-gray-700 mb-6">สแกนเพื่อชำระเงิน</h2>

                <!-- QR Code Display -->
                <div class="mb-6">
                    <img src="{{ asset('qrcodes/payment_qr.png') }}" alt="QR Code"
                        class="w-64 h-64 mx-auto border-4 border-gray-300 rounded-lg shadow-md">
                </div>

                <!-- Amount Section -->
                <p class="text-lg text-gray-700">
                    <span class="text-xl font-semibold text-gray-900">ยอดเงิน :</span>
                    <strong class="text-2xl text-green-600">{{ number_format($totalAmount, 2) }} บาท</strong>
                </p>

                <!-- PromptPay Instruction Section -->
                <p class="mt-4 text-sm text-gray-600">
                    <i>กรุณาสแกน QR Code ด้านบนเพื่อชำระเงินผ่าน PromptPay</i>
                </p>

                <!-- Download Button Section -->
                <div class="mt-8">
                    <a href="{{ asset('qrcodes/final_payment_qr.png') }}" download="payment_qr.png">
                        <button
                            class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                            ดาวน์โหลด QR Code
                        </button>
                    </a>
                </div>
            </div>

            <!-- Slip Upload Section -->
            <div class="bg-white rounded-lg shadow-lg p-8 mb-8 text-center w-full sm:w-1/2 flex flex-col">
                <h2 class="text-2xl font-bold text-gray-700 mb-6">อัปโหลดสลิปชำระเงิน</h2>

                <form id="paymentForm" action="{{ route('payment.withqrcode') }}" method="POST"
                    enctype="multipart/form-data" onsubmit="return validateForm()">
                    @csrf

                    <div id="imagePreview" class="mb-6 mx-auto" onclick="document.getElementById('slipInput').click()">
                        <div id="placeholder"
                            class="w-64 h-64 mx-auto border-4 border-gray-300 rounded-lg shadow-md flex justify-center items-center text-gray-400">
                            <span>กรุณาอัปโหลดสลิป</span>
                        </div>
                    </div>

                    <p class="mt-4 text-sm text-gray-600">
                        <i>*กรุณาแนบหลักฐานการโอนเงิน</i>
                    </p>

                    <input type="file" name="slipInput" class="mb-4" id="slipInput" style="display: none;"
                        onchange="previewImage(event)" oninput="resetPreview()">

                    <div class="mt-20">
                        <button type="submit"
                            class="bg-primary text-white py-2 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                            ยืนยันการชำระเงิน
                        </button>
                    </div>
                </form>
                {{-- <div id="deletePreview" class="mt-2">
            <a href="javascript:void(0);" onclick="removeImagePreview()" class="text-sm text-red-500">ลบภาพ</a>
        </div> --}}
            </div>
        </div>
        <!-- Back Link Section -->
        <a href="{{ route('cart.index') }}" class="inline-block mt-4 text-indigo-600 hover:underline">
            กลับไปที่หน้าตะกร้าสินค้า
        </a>
    </div>
</x-app-layout>

<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (!file) {
            return;
        }

        // ตรวจสอบนามสกุลไฟล์
        const allowedExtensions = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!allowedExtensions.includes(file.type)) {
            alert('กรุณาอัปโหลดไฟล์รูปภาพที่เป็น .jpg หรือ .png เท่านั้น');
            event.target.value = ''; // รีเซ็ตค่า input file
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const imagePreview = document.getElementById("imagePreview");

            // ลบ <img> ตัวเก่าถ้ามี
            const existingImages = imagePreview.getElementsByTagName("img");
            while (existingImages.length > 0) {
                existingImages[0].remove();
            }

            // สร้าง <img> ใหม่
            const img = document.createElement("img");
            img.src = e.target.result;
            img.classList.add("w-64", "h-64", "mx-auto", "border-4", "border-gray-300", "rounded-lg", "shadow-md");

            // ซ่อน placeholder และแสดงภาพที่อัปโหลด
            document.getElementById("placeholder").style.display = 'none';
            imagePreview.appendChild(img);
        };
        reader.readAsDataURL(file);
    }

    function resetPreview() {
        const fileInput = document.getElementById("slipInput");

        if (fileInput.files.length === 0) {
            const previewContainer = document.getElementById("imagePreview");

            // ลบ <img> ทุกตัวที่อยู่ใน previewContainer
            const existingImages = previewContainer.getElementsByTagName("img");
            for (let i = 0; i < existingImages.length; i++) {
                existingImages[i].remove();
            }

            // แสดง placeholder ใหม่'
            const placeholder = document.getElementById("placeholder").style.display = '';
            previewContainer.appendChild(placeholder);

            // ถ้าอยากรีเฟรชหน้าให้โหลดใหม่
            // window.location.reload(true); // ปิดไว้ถ้าไม่ต้องการให้รีเฟรช
        }
    }

    function validateForm() {
        const fileInput = document.getElementById("slipInput");
        if (!fileInput.files.length) {
            alert("กรุณาอัปโหลดสลิปการโอนเงินก่อนทำการยืนยัน");
            event.preventDefault(); // ป้องกันการ submit ฟอร์ม
            return false;
        }
        return true;
    }
</script>
