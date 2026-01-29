@extends('layouts.app')

@section('title', 'تفاصيل الكرت')

@section('content')
<div class="space-y-6">
    <!-- Card Info -->
    <div class="bg-white rounded-3xl shadow-card border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <div>
                <h3 class="text-xl font-black text-gray-800">تفاصيل الكرت #{{ $card->card_number }}</h3>
                <p class="text-sm text-gray-500 mt-1">عرض حالة الكرت والألبومات المرتبطة</p>
            </div>
            <span class="px-4 py-2 rounded-xl font-bold text-sm {{ $card->is_active ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-gray-100 text-gray-500 border border-gray-200' }}">
                {{ $card->status->name ?? ($card->is_active ? 'نشط' : 'غير مفعل') }}
            </span>
        </div>

        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-6">
                <div>
                    <label class="text-xs text-gray-400 font-bold uppercase tracking-wider">رقم البطاقة (UUID)</label>
                    <p class="text-gray-800 font-mono text-lg font-bold mt-1">{{ $card->card_uuid }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-400 font-bold uppercase tracking-wider">نوع البطاقة</label>
                    <p class="text-gray-800 font-bold mt-1">{{ $card->type->name ?? 'غير محدد' }}</p>
                </div>
                <div>
                    <label class="text-xs text-gray-400 font-bold uppercase tracking-wider">تاريخ الانتهاء</label>
                    <p class="text-gray-800 font-bold mt-1">{{ $card->expiry_date ? $card->expiry_date->format('Y-m-d') : 'دائم' }}</p>
                </div>
            </div>
            
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-link text-accent"></i>
                    الألبومات المرتبطة
                </h4>
                <div class="space-y-3">
                    @forelse($card->albums as $album)
                        <div class="flex items-center justify-between bg-white p-3 rounded-xl border border-gray-100 shadow-soft">
                            <span class="font-bold text-gray-700">{{ $album->name }}</span>
                            <a href="{{ route('studio.albums.show', $album->album_id) }}" class="text-accent hover:text-accent-hover text-xs font-bold">عرض الألبوم</a>
                        </div>
                    @empty
                        <p class="text-gray-400 text-sm py-4 text-center">لا توجد ألبومات مرتبطة بهذا الكرت حالياً</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Link Albums Form -->
    <div class="bg-white rounded-3xl shadow-card border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100">
            <h3 class="text-xl font-black text-gray-800">ربط ألبومات جديدة</h3>
            <p class="text-sm text-gray-500 mt-1">اختر الألبومات التي تريد للحق لليحامل لهذا الكرت الوصول إليها</p>
        </div>
        
        <form action="{{ route('studio.cards.link-albums', $card->card_id) }}" method="POST" class="p-8">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                @foreach($availableAlbums as $album)
                    <label class="relative flex items-center p-4 rounded-2xl border-2 transition-all cursor-pointer @if($card->albums->contains($album->album_id)) border-accent bg-accent/5 @else border-gray-100 hover:border-gray-200 @endif">
                        <input type="checkbox" name="album_ids[]" value="{{ $album->album_id }}" 
                            class="w-5 h-5 text-accent rounded focus:ring-accent accent-accent"
                            @checked($card->albums->contains($album->album_id))>
                        <span class="mr-3 font-bold text-gray-700">{{ $album->name }}</span>
                    </label>
                @endforeach
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-accent text-white px-8 py-3 rounded-xl font-bold hover:bg-accent-hover transition-all shadow-lg shadow-accent/20 flex items-center gap-2">
                    <i class="fa-solid fa-save"></i>
                    تحديث الألبومات المرتبطة
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
