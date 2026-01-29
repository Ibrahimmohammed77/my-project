@extends('layouts.app')

@section('title', 'لوحة تحكم الاستوديو')

@section('content')
<div class="space-y-8 animate-in fade-in duration-700">
    <!-- Hero Stats Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Albums -->
        <div class="bg-white rounded-3xl p-6 shadow-card border border-gray-100 group hover:border-accent/30 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-images text-xl"></i>
                </div>
                <span class="text-xs font-bold text-green-500 bg-green-50 px-2 py-1 rounded-full">+12%</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">إجمالي الألبومات</h3>
            <p class="text-2xl font-black text-gray-800 mt-1">{{ $stats['total_albums'] ?? 0 }}</p>
        </div>

        <!-- Total Photos -->
        <div class="bg-white rounded-3xl p-6 shadow-card border border-gray-100 group hover:border-accent/30 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-camera text-xl"></i>
                </div>
                <span class="text-xs font-bold text-indigo-500 bg-indigo-50 px-2 py-1 rounded-full">نشط</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">إجمالي الصور</h3>
            <p class="text-2xl font-black text-gray-800 mt-1">{{ $stats['total_photos'] ?? 0 }}</p>
        </div>

        <!-- Active Cards -->
        <div class="bg-white rounded-3xl p-6 shadow-card border border-gray-100 group hover:border-accent/30 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-accent/10 flex items-center justify-center text-accent group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-id-card text-xl"></i>
                </div>
                <span class="text-xs font-bold text-accent bg-accent/5 px-2 py-1 rounded-full">{{ $stats['active_cards'] ?? 0 }} بطاقة</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">البطاقات النشطة</h3>
            <p class="text-2xl font-black text-gray-800 mt-1">{{ $stats['active_cards'] ?? 0 }}</p>
        </div>

        <!-- Commissions -->
        <div class="bg-white rounded-3xl p-6 shadow-card border border-gray-100 group hover:border-accent/30 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-wallet text-xl"></i>
                </div>
                <span class="text-xs font-bold text-emerald-500 bg-emerald-50 px-2 py-1 rounded-full">سحب</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">إجمالي العمولات</h3>
            <p class="text-2xl font-black text-gray-800 mt-1">{{ number_format($stats['total_commissions'] ?? 0, 2) }} ر.س</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Photo Review Summary -->
        <div class="lg:col-span-2 bg-white rounded-3xl shadow-card border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">صور قيد المراجعة</h3>
                    <p class="text-sm text-gray-500 mt-1">لديك {{ $stats['pending_photos_count'] ?? 0 }} صور تحتاج لموافقتك</p>
                </div>
                <a href="{{ route('studio.photo-review.pending') }}" class="text-sm font-bold text-accent hover:text-accent-hover transition-colors">عرض الكل</a>
            </div>
            <div class="p-8">
                @if(($stats['pending_photos_count'] ?? 0) > 0)
                    <div class="flex items-center justify-center py-12 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-white rounded-full shadow-soft flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-clock-rotate-left text-accent text-2xl animate-pulse"></i>
                            </div>
                            <p class="text-gray-600 font-medium">بانتظار المراجعة</p>
                            <a href="{{ route('studio.photo-review.pending') }}" class="mt-4 inline-flex items-center justify-center px-6 py-2 bg-accent text-white rounded-xl font-bold hover:bg-accent-hover transition-all shadow-lg shadow-accent/20">
                                ابدأ المراجعة الآن
                            </a>
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fa-solid fa-check text-green-500 text-2xl"></i>
                        </div>
                        <p class="text-gray-500">لا توجد صور معلقة حالياً. عمل رائع!</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Plan & Storage -->
        <div class="bg-primary rounded-3xl shadow-card p-8 text-white relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-white/5 to-transparent pointer-events-none"></div>
            <h3 class="text-xl font-bold mb-6">مساحة التخزين</h3>
            
            <div class="space-y-6">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-400">المساحة المستخدمة</span>
                        <span class="text-sm font-bold">75%</span>
                    </div>
                    <div class="h-2 bg-white/10 rounded-full overflow-hidden">
                        <div class="h-full bg-accent rounded-full transition-all duration-1000" style="width: 75%"></div>
                    </div>
                </div>

                <div class="bg-white/5 rounded-2xl p-4 border border-white/10">
                    <p class="text-xs text-gray-400 mb-1">الخطة الحالية</p>
                    <p class="text-sm font-bold">باقة الاستوديو الاحترافية</p>
                </div>

                <a href="{{ route('studio.storage.index') }}" class="w-full flex items-center justify-center px-6 py-3 bg-white text-primary rounded-xl font-black hover:bg-gray-100 transition-all shadow-xl">
                    إدارة المساحات
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
