@extends('layouts.app')

@section('title', 'إنشاء ألبوم جديد')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-3xl shadow-card border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-xl font-black text-gray-800">إنشاء ألبوم جديد</h3>
            <p class="text-sm text-gray-500 mt-1">أضف ألبوماً جديداً وقم بتخصيص إعداداته</p>
        </div>

        <form action="{{ route('studio.albums.store') }}" method="POST" class="p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">اسم الألبوم</label>
                    <input type="text" name="name" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all" placeholder="مثال: حفل تخرج 2024">
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">الوصف</label>
                    <textarea name="description" rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all" placeholder="أضف وصفاً طويلاً للألبوم..."></textarea>
                </div>

                <!-- Storage Library -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">مكتبة التخزين المرتبطة</label>
                    <select name="storage_library_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all">
                        <option value="">اختر مكتبة تخزين (اختياري)</option>
                        @foreach($libraries ?? [] as $library)
                            <option value="{{ $library->storage_library_id }}">{{ $library->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Visibility -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">حالة الظهور</label>
                    <div class="flex items-center gap-4 mt-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_visible" value="1" checked class="text-accent focus:ring-accent">
                            <span class="text-sm text-gray-600 font-medium">مرئي للجميع</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_visible" value="0" class="text-accent focus:ring-accent">
                            <span class="text-sm text-gray-600 font-medium">مخفي</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('studio.albums.index') }}" class="bg-gray-100 text-gray-500 px-8 py-3 rounded-xl font-bold hover:bg-gray-200 transition-all">إلغاء</a>
                <button type="submit" class="bg-primary text-white px-10 py-3 rounded-xl font-black hover:bg-primary-dark transition-all shadow-xl shadow-primary/20">
                    حفظ الألبوم
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
