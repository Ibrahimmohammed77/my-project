@extends('layouts.app')

@section('title', 'لوحة تحكم المدرسة')

@section('content')
<div class="space-y-8 animate-in fade-in duration-700">
    <!-- Hero Stats Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Albums -->
        <div class="bg-white rounded-3xl p-6 shadow-card border border-gray-100 group hover:border-accent/30 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-images text-xl"></i>
                </div>
                <span class="text-xs font-bold text-green-500 bg-green-50 px-2 py-1 rounded-full">نشط</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">إجمالي الألبومات</h3>
            <p class="text-2xl font-black text-gray-800 mt-1">{{ $stats['total_albums'] ?? 0 }}</p>
        </div>

        <!-- Total Photos -->
        <div class="bg-white rounded-3xl p-6 shadow-card border border-gray-100 group hover:border-accent/30 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-camera text-xl"></i>
                </div>
                <span class="text-xs font-bold text-indigo-500 bg-indigo-50 px-2 py-1 rounded-full">نشط</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">إجمالي الصور</h3>
            <p class="text-2xl font-black text-gray-800 mt-1">{{ $stats['total_photos'] ?? 0 }}</p>
        </div>

        <!-- Active Cards -->
        <div class="bg-white rounded-3xl p-6 shadow-card border border-gray-100 group hover:border-accent/30 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-accent/10 flex items-center justify-center text-accent group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-id-card text-xl"></i>
                </div>
                <span class="text-xs font-bold text-accent bg-accent/5 px-2 py-1 rounded-full">{{ $stats['active_cards'] ?? 0 }} بطاقة</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">البطاقات النشطة</h3>
            <p class="text-2xl font-black text-gray-800 mt-1">{{ $stats['active_cards'] ?? 0 }}</p>
        </div>

        <!-- Students -->
        <div class="bg-white rounded-3xl p-6 shadow-card border border-gray-100 group hover:border-accent/30 transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-user-graduate text-xl"></i>
                </div>
                <span class="text-xs font-bold text-emerald-500 bg-emerald-50 px-2 py-1 rounded-full">طالب</span>
            </div>
            <h3 class="text-gray-500 text-sm font-medium">إجمالي الطلاب</h3>
            <p class="text-2xl font-black text-gray-800 mt-1">{{ $stats['total_students'] ?? 0 }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Quick Actions -->
        <div class="lg:col-span-2 bg-white rounded-3xl shadow-card border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50">
                <h3 class="text-lg font-bold text-gray-800">إجراءات سريعة</h3>
                <p class="text-sm text-gray-500 mt-1">الوصول السريع للوظائف الأساسية</p>
            </div>
            <div class="p-8">
                <div class="grid grid-cols-2 gap-4">
                    <a href="{{ route('school.albums.index') }}" class="p-6 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl border-2 border-transparent hover:border-blue-200 transition-all group">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mb-4 shadow-soft group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-images text-blue-600 text-xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-800">إدارة الألبومات</h4>
                        <p class="text-xs text-gray-600 mt-1">عرض وإدارة ألبومات المدرسة</p>
                    </a>

                    <a href="{{ route('school.cards.index') }}" class="p-6 bg-gradient-to-br from-accent/5 to-accent/10 rounded-2xl border-2 border-transparent hover:border-accent/30 transition-all group">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mb-4 shadow-soft group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-id-card text-accent text-xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-800">إدارة البطاقات</h4>
                        <p class="text-xs text-gray-600 mt-1">عرض وإدارة بطاقات الطلاب</p>
                    </a>

                    <a href="{{ route('school.students.index') }}" class="p-6 bg-gradient-to-br from-emerald-50 to-green-50 rounded-2xl border-2 border-transparent hover:border-emerald-200 transition-all group">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mb-4 shadow-soft group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-user-graduate text-emerald-600 text-xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-800">الطلاب</h4>
                        <p class="text-xs text-gray-600 mt-1">عرض كافة الطلاب المسجلين</p>
                    </a>

                    <a href="{{ route('school.profile.edit') }}" class="p-6 bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl border-2 border-transparent hover:border-purple-200 transition-all group">
                        <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center mb-4 shadow-soft group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-gear text-purple-600 text-xl"></i>
                        </div>
                        <h4 class="font-bold text-gray-800">إعدادات المدرسة</h4>
                        <p class="text-xs text-gray-600 mt-1">تعديل معلومات المدرسة</p>
                    </a>
                </div>
            </div>
        </div>

        <!-- School Info -->
        <div class="bg-primary rounded-3xl shadow-card p-8 text-white relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-white/5 to-transparent pointer-events-none"></div>
            <h3 class="text-xl font-bold mb-6">معلومات المدرسة</h3>
            
            <div class="space-y-4">
                @if($school)
                    <div class="bg-white/10 rounded-2xl p-4 border border-white/10">
                        <p class="text-xs text-gray-300 mb-1">اسم المدرسة</p>
                        <p class="text-sm font-bold">{{ $school->name }}</p>
                    </div>

                    <div class="bg-white/10 rounded-2xl p-4 border border-white/10">
                        <p class="text-xs text-gray-300 mb-1">رقم التواصل</p>
                        <p class="text-sm font-bold">{{ $school->phone ?? 'غير محدد' }}</p>
                    </div>

                    <div class="bg-white/10 rounded-2xl p-4 border border-white/10">
                        <p class="text-xs text-gray-300 mb-1">البريد الإلكتروني</p>
                        <p class="text-sm font-bold">{{ $school->email ?? 'غير محدد' }}</p>
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-sm text-gray-300">لا توجد معلومات محددة</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
