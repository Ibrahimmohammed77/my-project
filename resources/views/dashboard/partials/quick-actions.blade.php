@props(['title', 'actions' => []])

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4">{{ $title }}</h2>
    <div class="space-y-3">
        @foreach($actions as $action)
            @if($action['route'] && Route::has($action['route']))
                <a href="{{ route($action['route']) }}"
                   class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition group">
                    <div class="w-10 h-10 rounded-lg bg-{{ $action['color'] }}-50 flex items-center justify-center mr-3 group-hover:bg-{{ $action['color'] }}-100 transition">
                        <i class="{{ $action['icon'] }} text-{{ $action['color'] }}-600"></i>
                    </div>
                    <span class="text-sm text-gray-700">{{ $action['label'] }}</span>
                    <i class="fas fa-arrow-left text-gray-400 text-xs mr-auto opacity-0 group-hover:opacity-100 transition"></i>
                </a>
            @else
                <div class="flex items-center p-3">
                    <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center mr-3">
                        <i class="{{ $action['icon'] ?? 'fas fa-info-circle' }} text-gray-400"></i>
                    </div>
                    <span class="text-sm text-gray-500">{{ $action['label'] }}</span>
                </div>
            @endif
        @endforeach
    </div>
</div>
