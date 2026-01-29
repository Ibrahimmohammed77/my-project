@extends('layouts.app')

@section('title', 'إدارة الكروت')

@section('content')
<div class="bg-white rounded-3xl shadow-card border border-gray-100 overflow-hidden">
    <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
        <div>
            <h3 class="text-xl font-black text-gray-800">إدارة كروت الاستوديو</h3>
            <p class="text-sm text-gray-500 mt-1">تتبع حالة الكروت وتعيينها للعملاء</p>
        </div>
        <div class="flex gap-3">
            <button class="bg-white text-gray-700 border border-gray-200 px-6 py-2.5 rounded-xl font-bold hover:bg-gray-50 transition-all shadow-soft flex items-center gap-2">
                <i class="fa-solid fa-file-export"></i>
                تصدير
            </button>
        </div>
    </div>

    <div class="p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($cards ?? [] as $card)
                <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 hover:border-accent/30 transition-all group">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-gray-400 group-hover:text-accent transition-colors shadow-soft">
                            <i class="fa-solid fa-credit-card"></i>
                        </div>
                        <span class="text-[10px] font-bold px-2 py-1 rounded-full {{ $card->status == 'active' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-gray-100 text-gray-500 border border-gray-200' }}">
                            {{ $card->status == 'active' ? 'نشط' : 'غير مفعل' }}
                        </span>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs text-gray-400 font-medium">رقم البطاقة</p>
                        <p class="font-mono font-bold text-gray-800">{{ $card->card_number }}</p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200/50 flex items-center justify-between">
                        <span class="text-[10px] text-gray-500">{{ $card->type->name ?? 'عادية' }}</span>
                        <button class="text-accent hover:text-accent-hover font-bold text-xs transition-colors">التفاصيل</button>
                    </div>
                </div>
            @empty
                <div class="lg:col-span-4 py-20 text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-gray-300">
                        <i class="fa-solid fa-id-card text-gray-300 text-3xl"></i>
                    </div>
                    <h4 class="text-gray-800 font-bold">لا توجد كروت حالياً</h4>
                    <p class="text-gray-500 text-sm mt-1">يمكنك البدء بتوليد كروت جديدة لتوزيعها على عملائك</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
