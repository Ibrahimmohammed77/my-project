@props(['id', 'title'])

<div id="{{ $id }}" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="document.getElementById('{{ $id }}').classList.add('hidden')"></div>

    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-right shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
            <!-- Header -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2" id="{{ $id }}-title">
                    <span class="w-2 h-6 bg-accent rounded-full"></span>
                    <span>{{ $title }}</span>
                </h3>
                <button onclick="document.getElementById('{{ $id }}').classList.add('hidden')" class="text-gray-400 hover:text-gray-500 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Body -->
            <div class="px-6 py-6">
                {{ $slot }}
            </div>

            <!-- Footer -->
            @if(isset($footer))
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex flex-row-reverse gap-3">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>
