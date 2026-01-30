@extends('layouts.app')
@section('title', 'تفاصيل الكرت المدرسي')
@section('header', 'ربط الكرت بالألبومات')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-3xl shadow-card border border-gray-100 overflow-hidden mb-6">
            <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-black text-gray-800">تحرير الكرت: {{ $card->card_number }}</h3>
                    <p class="text-sm text-gray-500 mt-1">قم باختيار ألبومات المدرسة التي سيتم ربطها بهذا الكرت</p>
                </div>
                <div class="px-3 py-1 bg-accent/10 text-accent text-xs font-bold rounded-full border border-accent/20">
                    {{ $card->status->name ?? 'نشط' }}
                </div>
            </div>

            <div class="p-8">
                <form action="{{ route('school.cards.link-albums', $card->card_id) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[400px] overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($availableAlbums as $album)
                        <label class="group relative flex items-center p-4 bg-gray-50 rounded-2xl border border-gray-100 cursor-pointer hover:border-accent/40 hover:bg-white transition-all shadow-sm">
                            <input type="checkbox" name="album_ids[]" value="{{ $album->album_id }}" 
                                {{ in_array($album->album_id, $card->albums->pluck('album_id')->toArray()) ? 'checked' : '' }}
                                class="w-5 h-5 rounded-lg border-gray-300 text-accent focus:ring-accent accent-accent transition-all">
                            <div class="mr-4">
                                <span class="block text-sm font-bold text-gray-800 group-hover:text-accent transition-colors">{{ $album->name }}</span>
                                <span class="block text-[11px] text-gray-500 mt-0.5">{{ $album->photos_count ?? $album->photos()->count() }} صورة</span>
                            </div>
                        </label>
                        @endforeach
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end gap-3">
                        <x-button href="{{ route('school.cards.index') }}" variant="secondary">إلغاء</x-button>
                        <x-button type="submit" variant="primary">حفظ التغييرات</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
