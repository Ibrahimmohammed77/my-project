@extends('layouts.app')

@section('title', 'إدارة الألبومات')

@section('content')
<div class="bg-white rounded-3xl shadow-card border border-gray-100 overflow-hidden">
    <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
        <div>
            <h3 class="text-xl font-black text-gray-800">الألبومات الخاصة بالاستوديو</h3>
            <p class="text-sm text-gray-500 mt-1">عرض وتحرير ألبوماتك المسجلة</p>
        </div>
        <a href="{{ route('studio.albums.create') }}" class="bg-accent text-white px-6 py-2.5 rounded-xl font-bold hover:bg-accent-hover transition-all shadow-lg shadow-accent/20 flex items-center gap-2">
            <i class="fa-solid fa-plus"></i>
            إنشاء ألبوم جديد
        </a>
    </div>

    <div class="p-8">
        <!-- Table or Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($albums ?? [] as $album)
                <div class="group bg-gray-50 rounded-2xl overflow-hidden border border-gray-100 hover:border-accent/30 transition-all duration-300">
                    <div class="aspect-video bg-gray-200 relative overflow-hidden">
                        <img src="{{ $album->cover_image ?? 'https://via.placeholder.com/400x225' }}" alt="{{ $album->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        <div class="absolute bottom-4 right-4 text-white">
                            <p class="text-xs font-medium opacity-80">{{ $album->photos_count ?? 0 }} صورة</p>
                        </div>
                    </div>
                    <div class="p-5">
                        <h4 class="font-bold text-gray-800 mb-1">{{ $album->name }}</h4>
                        <p class="text-xs text-gray-500 line-clamp-2 mb-4">{{ $album->description }}</p>
                        <div class="flex items-center justify-between pt-4 border-t border-gray-200/50">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-accent">{{ $album->is_visible ? 'عام' : 'مخفي' }}</span>
                            <div class="flex gap-2">
                                <a href="{{ route('studio.albums.edit', $album->album_id) }}" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-accent hover:border-accent transition-all flex items-center justify-center shadow-soft">
                                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                                </a>
                                <form action="{{ route('studio.albums.destroy', $album->album_id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الألبوم؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-red-500 hover:border-red-500 transition-all flex items-center justify-center shadow-soft">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="lg:col-span-3 py-20 text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-gray-300">
                        <i class="fa-solid fa-images text-gray-300 text-3xl"></i>
                    </div>
                    <h4 class="text-gray-800 font-bold">لا توجد ألبومات حالياً</h4>
                    <p class="text-gray-500 text-sm mt-1">ابدأ بإنشاء أول ألبوم لك الآن</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
