@extends('layouts.app')

@section('title', 'لوحة التحكم')

@section('content')
<!-- Welcome Section -->
<div class="relative bg-gradient-to-r from-primary to-primary-light rounded-3xl p-8 mb-8 text-white shadow-xl overflow-hidden">
    <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full blur-3xl -mr-16 -mt-16"></div>
    <div class="absolute bottom-0 left-0 w-64 h-64 bg-accent/20 rounded-full blur-3xl -ml-16 -mb-16"></div>
    
    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
        <div>
            <h2 class="text-3xl font-bold mb-2">مرحباً، {{ auth()->user()->full_name ?? 'المستخدم' }} 👋</h2>
            <p class="text-blue-100 text-lg opacity-90">إليك ملخص سريع لما يحدث في المنصة اليوم.</p>
        </div>
        <div class="flex gap-3">
             <button class="px-5 py-2.5 bg-white text-primary font-bold rounded-xl shadow-lg hover:bg-gray-50 transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> إضافة جديد
            </button>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    
    <!-- Total Accounts -->
    <div class="bg-white p-6 rounded-2xl shadow-soft border border-gray-100 hover:shadow-card transition-all duration-300 group">
        <div class="flex items-start justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all">
                <i class="fa-solid fa-users text-xl"></i>
            </div>
            <span class="bg-green-50 text-green-600 text-xs font-bold px-2 py-1 rounded-lg border border-green-100 flex items-center gap-1">
                <i class="fa-solid fa-arrow-up"></i> 12%
            </span>
        </div>
        <div>
            <p class="text-gray-500 text-sm font-bold mb-1">إجمالي الحسابات</p>
            <h3 class="text-3xl font-extrabold text-gray-800">{{ number_format($stats['total_accounts'] ?? 0) }}</h3>
        </div>
    </div>

    <!-- Active Studios -->
    <div class="bg-white p-6 rounded-2xl shadow-soft border border-gray-100 hover:shadow-card transition-all duration-300 group">
        <div class="flex items-start justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-all">
                <i class="fa-solid fa-camera text-xl"></i>
            </div>
             <span class="bg-purple-50 text-purple-600 text-xs font-bold px-2 py-1 rounded-lg border border-purple-100">
                {{ $stats['studios_count'] ?? 0 }} نشط
            </span>
        </div>
        <div>
            <p class="text-gray-500 text-sm font-bold mb-1">الاستوديوهات</p>
            <h3 class="text-3xl font-extrabold text-gray-800">{{ number_format($stats['studios_count'] ?? 0) }}</h3>
        </div>
    </div>

    <!-- Active Schools -->
    <div class="bg-white p-6 rounded-2xl shadow-soft border border-gray-100 hover:shadow-card transition-all duration-300 group">
        <div class="flex items-start justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-all">
                <i class="fa-solid fa-school text-xl"></i>
            </div>
             <span class="bg-orange-50 text-orange-600 text-xs font-bold px-2 py-1 rounded-lg border border-orange-100">
                {{ $stats['schools_count'] ?? 0 }} نشط
            </span>
        </div>
        <div>
            <p class="text-gray-500 text-sm font-bold mb-1">المدارس</p>
            <h3 class="text-3xl font-extrabold text-gray-800">{{ number_format($stats['schools_count'] ?? 0) }}</h3>
        </div>
    </div>

    <!-- New Registrations -->
    <div class="bg-white p-6 rounded-2xl shadow-soft border border-gray-100 hover:shadow-card transition-all duration-300 group">
        <div class="flex items-start justify-between mb-4">
            <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600 group-hover:bg-teal-600 group-hover:text-white transition-all">
                <i class="fa-solid fa-user-plus text-xl"></i>
            </div>
            <span class="bg-teal-50 text-teal-600 text-xs font-bold px-2 py-1 rounded-lg border border-teal-100">
                اليوم
            </span>
        </div>
        <div>
            <p class="text-gray-500 text-sm font-bold mb-1">تسجيلات اليوم</p>
            <h3 class="text-3xl font-extrabold text-gray-800">{{ number_format($stats['new_accounts_today'] ?? 0) }}</h3>
        </div>
    </div>
</div>

<!-- Recent Activity & Quick Actions -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Recent Users Table -->
    <div class="lg:col-span-2 bg-white rounded-3xl shadow-soft border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-accent">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900 text-lg">أحدث المستخدمين</h3>
                    <p class="text-xs text-gray-500">آخر الحسابات المسجلة في النظام</p>
                </div>
            </div>
            <a href="{{ route('spa.accounts') }}" class="text-sm font-bold text-accent hover:text-accent-hover transition-colors">
                عرض الكل <i class="fa-solid fa-arrow-left mr-1"></i>
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">المستخدم</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">الحالة</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right">التاريخ</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($latestAccounts ?? [] as $account)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold border border-gray-200">
                                    {{ substr($account->full_name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $account->full_name }}</p>
                                    <p class="text-xs text-gray-500 font-mono">{{ $account->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'ACTIVE' => 'bg-green-50 text-green-600 border-green-100',
                                    'PENDING' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                                    'SUSPENDED' => 'bg-red-50 text-red-600 border-red-100',
                                ];
                                $statusClass = $statusColors[$account->status?->code ?? 'default'] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold border {{ $statusClass }}">
                                {{ $account->status?->name ?? 'غير محدد' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-500 font-medium">
                                {{ $account->created_at->format('Y/m/d') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-left">
                            <button class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-accent hover:bg-accent/10 transition-colors">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <i class="fa-regular fa-folder-open text-4xl mb-3 opacity-20"></i>
                                <p>لا توجد بيانات متاحة حالياً</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Quick Storage -->
    <div class="flex flex-col gap-6">
        <div class="bg-primary text-white p-6 rounded-3xl shadow-xl relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-32 h-32 bg-accent/20 rounded-full blur-2xl -mr-10 -mt-10 group-hover:bg-accent/30 transition-all duration-700"></div>
            
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-lg">مساحة التخزين</h3>
                    <i class="fa-solid fa-cloud text-accent text-2xl"></i>
                </div>
                
                <div class="mb-2 flex items-end gap-2">
                    <span class="text-4xl font-bold tracking-tight">75%</span>
                    <span class="text-sm text-gray-400 mb-1">مستخدمة</span>
                </div>
                
                <div class="w-full bg-white/10 rounded-full h-2 mb-6 overflow-hidden">
                    <div class="bg-accent h-full rounded-full w-3/4 shadow-[0_0_10px_rgba(59,130,246,0.5)]"></div>
                </div>
                
                <button class="w-full py-3 bg-white/10 hover:bg-white/20 rounded-xl text-sm font-bold transition-all border border-white/5 hover:border-white/20">
                    إدارة التخزين
                </button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-3xl shadow-soft border border-gray-100">
            <h3 class="font-bold text-gray-900 mb-4">روابط سريعة</h3>
            <div class="space-y-3">
                <a href="#" class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-file-circle-plus"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">إصدار تقرير جديد</p>
                        <p class="text-xs text-gray-400">إنشاء تقرير أداء مخصص</p>
                    </div>
                </a>
                
                <a href="#" class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                    <div class="w-10 h-10 rounded-xl bg-pink-50 flex items-center justify-center text-pink-600 group-hover:scale-110 transition-transform">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-800">إرسال تنبيه</p>
                        <p class="text-xs text-gray-400">إرسال إشعار للمستخدمين</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
