<x-app-layout>
    <!-- Hero Section -->
    <section class="hero bg-gray py-3 px-6">
        <div class="container mx-auto flex flex-col-reverse lg:flex-row items-center">
            <div class="hero-content lg:w-1/2 text-center lg:text-left">
                <h1 class="text-4xl font-bold mb-4">
                    <span class="text-gray-800">Bring </span>
                    <span class="text-green-primary">Nature </span>
                    <span class="text-gray-800">to </span>
                    <span class="text-gray-800">Your </span>
                    <span class="text-green-primary">Home </span>
                </h1>
                <p class="text-2xl mb-12 text-gray-800">
                    ค้นพบคอลเลกชันพืชที่เราได้คัดสรรมาอย่างดี ซึ่งเหมาะสำหรับทุกพื้นที่
                    เติมความสดใสให้กับวันของคุณและฟอกอากาศให้บริสุทธิ์ด้วยความงามจากธรรมชาติ
                </p>
                <a href="{{ route('shop') }}" class="btn-primary" style="padding: 15px 20px;">Shopping Now!!</a>
            </div>
            <div class="hero-section lg:w-1/2">
                <img src="{{ asset('/storage/imgfront/hero-image.png') }}" alt="Hero image"
                    class="w-full lg:w-3/4 mx-auto" />
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories py-12 bg-white rounded-xl">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl text-gray-700 font-bold mb-8">หมวดหมู่</h2>
            <div class="flex items-center justify-center gap-4 relative">
                <button
                    class="w-12 h-12 text-3xl bg-gray-200 rounded-full flex items-center justify-center border border-gray-300 hover:bg-gray-300 transition-colors duration-200"
                    id="scroll-left">
                    <span>&#8249;</span>
                </button>

                <div id="category-container"
                    class="overflow-x-auto whitespace-nowrap scroll-smooth no-scrollbar overflow-hidden flex-1">
                    <div class="flex gap-4">
                        @foreach ($categories as $category)
                            <a href="{{ route('byCategory', $category->slug) }}"
                                class="category-item inline-block bg-gray-100 p-4 rounded-lg shadow hover:shadow-lg transition-shadow duration-200 min-w-[200px]"
                                data-slug="{{ $category->slug }}">
                                <h3 class="text-lg text-gray-700 font-semibold">{{ $category->name }}</h3>
                            </a>
                        @endforeach
                    </div>
                </div>

                <button
                    class="w-12 h-12 text-3xl bg-gray-200 rounded-full flex items-center justify-center border border-gray-300 hover:bg-gray-300 transition-colors duration-200"
                    id="scroll-right">
                    <span>&#8250;</span>
                </button>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products py-12 bg-gray rounded-xl">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl text-gray-700 font-bold mb-8">รายการสินค้า</h2>

            <!-- Tabs -->
            <div class="tabs mb-6">
                <button class="text-gray-700 tab-button active" onclick="showTab('all')">สินค้าทั้งหมด</button>
                <button class="text-gray-700 tab-button" onclick="showTab('bestseller')">สินค้าขายดี</button>
                <button class="text-gray-700 tab-button" onclick="showTab('new')">สินค้ามาใหม่</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6" id="product-grid">
                <!-- All Products -->
                @foreach ($allProducts as $product)
                    <div x-data="productItem({{ json_encode([
                        'id' => $product->id,
                        'slug' => $product->slug,
                        'image' => $product->image ?: '/assets/img/noimage.png',
                        'title' => $product->title,
                        'price' => $product->price,
                        'addToCartUrl' => route('cart.add', $product),
                    ]) }})"
                        class="product-item bg-white p-4 rounded-lg shadow hover:shadow-lg relative">
                        @if ($product->is_promotion)
                            <span
                                class="absolute top-2 right-2 bg-red-600 text-white text-lg font-bold py-1 px-3 rounded rotate-12">
                                Hot
                            </span>
                        @endif
                        <a href="{{ route('product.view', $product->slug) }}">
                            <img src="{{ $product->image ?: asset('assets/img/noimage.png') }}"
                                alt="{{ $product->title }}" class="w-full h-48 object-cover mb-4 rounded">
                        </a>

                        <h3 class="text-lg font-semibold">{{ $product->title }}</h3>
                        <p class="text-green-700 font-bold mt-2">฿{{ number_format($product->price, 2) }}</p>
                        <div class="flex justify-between items-center mb-2 mt-4">
                            <!-- Star Rating -->
                            <div class="flex gap-1">
                                @php
                                    $averageRating = $product->average_rating ?? 0;
                                    $fullStars = floor($averageRating);
                                    $halfStar = $averageRating - $fullStars >= 0.5 ? 1 : 0;
                                @endphp

                                @for ($i = 0; $i < $fullStars; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                        class="w-4 h-4 text-yellow-500" viewBox="0 0 24 24">
                                        <path
                                            d="M12 .587l3.668 7.429L24 9.347l-6 5.848 1.416 8.264L12 18.766l-7.416 4.693L6 15.195.001 9.347l8.332-1.331L12 .587z" />
                                    </svg>
                                @endfor

                                @if ($halfStar)
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                        class="w-4 h-4 text-yellow-500" viewBox="0 0 24 24" transform="scale(-1, 1)">
                                        <path
                                            d="M12 .587l3.668 7.429L24 9.347l-6 5.848 1.416 8.264L12 18.766v-18.18z" />
                                    </svg>
                                @endif

                                @for ($i = $fullStars + $halfStar; $i < 5; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                                        class="w-4 h-4 text-yellow-500" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 .587l3.668 7.429L24 9.347l-6 5.848 1.416 8.264L12 18.766l-7.416 4.693L6 15.195.001 9.347l8.332-1.331L12 .587z" />
                                    </svg>
                                @endfor
                            </div>

                            <!-- Add to Cart Button -->
                            <button class="bg-transparent border-none cursor-pointer p-2 hover:text-green-700"
                                @click="addToCart()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </button>
                        </div>

                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 hidden" id="best-seller-products">
                <!-- Best Selling Products -->
                @foreach ($bestSellProducts as $product)
                    <div x-data="productItem({{ json_encode([
                        'id' => $product->id,
                        'slug' => $product->slug,
                        'image' => $product->image ?: '/assets/img/noimage.png',
                        'title' => $product->title,
                        'price' => $product->price,
                        'addToCartUrl' => route('cart.add', $product),
                    ]) }})"
                        class="product-item bg-white p-4 rounded-lg shadow hover:shadow-lg relative">
                        @if ($product->is_promotion)
                            <span
                                class="absolute top-2 right-2 bg-red-600 text-white text-lg font-bold py-1 px-3 rounded rotate-12">
                                Hot
                            </span>
                        @endif
                        <a href="{{ route('product.view', $product->slug) }}">
                            <img src="{{ $product->image ?: asset('assets/img/noimage.png') }}"
                                alt="{{ $product->title }}" class="w-full h-48 object-cover mb-4 rounded">
                        </a>

                        <h3 class="text-lg font-semibold">{{ $product->title }}</h3>
                        <p class="text-green-700 font-bold mt-2">฿{{ number_format($product->price, 2) }}</p>
                        <div class="flex justify-between items-center mb-2 mt-4">
                            <!-- Star Rating -->
                            <div class="flex gap-1">
                                @php
                                    $averageRating = $product->average_rating ?? 0;
                                    $fullStars = floor($averageRating);
                                    $halfStar = $averageRating - $fullStars >= 0.5 ? 1 : 0;
                                @endphp

                                @for ($i = 0; $i < $fullStars; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                        class="w-4 h-4 text-yellow-500" viewBox="0 0 24 24">
                                        <path
                                            d="M12 .587l3.668 7.429L24 9.347l-6 5.848 1.416 8.264L12 18.766l-7.416 4.693L6 15.195.001 9.347l8.332-1.331L12 .587z" />
                                    </svg>
                                @endfor

                                @if ($halfStar)
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                        class="w-4 h-4 text-yellow-500" viewBox="0 0 24 24" transform="scale(-1, 1)">
                                        <path
                                            d="M12 .587l3.668 7.429L24 9.347l-6 5.848 1.416 8.264L12 18.766v-18.18z" />
                                    </svg>
                                @endif

                                @for ($i = $fullStars + $halfStar; $i < 5; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                                        class="w-4 h-4 text-yellow-500" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 .587l3.668 7.429L24 9.347l-6 5.848 1.416 8.264L12 18.766l-7.416 4.693L6 15.195.001 9.347l8.332-1.331L12 .587z" />
                                    </svg>
                                @endfor
                            </div>

                            <!-- Add to Cart Button -->
                            <button class="bg-transparent border-none cursor-pointer p-2 hover:text-green-700"
                                @click="addToCart()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 hidden" id="new-products">
                <!-- New Products -->
                @foreach ($newProducts as $product)
                    <div x-data="productItem({{ json_encode([
                        'id' => $product->id,
                        'slug' => $product->slug,
                        'image' => $product->image ?: '/assets/img/noimage.png',
                        'title' => $product->title,
                        'price' => $product->price,
                        'addToCartUrl' => route('cart.add', $product),
                    ]) }})"
                        class="product-item bg-white p-4 rounded-lg shadow hover:shadow-lg relative">
                        @if ($product->is_promotion)
                            <span
                                class="absolute top-2 right-2 bg-red-600 text-white text-lg font-bold py-1 px-3 rounded rotate-12">
                                Hot
                            </span>
                        @endif
                        <a href="{{ route('product.view', $product->slug) }}">
                            <img src="{{ $product->image ?: asset('assets/img/noimage.png') }}"
                                alt="{{ $product->title }}" class="w-full h-48 object-cover mb-4 rounded">
                        </a>

                        <h3 class="text-lg font-semibold">{{ $product->title }}</h3>
                        <p class="text-green-700 font-bold mt-2">฿{{ number_format($product->price, 2) }}</p>
                        <div class="flex justify-between items-center mb-2 mt-4">
                            <!-- Star Rating -->
                            <div class="flex gap-1">
                                @php
                                    $averageRating = $product->average_rating ?? 0;
                                    $fullStars = floor($averageRating);
                                    $halfStar = $averageRating - $fullStars >= 0.5 ? 1 : 0;
                                @endphp

                                @for ($i = 0; $i < $fullStars; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                        class="w-4 h-4 text-yellow-500" viewBox="0 0 24 24">
                                        <path
                                            d="M12 .587l3.668 7.429L24 9.347l-6 5.848 1.416 8.264L12 18.766l-7.416 4.693L6 15.195.001 9.347l8.332-1.331L12 .587z" />
                                    </svg>
                                @endfor

                                @if ($halfStar)
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                        class="w-4 h-4 text-yellow-500" viewBox="0 0 24 24" transform="scale(-1, 1)">
                                        <path
                                            d="M12 .587l3.668 7.429L24 9.347l-6 5.848 1.416 8.264L12 18.766v-18.18z" />
                                    </svg>
                                @endif

                                @for ($i = $fullStars + $halfStar; $i < 5; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                                        class="w-4 h-4 text-yellow-500" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 .587l3.668 7.429L24 9.347l-6 5.848 1.416 8.264L12 18.766l-7.416 4.693L6 15.195.001 9.347l8.332-1.331L12 .587z" />
                                    </svg>
                                @endfor
                            </div>

                            <!-- Add to Cart Button -->
                            <button class="bg-transparent border-none cursor-pointer p-2 hover:text-green-700"
                                @click="addToCart()">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-2" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination Links -->
            <div class="mt-8">
                {{ $allProducts->links() }}
            </div>
        </div>
    </section>

    <!-- Plant Care Tips Section -->
    <section class="plant-care-tips py-12 bg-white rounded-xl">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl text-gray-700 font-bold mb-8">เคล็ดลับการดูแลต้นไม้</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($tools as $tip)
                    <div class="tip-item bg-gray-100 p-6 rounded-lg shadow hover:shadow-lg">
                        <img src="{{ $tip->image ?: asset('assets/img/noimage.png') }}" alt="{{ $tip->title }}"
                            class="w-full h-48 object-cover mb-4 rounded">
                        <h3 class="text-xl font-semibold mb-4">{{ $tip->title }}</h3>
                        <p class="text-gray-600">{{ $tip->description }}</p>
                        {{-- <a href="{{ route('tips.show', $tip->slug) }}" class="text-green-600 mt-4 inline-block">Learn More</a> --}}
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Plant Care Videos Section -->
    <section class="plant-care-videos py-12 bg-white rounded-xl">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl text-gray-700 font-bold mb-8">วิดีโอการดูแลต้นไม้</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Video 1 -->
                <div class="video-item bg-gray-100 p-6 rounded-lg shadow hover:shadow-lg">
                    <div class="video-wrapper mb-4">
                        <iframe class="w-full h-48 rounded" src="https://www.youtube.com/embed/J1LW1Nz0pd4"
                            frameborder="0" allowfullscreen>
                        </iframe>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">วิธีการดูแลต้นไม้ในร่ม</h3>
                    <p class="text-gray-600">เรียนรู้เคล็ดลับที่ดีที่สุดในการดูแลต้นไม้ในร่มให้เติบโตอย่างมีชีวิตชีวา
                    </p>
                </div>
                <!-- Video 2 -->
                <div class="video-item bg-gray-100 p-6 rounded-lg shadow hover:shadow-lg">
                    <div class="video-wrapper mb-4">
                        <iframe class="w-full h-48 rounded" src="https://www.youtube.com/embed/-Dmvx20Y4YQ"
                            frameborder="0" allowfullscreen>
                        </iframe>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">คู่มือการรดน้ำต้นไม้</h3>
                    <p class="text-gray-600">ค้นพบวิธีการรดน้ำต้นไม้ซักคิวเลนท์อย่างถูกต้อง</p>
                </div>
                <!-- Video 3 -->
                <div class="video-item bg-gray-100 p-6 rounded-lg shadow hover:shadow-lg">
                    <div class="video-wrapper mb-4">
                        <iframe class="w-full h-48 rounded" src="https://www.youtube.com/embed/GoHvdDlbjvw"
                            frameborder="0" allowfullscreen>
                        </iframe>
                    </div>
                    <h3 class="text-xl font-semibold mb-4">เทคนิคการตัดแต่งต้นไม้</h3>
                    <p class="text-gray-600">เชี่ยวชาญศิลปะการตัดแต่งต้นไม้ของคุณเพื่อการเติบโตที่ดีที่สุด</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Features Section -->
    <section class="features py-12 bg-white rounded-xl mt-10">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl text-gray-700 font-bold mb-8">การทำงานของเรา</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
                <div class="feature-item flex flex-col items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-12 h-12 text-green-700 mb-4">
                        <path strokeLinecap="round" strokeLinejoin="round"
                            d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                    <h3 class="text-gray-700 text-lg font-semibold">บริการจัดส่ง</h3>
                </div>

                <div class="feature-item flex flex-col items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-12 h-12 text-green-700 mb-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                    </svg>
                    <h3 class="text-gray-700 text-lg font-semibold">การคืนเงิน 100%</h3>
                </div>

                <div class="feature-item flex flex-col items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-12 h-12 text-green-700 mb-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z" />
                    </svg>
                    <h3 class="text-gray-700 text-lg font-semibold">การชำระเงินที่ปลอดภัย</h3>
                </div>


                <div class="feature-item flex flex-col items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-12 h-12 text-green-700 mb-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                    </svg>
                    <h3 class="text-gray-700 text-lg font-semibold">การสนับสนุนตลอด 24 ชั่วโมง</h3>
                </div>
            </div>
        </div>

        <!-- chat bot -->
        <div class="chatbot-container fixed bottom-4 right-4 z-50">
            <button id="chatbot-button"
                class="bg-green-700 text-white p-4 rounded-full shadow-lg hover:bg-green-800 transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
            </button>

            <!-- แชทบอท -->
            <div id="chatbot-window" class="hidden bg-white rounded-lg shadow-lg fixed bottom-20 right-4 p-4"
                style="width: 340px; height: 400px;">
                <div class="chatbot-header flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">สอบถามผู้เชี่ยวชาญ</h3>
                    <button id="close-chatbot" class="text-gray-500 hover:text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- ส่วนข้อความแชท -->
                <div id="chatbot-body" class="chatbot-body overflow-y-auto mb-4" style="height: 264px;">
                    <div class="message mb-2 flex items-start">
                        <img src="{{ asset('/storage/imgfront/chat-bot.png') }}" alt="Bot Avatar"
                            class="w-8 h-8 rounded-full mr-2">
                        {{-- <p class="bg-gray-100 p-2 rounded-lg max-w-[80%]">สวัสดี! มีอะไรให้ช่วยไหมครับ?</p> --}}
                        <div class="bg-gray-100 p-2 rounded-lg max-w-[80%]"> สวัสดี! มีอะไรให้ช่วยไหม?<br>
                            (Hello! Is there anything I can help you with?)
                        </div>
                    </div>
                </div>

                <!-- ช่องพิมพ์ข้อความ -->
                <div class="chatbot-footer">
                    <input id="user-input" type="text" class="w-full p-2 border rounded-lg"
                        placeholder="พิมพ์ข้อความ..." />
                </div>
            </div>
        </div>
    </section>
</x-app-layout>

<script>
    function showTab(tabName) {
        const allProducts = document.getElementById('product-grid');
        const newProducts = document.getElementById('new-products');
        const bestSellerProducts = document.getElementById('best-seller-products');

        allProducts.classList.add('hidden');
        newProducts.classList.add('hidden');
        bestSellerProducts.classList.add('hidden');

        if (tabName === 'all') {
            allProducts.classList.remove('hidden');
        } else if (tabName === 'new') {
            newProducts.classList.remove('hidden');
        } else if (tabName === 'bestseller') {
            bestSellerProducts.classList.remove('hidden');
        }

        const tabButtons = document.querySelectorAll('.tab-button');
        tabButtons.forEach(button => {
            button.classList.remove('active');
        });
        document.querySelector(`.tab-button[onclick="showTab('${tabName}')"]`).classList.add('active');
    }

    showTab('all');


    const categoryLinks = document.querySelectorAll('.category-item');
    const container = document.getElementById('category-container');
    document.getElementById('scroll-left').addEventListener('click', () => {
        container.scrollBy({
            left: -250,
            behavior: 'smooth'
        });
    });
    document.getElementById('scroll-right').addEventListener('click', () => {
        container.scrollBy({
            left: 250,
            behavior: 'smooth'
        });
    });
    categoryLinks.forEach(link => {
        link.addEventListener('click', function() {
            const slug = this.dataset.slug;
            const url = "{{ url('category') }}/" + slug;
            localStorage.setItem('selectedCategory', url);
        });
    });

    // สำหรับการเปิด / ปิดแชทบอท
    document.getElementById('chatbot-button').addEventListener('click', function() {
        document.getElementById('chatbot-window').classList.toggle('hidden');
    });

    document.getElementById('close-chatbot').addEventListener('click', function() {
        document.getElementById('chatbot-window').classList.add('hidden');
    });

    // 

    // สำหรับ ฟังชั่นของ chatbot 

    document.addEventListener("DOMContentLoaded", function() {
        const chatbotBody = document.getElementById("chatbot-body");
        const userInput = document.getElementById("user-input");

        async function translate(text, fromLang, toLang) {
            const apiKey = @json($openaiApiKey);
            const modelId = "Helsinki-NLP/opus-mt-th-en";
            const apiUrl = `https://api-inference.huggingface.co/models/${modelId}`;

            const response = await fetch(apiUrl, {
                method: "POST",
                headers: {
                    "Authorization": `Bearer ${apiKey}`,
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    inputs: text
                }),
            });

            const data = await response.json();
            if (Array.isArray(data) && data[0].translation_text) {
                return data[0].translation_text;
            }
            throw new Error("Translation failed");
        }

        async function sendMessageToBot(message) {
            const apiKey = @json($openaiApiKey);
            const apiUrl = @json($apiUrlAI);

            appendMessage("user", message);

            function filterAnswer(responseText, question) {
                if (!responseText) return "";
                const trimmedResponse = responseText.trim();
                const trimmedQuestion = question.trim();
                if (trimmedResponse.startsWith(trimmedQuestion)) {
                    return trimmedResponse.slice(trimmedQuestion.length).trim();
                }
                return trimmedResponse;
            }

            const typingId = `typing-${Date.now()}`;
            appendMessage("bot", "กำลังพิมพ์...", typingId);

            try {
                const response = await fetch(apiUrl, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": `Bearer ${apiKey}`
                    },
                    body: JSON.stringify({
                        model: 'gpt-4o-mini',
                        messages: [{
                            "role": "system",
                            "content": `
                                คุณคือแชทบอทผู้เชี่ยวชาญด้านการให้ข้อมูลเว็บไซต์ Perdis Store ขายต้นไม้แห่งหนึ่ง ที่มีร้านค้ามาเปิดขายต้นไม้ได้

                                🏪 ข้อมูลเว็บไซต์ Perdis Store:
                                - เว็บไซต์มีหน้าหลักแสดงสินค้ายอดฮิต สินค้าขายดี สินค้าโปรโมชั่น และสินค้าทั่วไป
                                - เป็นแพลตฟอร์มที่ให้ร้านค้าต่างๆ มาเปิดขายต้นไม้ได้

                                📂 เว็บไซต์มีหมวดหมู่ต้นไม้ดังนี้:
                                - ต้นไม้ในร่ม
                                - ต้นไม้นอกบ้าน/กลางแจ้ง
                                - ต้นไม้ขนาดเล็ก
                                - ต้นไม้คลุมดิน
                                - ต้นไม้เลื้อย
                                - ต้นไม้ดอก
                                - ต้นไม้ใบสวยงาม
                                - ต้นไม้มีผล
                                - ต้นไม้กรองฝุ่น
                                - ต้นไม้หลากสี
                                - ต้นไม้พุ่มไม้

                                🏪 ร้านค้าในระบบ:
                                - dckshop
                                - bowtree
                                - gunflower
                                - Flower Paradise
                                - Tropical Garden
                                - Tree Shop
                                - Plants Ball Shop

                                🌱 สินค้าในระบบ:
                                - ต้นชลอรัม (Chlorophytum comosum/Spider Plant) ราคา ฿500.00
                                - ต้นแสงทอง/ต้นลิ้นมังกร (Dracaena trifasciata/Snake Plant) ราคา ฿1,600.00
                                - ต้นขมิ้น (Turmeric) ราคา ฿900.00 
                                - คาลาเธีย โครคาต้า/ต้นกะบากส้ม (Calathea crocata) ราคา ฿6,000.00 
                                - ต้นชบา (Hibiscus) ราคา ฿4,000.00 
                                - คาลาเธีย (Calathea) ราคา ฿1,400.00 
                                - เฟิร์นบอสตัน (Boston Fern) ราคา ฿3,500.00 
                                - ปาล์มใหญ่ (Majesty Palm) ราคา ฿2,900.00 
                                - คาเมลเลีย (Camellia) ราคา ฿1,700.00  [Hot Item]
                                - ต้นพลูด่าง (Epipremnum aureum/Golden Pothos) ราคา ฿1,400.00  [Hot Item]
                                - ต้นกวักมรกต (Zamioculcas zamiifolia/ZZ Plant) ราคา ฿1,500.00 [Hot Item]
                                - มอนสเตอร่า (Monstera deliciosa) ราคา ฿1,400.00  [Hot Item]
                                - ต้นไผ่เบญจมาศ/ต้นไทรใบเล็ก (Ficus benjamina) ราคา ฿600.00 
                                - มอนสเตอร่า อาดานโซนี (Monstera adansonii/Swiss Cheese Plant) ราคา ฿1,300.00 
                                - ต้นเงินไหลมา (Pachira aquatica/Money Tree) ราคา ฿1,800.00 
                                - ต้นโพธอส (Epipremnum aureum/Pothos) ราคา ฿700.00 
                                - กล้วยไม้ (orchid) ราคา ฿200.00 
                                - เฟิร์นบอสตัน (Boston fern) ราคา ฿350.00 
                                - ต้นหน้าต่างใบ (Window leaf) ราคา ฿1,300.00 
                                - ต้นเศรษฐีเรือนนอก (Ocean Spider Plant) ราคา ฿500.00 

                                📋 สิ่งที่คุณสามารถช่วยได้:
                                1. ข้อมูลเว็บไซต์ Perdis Store และร้านค้าต่างๆ
                                2. ข้อมูลสินค้าต้นไม้ ราคา คะแนน รีวิว
                                3. ความรู้เกี่ยวกับต้นไม้ เช่น:
                                - วิธีการปลูกและดูแล
                                - การใช้ประโยชน์จากต้นไม้
                                - การแก้ไขปัญหาต้นไม้
                                - การเลือกต้นไม้ที่เหมาะสม
                                - เทคนิคการจัดสวน
                                - โรคและแมลงของต้นไม้
                                4. แนะนำสินค้ายอดฮิต สินค้าขายดี และโปรโมชั่น

                                ❌ สิ่งที่คุณไม่ควรตอบ: เช่น การเมือง, ข่าว, ดารา, เทคโนโลยี, ข้อมูลส่วนตัว เช่น บัตรประชาชน เบอร์โทร ฯลฯ
                                หากผู้ใช้ถามเรื่อง❌ สิ่งที่คุณไม่ควรตอบ ให้ตอบว่า "ขออภัยค่ะ ฉันเป็นผู้ช่วยเฉพาะเรื่องต้นไม้และเว็บไซต์ Perdis Store เท่านั้น มีอะไรเกี่ยวกับต้นไม้ที่ฉันช่วยได้ไหมคะ"

                                Perdis Store มีรูปแบบการชำเงิน สองวิธีคือ ผ่าน Qr Code พร้อมเพย์ และ ชำระเงินปลายทาง
                                
                               ✅ การจัดรูปแบบผลลัพธ์:
                                - ถ้าเป็นข้อมูลเกี่ยวกับสินค้า ให้แสดงแบบขึ้นบรรทัดใหม่ แล้วก็แสดง เป็นข้อ 1 2 3...
                                - ข้อมูลที่เป็น ข้อ 1 2 3... ให้ขึ้นบรรทัดใหม่ด้วย
                                - แสดงราคาในรูปแบบ "ราคา: ฿1,700.00"
                                - หากมี [Hot Item] ให้ใช้ <em> แสดงท้ายรายการ
                                - ให้ system เป็นผู้หญิง ใช้ คำสุภาพเป็น คะ ค่ะ

                                โปรดส่งข้อความตอบกลับในรูปแบบ HTML ที่สามารถแสดงผลในกล่องแชทได้โดยตรง โดย:
                                - ใช้แท็ก < br > สำหรับขึ้นบรรทัดใหม่
                                - หากเป็นรายการลำดับให้ใช้ < ol > < li > ... < /li></ol >
                                - หากเป็นรายการแบบไม่เรียงลำดับให้ใช้ < ul > < li > ... < /li></ul >
                                - ข้อความชื่อสินค้าหรือหัวข้อให้แสดงเลขข้อเพื่อบอกลำดับตัวเลข เช่น 1. ต้นพลูด่าง (Epipremnum aureum/Golden Pothos) ราคา: ฿1,400.00 [Hot Item]
                                - ถ้าสินค้าเป็น Hot Item ให้ใช้ < em > ต่อท้าย
                                - อย่าใช้แท็ก < html >, < body > หรือ CSS ใด ๆ แทรกในข้อความ
                                - อย่าส่งกลับใน < code > หรือ markdown format (เช่น \`\`\`)
                                - ทำตัวหนา หรือหัวข้อรายการตามความเหมาะสมและสวยงาม 
                                - ให้ตอบกลับด้วยภาษาที่สุภาพแบบผู้หญิง

                                ✅ หากผู้ใช้ถามถึงชื่อของพันธุ์ไม้ต่าง ๆ ที่มีอยู่จริง เช่น กล้วยไม้ มอนสเตอร่า ฯลฯ ให้ตอบได้
                                ✅ ถ้าชื่อต้นไม้ตรงกับสินค้าที่มีในระบบ ให้ตอบข้อมูลราคาด้วย  
                                `
                        }, {
                            role: "user",
                            content: message
                        }],
                        temperature: 0.7,
                        max_tokens: 1000
                    })
                });

                const data = await response.json();
                const typingEl = document.getElementById(typingId);
                if (typingEl) typingEl.remove();

                if (data.choices && data.choices.length > 0 && data.choices[0].message?.content) {
                    const rawReply = data.choices[0].message.content;
                    appendMessage("bot", rawReply);
                } else {
                    appendMessage("bot", "ขออภัย ไม่สามารถให้คำตอบได้ กรุณาลองใหม่ภายหลัง");
                }
            } catch (error) {
                console.error("Error:", error);
                appendMessage("bot", "ขออภัย มีข้อผิดพลาดเกิดขึ้น, กรุณาลองใหม่อีกครั้ง หรือติดต่อ admin");
            }
        }

        // ฟังก์ชันเพิ่มข้อความลงใน UI
        function appendMessage(sender, text) {
            const messageDiv = document.createElement("div");
            messageDiv.classList.add("message", "mb-2", "flex", "items-start");

            if (sender === "user") {
                messageDiv.innerHTML = `<p class="bg-blue-100 p-2 rounded-lg ml-auto">${text}</p>`;
            } else {
                messageDiv.innerHTML = `
                    <img src="/storage/imgfront/chat-bot.png" alt="Bot Avatar" class="w-8 h-8 rounded-full mr-2">
                    <div class="bg-gray-100 p-2 rounded-lg max-w-[80%]">${text}</div>
                    `;

            }

            chatbotBody.appendChild(messageDiv);
            chatbotBody.scrollTop = chatbotBody.scrollHeight; // เลื่อนลงอัตโนมัติ
        }

        // ดักจับ Enter เพื่อส่งข้อความ
        userInput.addEventListener("keypress", function(event) {
            if (event.key === "Enter" && userInput.value.trim() !== "") {
                sendMessageToBot(userInput.value.trim());
                userInput.value = ""; // ล้างช่อง input
            }
        });
    });
</script>

{{-- fix style on page index --}}
<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .message img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }

    #chatbot-body {
        background-color: #f9fafb;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        max-height: 100%;
        overflow-y: auto;
    }

    #chatbot-body .message p {
        background-color: #e5e7eb;
        padding: 10px 14px;
        border-radius: 16px;
        max-width: 80%;
        word-break: break-word;
        font-size: 0.95rem;
    }

    #chatbot-body .message.user {
        justify-content: flex-end;
    }

    #chatbot-body .message.bot {
        justify-content: flex-start;
    }

    #chatbot-body .message div {
        background-color: #e5e7eb;
        padding: 10px 14px;
        border-radius: 16px;
        max-width: 80%;
        margin: 0;
        line-height: 1.4;
    }

    #chatbot-body .message.user p {
        background-color: #dbeafe;
    }

    #chatbot-body .message img {
        width: 32px;
        height: 32px;
        margin-right: 8px;
    }
</style>
