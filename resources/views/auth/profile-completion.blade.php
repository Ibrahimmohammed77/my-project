@extends('layouts.app')

@section('title', 'إكمال الملف الشخصي')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50/50">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-3xl shadow-xl border border-gray-100 relative overflow-hidden">
        <!-- Decorative Background dots -->
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-accent/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-blue-500/5 rounded-full blur-3xl"></div>

        <div class="text-center relative">
            <div class="mx-auto h-20 w-20 bg-accent/10 rounded-2xl flex items-center justify-center mb-6 transform rotate-3 hover:rotate-0 transition-transform duration-300">
                <i class="fas fa-user-edit text-3xl text-accent"></i>
            </div>
            <h2 class="text-3xl font-black text-gray-900 tracking-tight mb-2">إكمال الملف الشخصي</h2>
            <p class="text-gray-500 text-sm">نحتاج لبعض المعلومات الإضافية لنبدأ رحلتك معنا</p>
        </div>

        <div class="mt-8 space-y-6 relative">
            @if(Auth::user()->hasRole('studio_owner'))
            <div class="p-4 bg-orange-50 border border-orange-100 rounded-2xl flex items-start gap-4">
                <div class="h-10 w-10 bg-orange-100 rounded-xl flex items-center justify-center shrink-0">
                    <i class="fas fa-building text-orange-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-orange-900 text-sm">إعداد الاستوديو</h3>
                    <p class="text-xs text-orange-700 mt-1 leading-relaxed">بصفتك صاحب استوديو، يجب عليك إنشاء ملف تعريف للاستوديو الخاص بك لتتمكن من رفع الصور وإدارة الألبومات.</p>
                </div>
            </div>
            
            <form action="{{ route('studio.profile.update') }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <x-form.input name="name" label="اسم الاستوديو" required icon="fa-camera" placeholder="مثلاً: استوديو الأضواء" />
                <x-form.input name="phone" label="رقم التواصل" icon="fa-phone" placeholder="05xxxxxxxx" />
                <x-form.input name="address" label="العنوان" icon="fa-map-marker-alt" placeholder="المدينة، الحي" />
                
                <button type="submit" class="w-full py-4 bg-gray-900 text-white rounded-2xl font-bold shadow-lg shadow-gray-900/20 hover:bg-gray-800 transform hover:-translate-y-1 transition-all flex items-center justify-center gap-2 group">
                    <span>إنشاء ملف الاستوديو</span>
                    <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                </button>
            </form>
            @elseif(Auth::user()->hasRole('school_owner'))
            <!-- Similar block for school owners -->
             <div class="p-4 bg-blue-50 border border-blue-100 rounded-2xl flex items-start gap-4">
                <div class="h-10 w-10 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
                    <i class="fas fa-school text-blue-600"></i>
                </div>
                <div>
                    <h3 class="font-bold text-blue-900 text-sm">إعداد المدرسة</h3>
                    <p class="text-xs text-blue-700 mt-1 leading-relaxed">يرجى إضافة بيانات المدرسة لتتمكن من إدارة الصور والطلاب.</p>
                </div>
            </div>
            <x-button variant="primary" class="w-full py-4">تواصل مع الإدارة لإكمال التفعيل</x-button>
            @else
            <div class="text-center py-10">
                <p class="text-gray-500 mb-6">يرجى التواصل مع مدير النظام لإكمال إعدادات حسابك.</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-button type="submit" variant="secondary" class="w-full">تسجيل الخروج</x-button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
