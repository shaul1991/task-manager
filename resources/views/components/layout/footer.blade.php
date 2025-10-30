<!-- Footer -->
<footer {{ $attributes->merge(['class' => 'border-t border-gray-200 bg-white py-6']) }}>
    <div class="px-6">
        <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
            <!-- Copyright -->
            <div class="text-sm text-gray-600">
                © {{ date('Y') }} Task Manager. All rights reserved.
            </div>

            <!-- Links -->
            <div class="flex gap-6">
                <a href="#" class="text-sm text-gray-600 hover:text-gray-900">
                    개인정보처리방침
                </a>
                <a href="#" class="text-sm text-gray-600 hover:text-gray-900">
                    이용약관
                </a>
                <a href="#" class="text-sm text-gray-600 hover:text-gray-900">
                    고객지원
                </a>
            </div>
        </div>
    </div>
</footer>
