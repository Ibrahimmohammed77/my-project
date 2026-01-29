@extends('layouts.app')

@section('title', 'تخصيص مساحة التخزين')

@section('content')
<div class="bg-white rounded-3xl shadow-card border border-gray-100 overflow-hidden">
    <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
        <div>
            <h3 class="text-xl font-black text-gray-800">إدارة مكتبات التخزين</h3>
            <p class="text-sm text-gray-500 mt-1">تخصيص مساحات للمشتركين والعملاء</p>
        </div>
        <button class="bg-accent text-white px-6 py-2.5 rounded-xl font-bold hover:bg-accent-hover transition-all shadow-lg shadow-accent/20 flex items-center gap-2">
            <i class="fa-solid fa-plus"></i>
            إنشاء مكتبة جديدة
        </button>
    </div>

    <div class="p-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Summary Stats -->
            <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                    <p class="text-xs text-gray-500 font-bold mb-1 uppercase tracking-widest">إجمالي المساحة المخصصة</p>
                    <p class="text-2xl font-black text-gray-800">500 GB</p>
                </div>
                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                    <p class="text-xs text-gray-500 font-bold mb-1 uppercase tracking-widest">المستخدم حالياً</p>
                    <p class="text-2xl font-black text-accent">128 GB</p>
                </div>
                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                    <p class="text-xs text-gray-500 font-bold mb-1 uppercase tracking-widest">المكتبات النشطة</p>
                    <p class="text-2xl font-black text-gray-800">12</p>
                </div>
            </div>

            @forelse($libraries ?? [] as $library)
                <div class="bg-white rounded-2xl p-6 border border-gray-100 hover:border-accent/30 transition-all shadow-soft group">
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-accent/5 text-accent flex items-center justify-center text-xl group-hover:bg-accent group-hover:text-white transition-all">
                                <i class="fa-solid fa-database"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">{{ $library->name }}</h4>
                                <p class="text-xs text-gray-500">العميل: {{ $library->user->name }}</p>
                            </div>
                        </div>
                        <button class="text-gray-400 hover:text-accent transition-colors">
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </button>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center justify-between mb-2 text-xs">
                                <span class="text-gray-500">استهلاك المساحة</span>
                                <span class="font-bold text-gray-800">{{ round(($library->used_storage / $library->storage_limit) * 100, 1) }}%</span>
                            </div>
                            <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-accent rounded-full" style="width: {{ ($library->used_storage / $library->storage_limit) * 100 }}%"></div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>{{ number_format($library->used_storage / 1024 / 1024, 2) }} MB مستخدمة</span>
                            <span>الحد الأقصى: {{ number_format($library->storage_limit / 1024 / 1024, 0) }} MB</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="lg:col-span-2 py-20 text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-gray-300">
                        <i class="fa-solid fa-server text-gray-300 text-3xl"></i>
                    </div>
                    <h4 class="text-gray-800 font-bold">لا توجد مكتبات تخزين</h4>
                    <p class="text-gray-500 text-sm mt-1">ابدأ بتخصيص مساحات تخزين لعملائك لتمكينهم من رفع الصور</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
