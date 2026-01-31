@extends('layouts.app')
@section('title', isset($group) ? 'كروت المجموعة: ' . $group->name : 'إدارة كافة الكروت')
@section('header', 'إدارة الكروت')

@section('content')
    @if(isset($group))
    <input type="hidden" id="group-id" value="{{ $group->group_id }}">
    @endif
    
    <x-page-header title="{{ isset($group) ? 'كروت المجموعة: ' . $group->name : 'إدارة كافة الكروت' }}">
            <x-button onclick="history.back()" variant="secondary">
                <i class="fas fa-arrow-right text-xs"></i>
                <span>العودة للمجموعات</span>
            </x-button>
            <x-button onclick="showCreateModal()" variant="primary">
                <i class="fas fa-plus text-xs"></i>
                <span>{{ isset($group) ? 'إضافة كرت فرعي' : 'كرت جديد' }}</span>
            </x-button>
            @if(isset($group))
            <x-button onclick="showCreateGroupModal()" variant="secondary">
                <i class="fas fa-folder-plus text-xs"></i>
                <span>إضافة فرع فرعي</span>
            </x-button>
            @endif

        
        <div class="relative min-w-[240px]">
            <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" id="search" placeholder="بحث برقم الكرت..." class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
        </div>
    </x-page-header>

    <x-table :headers="[
        ['name' => 'رقم الكرت / UUID', 'class' => 'w-1/4'],
        ['name' => 'الحامل', 'class' => 'w-1/6'],
        ['name' => 'النوع / الحالة'],
        ['name' => 'التنشيط / الانتهاء'],
        ['name' => 'إجراءات', 'class' => 'text-center']
    ]" id="cards">
        <!-- JS renders rows here -->
    </x-table>

    {{-- نافذة إضافة/تعديل كرت --}}
    <x-modal id="modal" title="إضافة كرت جديد">
        <form id="modal-form" class="space-y-4">
            <input type="hidden" name="id">
            
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700">رقم الكرت</label>
                    <input type="text" name="card_number" required placeholder="مثال: CARD-001" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm font-mono">
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700">نوع الكرت</label>
                    <select name="card_type_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
                        @foreach($types as $type)
                            <option value="{{ $type->lookup_value_id }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700">تاريخ التنشيط</label>
                    <input type="datetime-local" name="activation_date" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-700">تاريخ الانتهاء</label>
                    <input type="date" name="expiry_date" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
                </div>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-gray-700">الحالة</label>
                <select name="card_status_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
                    @foreach($statuses as $status)
                        <option value="{{ $status->lookup_value_id }}">{{ $status->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-gray-700">ملاحظات</label>
                <textarea name="notes" rows="2" placeholder="ملاحظات إضافية..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <x-button type="button" onclick="closeModal()" variant="secondary">إلغاء</x-button>
                <x-button type="submit" variant="primary">حفظ الكرت</x-button>
            </div>
        </form>
    </x-modal>
    
    {{-- نافذة إضافة/تعديل فرع فرعي (مجموعة) --}}
    <x-modal id="group-modal" title="إضافة فرع فرعي جديد">
        <form id="group-modal-form" class="space-y-4">
            <input type="hidden" name="id">
            
            <div class="space-y-2">
                <label class="text-sm font-semibold text-gray-700">اسم الفرع</label>
                <input type="text" name="name" required placeholder="مثال: دفعة 2024 - المستوى الأول" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-gray-700">الوصف</label>
                <textarea name="description" rows="3" placeholder="وصف اختياري للفرع..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm resize-none"></textarea>
            </div>

            <div class="space-y-2">
                <label class="text-sm font-semibold text-gray-700">عدد الكروت المتاحة</label>
                <input type="number" name="sub_card_available" min="0" value="0" required placeholder="مثال: 30" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <x-button type="button" onclick="closeGroupModal()" variant="secondary">إلغاء</x-button>
                <x-button type="submit" variant="primary">حفظ الفرع</x-button>
            </div>
        </form>
    </x-modal>

@push('scripts')
    @vite('resources/js/spa/modules/cards/index.js')
@endpush
@endsection
