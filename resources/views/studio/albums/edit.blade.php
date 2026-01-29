@extends('layouts.app')

@section('title', 'تعديل الألبوم')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-3xl shadow-card border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-xl font-black text-gray-800">تعديل الألبوم: {{ $album->name }}</h3>
            <p class="text-sm text-gray-500 mt-1">تحديث إعدادات الألبوم وربطه بالكروت</p>
        </div>

        <form action="{{ route('studio.albums.update', $album->album_id) }}" method="POST" class="p-8 space-y-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">اسم الألبوم</label>
                    <input type="text" name="name" value="{{ old('name', $album->name) }}" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all">
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">الوصف</label>
                    <textarea name="description" rows="3" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all">{{ old('description', $album->description) }}</textarea>
                </div>

                <!-- Storage Library -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">مكتبة التخزين المرتبطة</label>
                    <select name="storage_library_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all">
                        <option value="">اختر مكتبة تخزين (اختياري)</option>
                        @foreach($libraries ?? [] as $library)
                            <option value="{{ $library->storage_library_id }}" {{ $album->storage_library_id == $library->storage_library_id ? 'selected' : '' }}>{{ $library->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Visibility -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">حالة الظهور</label>
                    <div class="flex items-center gap-4 mt-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_visible" value="1" {{ $album->is_visible ? 'checked' : '' }} class="text-accent focus:ring-accent">
                            <span class="text-sm text-gray-600 font-medium">مرئي للجميع</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="is_visible" value="0" {{ !$album->is_visible ? 'checked' : '' }} class="text-accent focus:ring-accent">
                            <span class="text-sm text-gray-600 font-medium">مخفي</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Linked Cards -->
            <div class="border-t border-gray-100 pt-8">
                <h4 class="text-lg font-bold text-gray-800 mb-4">ربط الألبوم بالكروت</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-60 overflow-y-auto p-2 bg-gray-50 rounded-2xl border border-gray-100">
                    @php $linkedCardIds = $album->cards->pluck('card_id')->toArray(); @endphp
                    @forelse($cards ?? [] as $card)
                        <label class="flex items-center gap-3 p-3 bg-white border border-gray-100 rounded-xl hover:border-accent/40 cursor-pointer transition-all shadow-soft group">
                            <input type="checkbox" name="card_ids[]" value="{{ $card->card_id }}" {{ in_array($card->card_id, $linkedCardIds) ? 'checked' : '' }} class="rounded text-accent focus:ring-accent">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-700 group-hover:text-accent transition-colors">{{ $card->card_number }}</span>
                                <span class="text-[10px] text-gray-400 uppercase tracking-tighter">{{ $card->type->name ?? 'عادية' }}</span>
                            </div>
                        </label>
                    @empty
                        <div class="col-span-full py-4 text-center">
                            <p class="text-gray-400 text-sm italic">لا توجد كروت متاحة للربط حالياً</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('studio.albums.index') }}" class="bg-gray-100 text-gray-500 px-8 py-3 rounded-xl font-bold hover:bg-gray-200 transition-all">إلغاء</a>
                <button type="submit" class="bg-primary text-white px-10 py-3 rounded-xl font-black hover:bg-primary-dark transition-all shadow-xl shadow-primary/20">
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
