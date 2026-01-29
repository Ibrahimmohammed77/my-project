@extends('layouts.app')

@section('title', 'مراجعة الصور')

@section('content')
<div class="bg-white rounded-3xl shadow-card border border-gray-100 overflow-hidden">
    <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
        <div>
            <h3 class="text-xl font-black text-gray-800">الصور قيد المراجعة</h3>
            <p class="text-sm text-gray-500 mt-1">قم بمراجعة الصور التي تم رفعها من قبل المشتركين والعملاء</p>
        </div>
        <div class="flex gap-2 bg-white p-1 rounded-xl border border-gray-200">
            <button class="px-4 py-1.5 rounded-lg text-sm font-bold bg-accent text-white shadow-soft">قيد الانتظار</button>
            <button class="px-4 py-1.5 rounded-lg text-sm font-bold text-gray-500 hover:bg-gray-50 transition-colors">مرفوضة</button>
        </div>
    </div>

    <div class="p-8">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
            @forelse($photos ?? [] as $photo)
                <div class="group relative aspect-square bg-gray-100 rounded-2xl overflow-hidden border border-gray-100">
                    <img src="{{ asset('storage/' . $photo->file_path) }}" alt="صورة للمراجعة" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    
                    <!-- Overlay Controls -->
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-between p-4">
                        <div class="flex justify-start">
                            <span class="text-[10px] bg-white/20 backdrop-blur-md text-white px-2 py-1 rounded-full border border-white/20">
                                {{ $photo->album->storageLibrary->user->name }}
                            </span>
                        </div>
                        <div class="flex gap-2">
                            <button onclick="approvePhoto({{ $photo->photo_id }})" class="flex-1 bg-green-500 text-white p-2 rounded-xl hover:bg-green-600 transition-colors shadow-lg">
                                <i class="fa-solid fa-check"></i>
                            </button>
                            <button onclick="rejectPhoto({{ $photo->photo_id }})" class="flex-1 bg-red-500 text-white p-2 rounded-xl hover:bg-red-600 transition-colors shadow-lg">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="lg:col-span-5 py-24 text-center">
                    <div class="w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-green-200">
                        <i class="fa-solid fa-check-double text-green-500 text-3xl"></i>
                    </div>
                    <h4 class="text-gray-800 font-bold">لا توجد صور بانتظار المراجعة</h4>
                    <p class="text-gray-500 text-sm mt-1">لقد قمت بمراجعة كافة الصور المرفوعة بنجاح!</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
    function approvePhoto(id) {
        if(confirm('هل أنت متأكد من الموافقة على هذه الصورة؟')) {
            // تنفيذ طلب الموافقة عبر Axios
            console.log('Approve photo:', id);
        }
    }

    function rejectPhoto(id) {
        let reason = prompt('يرجى إدخال سبب الرفض (اختياري):');
        if(reason !== null) {
            // تنفيذ طلب الرفض عبر Axios
            console.log('Reject photo:', id, 'reason:', reason);
        }
    }
</script>
@endpush
@endsection
