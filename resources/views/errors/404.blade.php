@extends('layouts.app')

@section('title', '404 - الصفحة غير موجودة')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[60vh] text-center space-y-6">
    <div class="text-9xl font-bold text-gray-200">404</div>
    <div class="space-y-2">
        <h1 class="text-2xl font-bold text-gray-800">عذراً، الصفحة غير موجودة</h1>
        <p class="text-gray-500">يبدو أنك حاولت الوصول إلى صفحة غير متوفرة أو تم نقلها.</p>
    </div>
    <a href="{{ route('home') }}" class="px-6 py-3 bg-accent text-white rounded-xl shadow-lg shadow-accent/20 hover:bg-accent-dark transition-all">
        العودة للرئيسية
    </a>
</div>
@endsection
