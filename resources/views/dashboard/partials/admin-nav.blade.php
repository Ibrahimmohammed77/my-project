<nav class="bg-white border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <span class="text-xl font-bold text-blue-600">لوحة التحكم</span>
                </div>
            </div>
            <div class="flex items-center">
                <span class="text-gray-700 ml-4">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-red-600 hover:text-red-800">خروج</button>
                </form>
            </div>
        </div>
    </div>
</nav>
