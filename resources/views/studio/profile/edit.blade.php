@extends('layouts.app')

@section('title', 'بيانات الاستوديو')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-3xl shadow-card border border-gray-100 overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-xl font-black text-gray-800">تحديث بيانات الاستوديو</h3>
            <p class="text-sm text-gray-500 mt-1">تعديل معلومات الاستوديو والشعار والعنوان</p>
        </div>

        <form action="{{ route('studio.profile.update') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name (User Name) -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">اسم صاحب الاستوديو</label>
                    <input type="text" name="name" value="{{ Auth::user()->name }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all">
                </div>

                <!-- Email (Read Only) -->
                <div>
                    <label class="block text-sm font-bold text-gray-400 mb-2">البريد الإلكتروني (لا يمكن تغييره)</label>
                    <input type="email" value="{{ Auth::user()->email }}" readonly class="w-full bg-gray-100 border border-gray-200 rounded-xl px-4 py-3 text-gray-400 cursor-not-allowed">
                </div>

                <!-- Phone (Read Only) -->
                <div>
                    <label class="block text-sm font-bold text-gray-400 mb-2">رقم الجوال (لا يمكن تغييره)</label>
                    <input type="text" value="{{ Auth::user()->phone }}" readonly class="w-full bg-gray-100 border border-gray-200 rounded-xl px-4 py-3 text-gray-400 cursor-not-allowed">
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">وصف الاستوديو</label>
                    <textarea name="description" rows="4" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all">{{ $studio->description ?? '' }}</textarea>
                </div>

                <!-- City -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">المدينة</label>
                    <input type="text" name="city" value="{{ $studio->city ?? '' }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all">
                </div>

                <!-- Address -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">العنوان بالتفصيل</label>
                    <input type="text" name="address" value="{{ $studio->address ?? '' }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-accent/20 focus:border-accent transition-all">
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex justify-end">
                <button type="submit" class="bg-primary text-white px-10 py-3 rounded-xl font-black hover:bg-primary-dark transition-all shadow-xl shadow-primary/20">
                    حفظ التغييرات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
