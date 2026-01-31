@props(['title', 'value', 'icon', 'color' => 'gray', 'trend' => null])

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
    <div class="flex items-center justify-between">
        <div>
            <p class="text-sm font-medium text-gray-500 mb-1">{{ $title }}</p>
            <p class="text-2xl font-bold text-gray-900" data-stat="{{ Str::slug($title) }}">
                {{ $value }}
            </p>

            @if($trend !== null)
                <div class="mt-2 flex items-center text-sm">
                    @if($trend > 0)
                        <span class="text-green-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ $trend }}%
                        </span>
                    @elseif($trend < 0)
                        <span class="text-red-600 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M14.707 10.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 12.586V5a1 1 0 012 0v7.586l2.293-2.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ abs($trend) }}%
                        </span>
                    @endif
                </div>
            @endif
        </div>

        <div class="p-3 {{ $color }} rounded-lg">
            <i class="{{ $icon }} text-white text-xl"></i>
        </div>
    </div>
</div>
