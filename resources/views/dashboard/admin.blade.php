@extends('layouts.app')

@section('title', 'لوحة تحكم المسؤول')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Navigation -->
    @include('dashboard.partials.admin-nav')

    <!-- Main Content -->
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Welcome Message -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">لوحة تحكم المسؤول</h1>
                <p class="mt-2 text-gray-600">مرحباً {{ Auth::user()->name }}</p>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @foreach($stats as $key => $value)
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500">{{ $statLabels[$key] ?? $key }}</p>
                            <p class="text-2xl font-bold mt-2">
                                @if($key === 'total_revenue')
                                    {{ number_format($value, 0) }}
                                @else
                                    {{ $value }}
                                @endif
                            </p>
                        </div>
                        <div class="p-3 {{ $controller->getStatColor($key) }} rounded-lg">
                            <i class="{{ $controller->getStatIcon($key) }} text-white text-xl"></i>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">إدارة الحسابات والمستخدمين</h2>
                    <div class="space-y-3">
                        @if(Route::has('spa.accounts'))
                        <a href="{{ route('spa.accounts') }}" class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition">
                            <i class="fas fa-users text-blue-600 ml-3"></i>
                            <span>عرض وإدارة الحسابات</span>
                        </a>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">إدارة الاستوديوهات والمدارس</h2>
                    <div class="space-y-3">
                        @if(Route::has('spa.studios'))
                        <a href="{{ route('spa.studios') }}" class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition">
                            <i class="fas fa-building text-purple-600 ml-3"></i>
                            <span>عرض الاستوديوهات</span>
                        </a>
                        @endif
                        @if(Route::has('spa.schools'))
                        <a href="{{ route('spa.schools') }}" class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition">
                            <i class="fas fa-school text-red-600 ml-3"></i>
                            <span>عرض المدارس</span>
                        </a>
                        @endif
                        @if(Route::has('spa.subscribers'))
                        <a href="{{ route('spa.subscribers') }}" class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition">
                            <i class="fas fa-user-tag text-green-600 ml-3"></i>
                            <span>عرض المشتركين</span>
                        </a>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">التقارير والإحصائيات</h2>
                    <div class="space-y-3">
                        <p class="text-sm text-gray-500 p-3">الإحصائيات العامة معروضة أعلاه. تقارير مفصلة قريباً.</p>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-xl shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800">آخر النشاطات</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse(Auth::user()->activityLogs()->latest()->take(10)->get() as $activity)
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-circle text-xs {{ in_array($activity->action ?? '', ['login', 'user_logged_in'], true) ? 'text-green-500' : 'text-blue-500' }} mt-1"></i>
                            </div>
                            <div class="mr-3">
                                <p class="text-sm text-gray-800">{{ $activity->description }}</p>
                                <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-gray-500">لا توجد نشاطات مسجلة.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // إضافة أي سكريبتات خاصة بـ Dashboard المسؤول
    console.log('Admin Dashboard Loaded');
</script>
@endpush
