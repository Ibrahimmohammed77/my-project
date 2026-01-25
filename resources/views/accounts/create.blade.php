@extends('layouts.app')

@section('title', 'إنشاء حساب جديد')

@section('page-title', 'إنشاء حساب جديد')

@section('content')
<div class="max-w-4xl">
    <form action="{{ route('accounts.store') }}" method="POST" class="bg-surface rounded-2xl shadow-soft border border-gray-100 overflow-hidden">
        @csrf
        <div class="p-6 border-b border-gray-100">
            <h3 class="font-bold text-lg text-primary">تفاصيل الحساب</h3>
            <p class="text-sm text-gray-500 mt-1">أدخل معلومات الحساب الجديد</p>
        </div>
        
        <div class="p-6">
            @include('accounts.form')
        </div>
        
        <div class="p-6 border-t border-gray-100 bg-gray-50 flex items-center justify-end gap-3">
            <a href="{{ route('accounts.index') }}" class="px-5 py-2.5 text-gray-600 hover:text-gray-800 font-medium transition-colors">
                إلغاء
            </a>
            <button type="submit" class="bg-primary hover:bg-primary-light text-white px-6 py-2.5 rounded-xl font-semibold shadow-lg shadow-primary/20 transition-all active:scale-95">
                إنشاء الحساب
            </button>
        </div>
    </form>
</div>
@endsection
