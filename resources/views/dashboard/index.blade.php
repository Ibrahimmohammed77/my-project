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
    
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                    <i class="fas fa-calendar-alt text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-gray-900">أحدث الفعاليات</h3>
                    <p class="text-xs text-gray-500">آخر المناسبات التي تم تغطيتها</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button class="px-4 py-2 text-sm font-bold text-gray-600 hover:text-primary bg-gray-50 hover:bg-gray-100 rounded-xl transition-colors border border-gray-200">
                    <i class="fas fa-filter mr-1"></i> تصفية
                </button>
                <button class="px-4 py-2 text-sm font-bold text-white bg-primary hover:bg-primary-light rounded-xl transition-colors shadow-lg shadow-primary/20">
                    <i class="fas fa-plus mr-1"></i> فعالية جديدة
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto flex-1">
            <table class="w-full text-right">
                <thead class="bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">الفعالية</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">المصور</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">التاريخ</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="hover:bg-gray-50/80 transition-all group cursor-pointer">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="h-14 w-14 rounded-2xl bg-gray-100 overflow-hidden relative shadow-sm border border-gray-100 group-hover:shadow-md transition-all">
                                    <img src="https://images.unsplash.com/photo-1523580494863-6f3031224c94?ixlib=rb-1.2.1&auto=format&fit=crop&w=150&q=80" class="object-cover w-full h-full transform group-hover:scale-110 transition-transform duration-700" alt="">
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 text-sm mb-1">حفل التخرج 2026</p>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded border border-blue-100 font-mono">#QR-9255</span>
                                        <span class="text-[10px] text-gray-400"><i class="fas fa-camera mr-1"></i>245 صورة</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gray-100 overflow-hidden border border-white shadow-sm">
                                    <img src="https://ui-avatars.com/api/?name=Ahmed+Mohamed&background=random" class="w-full h-full object-cover">
                                </div>
                                <span class="text-sm font-medium text-gray-700">أحمد محمد</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600 border border-green-100">
                                <span class="relative flex h-2 w-2">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                </span>
                                مباشر
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-700">اليوم</span>
                                <span class="text-xs text-gray-400">09:30 ص</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <button class="w-8 h-8 rounded-lg text-gray-400 hover:text-accent hover:bg-accent/10 flex items-center justify-center transition-all"><i class="fa-solid fa-chevron-left"></i></button>
                        </td>
                    </tr>

                    <tr class="hover:bg-gray-50/80 transition-all group cursor-pointer">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="h-14 w-14 rounded-2xl bg-gray-100 overflow-hidden relative shadow-sm border border-gray-100 group-hover:shadow-md transition-all">
                                    <img src="https://images.unsplash.com/photo-1544161515-4ab6ce6db874?ixlib=rb-1.2.1&auto=format&fit=crop&w=150&q=80" class="object-cover w-full h-full transform group-hover:scale-110 transition-transform duration-700" alt="">
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 text-sm mb-1">مؤتمر التقنية</p>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded border border-blue-100 font-mono">#QR-9254</span>
                                        <span class="text-[10px] text-gray-400"><i class="fas fa-camera mr-1"></i>1,023 صورة</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gray-100 overflow-hidden border border-white shadow-sm">
                                    <img src="https://ui-avatars.com/api/?name=Sara+Ali&background=random" class="w-full h-full object-cover">
                                </div>
                                <span class="text-sm font-medium text-gray-700">سارة علي</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-50 text-yellow-600 border border-yellow-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span>
                                معالجة
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-700">أمس</span>
                                <span class="text-xs text-gray-400">04:15 م</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <button class="w-8 h-8 rounded-lg text-gray-400 hover:text-accent hover:bg-accent/10 flex items-center justify-center transition-all"><i class="fa-solid fa-chevron-left"></i></button>
                        </td>
                    </tr>
                    
                    <tr class="hover:bg-gray-50/80 transition-all group cursor-pointer">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="h-14 w-14 rounded-2xl bg-gray-100 overflow-hidden relative shadow-sm border border-gray-100 group-hover:shadow-md transition-all">
                                    <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?ixlib=rb-1.2.1&auto=format&fit=crop&w=150&q=80" class="object-cover w-full h-full transform group-hover:scale-110 transition-transform duration-700" alt="">
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 text-sm mb-1">فعالية أوريكس</p>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded border border-blue-100 font-mono">#QR-9253</span>
                                        <span class="text-[10px] text-gray-400"><i class="fas fa-camera mr-1"></i>85 صورة</span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gray-100 overflow-hidden border border-white shadow-sm">
                                    <img src="https://ui-avatars.com/api/?name=Khalid+Yusuf&background=random" class="w-full h-full object-cover">
                                </div>
                                <span class="text-sm font-medium text-gray-700">خالد يوسف</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-600 border border-green-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                مكتمل
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-gray-700">24 يناير</span>
                                <span class="text-xs text-gray-400">02:00 م</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <button class="w-8 h-8 rounded-lg text-gray-400 hover:text-accent hover:bg-accent/10 flex items-center justify-center transition-all"><i class="fa-solid fa-chevron-left"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 bg-gray-50/50">
            <button class="w-full py-2.5 text-sm font-bold text-accent hover:text-white hover:bg-accent rounded-xl transition-all border border-transparent hover:border-accent/20 hover:shadow-lg flex items-center justify-center gap-2 group">
                <span>عرض جميع الفعاليات</span>
                <i class="fas fa-arrow-left group-hover:-translate-x-1 transition-transform"></i>
            </button>
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
