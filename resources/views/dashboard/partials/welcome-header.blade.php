@props(['title', 'greeting', 'subtitle' => null, 'showActionButton' => false])

<div class="mb-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $title }}</h1>
            <div class="mt-2">
                <p class="text-lg text-gray-600">{{ $greeting }}</p>
                @if($subtitle)
                    <p class="text-sm text-gray-500 mt-1">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        @if($showActionButton)
            <div class="mt-4 sm:mt-0">
                {{ $slot }}
            </div>
        @endif
    </div>

    @if(isset($extra))
        <div class="mt-4">
            {{ $extra }}
        </div>
    @endif
</div>
