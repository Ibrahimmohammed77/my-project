@props(['headers'])

@php
    $tableId = $attributes->get('id', 'table');
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    @foreach($headers as $header)
                        <th class="{{ $header['class'] ?? 'text-right' }} px-6 py-4 text-xs font-extrabold text-gray-800 uppercase tracking-wider">
                            {{ $header['name'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody id="{{ $tableId }}-tbody" class="divide-y divide-gray-100 bg-white">
                {{ $slot }}
            </tbody>
        </table>
    </div>
    
    <!-- Loading State -->
    <div id="loading-state" class="hidden p-12 text-center text-gray-400">
        <i class="fas fa-circle-notch fa-spin text-2xl mb-3 text-accent"></i>
        <p>جاري تحميل البيانات...</p>
    </div>

    <!-- Empty State -->
    <div id="empty-state" class="hidden p-12 text-center">
        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
            <i class="fas fa-search text-xl text-gray-400"></i>
        </div>
        <h3 class="text-base font-bold text-gray-800 mb-1">لا توجد نتائج</h3>
        <p class="text-sm text-gray-500">لم يتم العثور على بيانات تطابق بحثك.</p>
    </div>
</div>
