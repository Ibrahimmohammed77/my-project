@extends('layouts.app')

@section('title', 'إدارة المستخدمين')

@section('page-title', 'إدارة المستخدمين')

@section('content')
<div class="bg-surface rounded-2xl shadow-soft border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-lg text-primary">الحسابات</h3>
            <p class="text-sm text-gray-500 mt-1">إدارة حسابات المستخدمين</p>
        </div>
        <a href="{{ route('accounts.create') }}" class="bg-primary hover:bg-primary-light text-white px-5 py-2.5 rounded-xl font-semibold shadow-lg shadow-primary/20 flex items-center gap-2 transition-all active:scale-95 w-fit">
            <i class="fa-solid fa-plus"></i>
            <span>إضافة حساب جديد</span>
        </a>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-right">
            <thead class="bg-gray-50 text-gray-500 text-xs font-bold uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-4">الاسم</th>
                    <th class="px-6 py-4">اسم المستخدم</th>
                    <th class="px-6 py-4">جهة الاتصال</th>
                    <th class="px-6 py-4">الحالة</th>
                    <th class="px-6 py-4">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($accounts as $account)
                <tr class="hover:bg-gray-50/50 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($account->profile_image)
                            <img src="{{ $account->profile_image }}" class="w-10 h-10 rounded-full border-2 border-gray-200" alt="{{ $account->full_name }}">
                            @else
                            <div class="w-10 h-10 rounded-full bg-accent text-white flex items-center justify-center font-bold text-sm">
                                {{ substr($account->full_name, 0, 2) }}
                            </div>
                            @endif
                            <div>
                                <p class="font-bold text-primary text-sm">{{ $account->full_name }}</p>
                                <p class="text-xs text-gray-400">{{ $account->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-600">{{ $account->username }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $account->phone }}</td>
                    <td class="px-6 py-4">
                        @if($account->status)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> {{ $account->status->name }}
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">
                            غير معروف
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('accounts.edit', $account->account_id) }}" class="p-2 text-gray-500 hover:bg-accent/10 hover:text-accent rounded-lg transition-colors" title="تعديل">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('accounts.destroy', $account->account_id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الحساب؟')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-500 hover:bg-red-50 hover:text-red-600 rounded-lg transition-colors" title="حذف">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center justify-center text-gray-400">
                            <i class="fa-solid fa-users text-4xl mb-3"></i>
                            <p class="text-sm font-medium">لا توجد حسابات</p>
                            <p class="text-xs mt-1">ابدأ بإضافة حساب جديد</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
