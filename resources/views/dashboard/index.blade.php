@extends('layouts.app')

@section('title', 'لوحة القيادة')

@section('page-title', 'نظرة عامة')

@section('content')
<div class="bg-gradient-to-l from-primary to-primary-light rounded-3xl p-6 sm:p-10 mb-8 text-white relative overflow-hidden shadow-xl">
    <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
        <i class="fa-solid fa-camera-retro absolute -bottom-10 -left-10 text-9xl transform rotate-12"></i>
    </div>
    <div class="relative z-10 max-w-2xl">
        <h2 class="text-2xl sm:text-3xl font-bold mb-3">مرحباً بعودتك، {{ auth()->user()->name ?? 'إبراهيم' }}! 👋</h2>
        <p class="text-blue-100 text-sm sm:text-base leading-relaxed mb-6">لديك 12 صورة جديدة في انتظار المراجعة، ونسبة التفاعل زادت بنسبة 25% مقارنة بالأمس.</p>
        <div class="flex gap-3">
            <button class="bg-white text-primary px-5 py-2.5 rounded-lg font-bold text-sm hover:bg-gray-50 transition-colors shadow-md">عرض التقارير</button>
            <button class="bg-white/20 backdrop-blur-sm text-white px-5 py-2.5 rounded-lg font-bold text-sm hover:bg-white/30 transition-colors">مراجعة الصور</button>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <div class="bg-surface p-6 rounded-2xl shadow-soft border border-gray-100 hover:-translate-y-1 transition-transform duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center text-accent">
                <i class="fa-solid fa-image text-xl"></i>
            </div>
            <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-full flex items-center gap-1">
                <i class="fa-solid fa-arrow-trend-up"></i> +12%
            </span>
        </div>
        <h3 class="text-gray-500 text-sm font-medium mb-1">إجمالي الصور</h3>
        <p class="text-3xl font-bold text-primary">24.5k</p>
    </div>
    
    <div class="bg-surface p-6 rounded-2xl shadow-soft border border-gray-100 hover:-translate-y-1 transition-transform duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600">
                <i class="fa-solid fa-qrcode text-xl"></i>
            </div>
            <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-full flex items-center gap-1">
                <i class="fa-solid fa-arrow-trend-up"></i> +5%
            </span>
        </div>
        <h3 class="text-gray-500 text-sm font-medium mb-1">مسح QR</h3>
        <p class="text-3xl font-bold text-primary">8,230</p>
    </div>

    <div class="bg-surface p-6 rounded-2xl shadow-soft border border-gray-100 hover:-translate-y-1 transition-transform duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600">
                <i class="fa-solid fa-users text-xl"></i>
            </div>
            <span class="text-xs font-bold text-red-500 bg-red-50 px-2 py-1 rounded-full flex items-center gap-1">
                <i class="fa-solid fa-arrow-trend-down"></i> -2%
            </span>
        </div>
        <h3 class="text-gray-500 text-sm font-medium mb-1">المستخدمين النشطين</h3>
        <p class="text-3xl font-bold text-primary">1,402</p>
    </div>

    <div class="bg-surface p-6 rounded-2xl shadow-soft border border-gray-100 hover:-translate-y-1 transition-transform duration-300">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 rounded-xl bg-teal-100 flex items-center justify-center text-teal-600">
                <i class="fa-solid fa-server text-xl"></i>
            </div>
            <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded-full">مستقر</span>
        </div>
        <h3 class="text-gray-500 text-sm font-medium mb-1">حالة السيرفر</h3>
        <p class="text-3xl font-bold text-primary">99.9%</p>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    
    <div class="xl:col-span-2 bg-surface rounded-2xl shadow-soft border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h3 class="font-bold text-lg text-primary">العمليات الأخيرة</h3>
            <div class="flex gap-2">
                <button class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-primary bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">تصفية</button>
                <button class="px-4 py-2 text-sm font-medium text-white bg-primary hover:bg-primary-light rounded-lg transition-colors">تصدير CSV</button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-gray-50 text-gray-500 text-xs font-bold uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">الحدث</th>
                        <th class="px-6 py-4">المالك</th>
                        <th class="px-6 py-4">الحالة</th>
                        <th class="px-6 py-4">التاريخ</th>
                        <th class="px-6 py-4">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50/50 transition-colors group cursor-pointer">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-gray-200 overflow-hidden relative">
                                    <img src="https://images.unsplash.com/photo-1523580494863-6f3031224c94?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80" class="object-cover w-full h-full" alt="">
                                </div>
                                <div>
                                    <p class="font-bold text-primary text-sm">حفل التخرج 2026</p>
                                    <p class="text-xs text-gray-400">#QR-9255</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-600">أحمد محمد</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> نشط
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">منذ 2 دقيقة</td>
                        <td class="px-6 py-4">
                            <button class="text-gray-400 hover:text-accent transition-colors"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                        </td>
                    </tr>

                    <tr class="hover:bg-gray-50/50 transition-colors group cursor-pointer">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-gray-200 overflow-hidden relative">
                                    <img src="https://images.unsplash.com/photo-1544161515-4ab6ce6db874?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80" class="object-cover w-full h-full" alt="">
                                </div>
                                <div>
                                    <p class="font-bold text-primary text-sm">مؤتمر التقنية</p>
                                    <p class="text-xs text-gray-400">#QR-9254</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-600">سارة علي</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> معالجة
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">منذ 15 دقيقة</td>
                        <td class="px-6 py-4">
                            <button class="text-gray-400 hover:text-accent transition-colors"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                        </td>
                    </tr>
                    
                    <tr class="hover:bg-gray-50/50 transition-colors group cursor-pointer">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-gray-200 overflow-hidden relative">
                                    <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?ixlib=rb-1.2.1&auto=format&fit=crop&w=100&q=80" class="object-cover w-full h-full" alt="">
                                </div>
                                <div>
                                    <p class="font-bold text-primary text-sm">فعالية أوريكس</p>
                                    <p class="text-xs text-gray-400">#QR-9253</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-600">خالد يوسف</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> موقوف
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">منذ 1 ساعة</td>
                        <td class="px-6 py-4">
                            <button class="text-gray-400 hover:text-accent transition-colors"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 text-center">
            <button class="text-sm font-bold text-accent hover:text-accent-hover transition-colors">عرض جميع العمليات</button>
        </div>
    </div>

    <div class="flex flex-col gap-6">
        <div class="bg-primary text-white rounded-2xl p-6 shadow-xl relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="font-bold text-lg mb-1">مساحة التخزين</h3>
                <p class="text-gray-400 text-sm mb-6">خطة الأعمال (2TB)</p>
                
                <div class="flex items-end gap-2 mb-2">
                    <span class="text-3xl font-bold">1.4</span>
                    <span class="text-sm text-gray-400 mb-1">TB مستخدمة</span>
                </div>
                
                <div class="w-full bg-white/10 rounded-full h-2 mb-4">
                    <div class="bg-accent h-2 rounded-full" style="width: 70%"></div>
                </div>
                
                <button class="w-full py-3 bg-white/10 hover:bg-white/20 rounded-xl text-sm font-bold transition-colors">ترقية الخطة</button>
            </div>
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-accent/20 rounded-full blur-2xl"></div>
        </div>

        <div class="bg-surface rounded-2xl shadow-soft border border-gray-100 p-6">
            <h3 class="font-bold text-primary mb-4">رفع سريع</h3>
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 flex flex-col items-center justify-center text-center hover:border-accent hover:bg-accent/5 transition-all cursor-pointer group">
                <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-white group-hover:shadow-md transition-all">
                    <i class="fa-solid fa-cloud-arrow-up text-gray-400 group-hover:text-accent text-xl transition-colors"></i>
                </div>
                <p class="text-sm font-bold text-gray-600 group-hover:text-primary">اضغط للرفع</p>
                <p class="text-xs text-gray-400 mt-1">أو اسحب الملفات هنا</p>
            </div>
        </div>
    </div>

</div>
@endsection
