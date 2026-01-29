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
                            <p class="text-sm text-gray-500">{{ trans("dashboard.stats.$key") }}</p>
                            <p class="text-2xl font-bold mt-2">{{ $value }}</p>
                        </div>
                        <div class="p-3 {{ $this->getStatColor($key) }} rounded-lg">
                            <i class="{{ $this->getStatIcon($key) }} text-white text-xl"></i>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">إدارة المستخدمين</h2>
                    <div class="space-y-3">
                        <a href="{{ route('admin.users.index') }}" class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition">
                            <i class="fas fa-users text-blue-600 ml-3"></i>
                            <span>عرض جميع المستخدمين</span>
                        </a>
                        <a href="{{ route('admin.users.create') }}" class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition">
                            <i class="fas fa-user-plus text-green-600 ml-3"></i>
                            <span>إضافة مستخدم جديد</span>
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">إدارة الاستوديوهات</h2>
                    <div class="space-y-3">
                        <a href="{{ route('admin.studios.index') }}" class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition">
                            <i class="fas fa-building text-purple-600 ml-3"></i>
                            <span>عرض الاستوديوهات</span>
                        </a>
                        <a href="{{ route('admin.studios.create') }}" class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition">
                            <i class="fas fa-plus-circle text-purple-600 ml-3"></i>
                            <span>إضافة استوديو جديد</span>
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">التقارير والإحصائيات</h2>
                    <div class="space-y-3">
                        <a href="{{ route('admin.reports.users') }}" class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition">
                            <i class="fas fa-chart-bar text-yellow-600 ml-3"></i>
                            <span>تقارير المستخدمين</span>
                        </a>
                        <a href="{{ route('admin.reports.financial') }}" class="flex items-center p-3 hover:bg-gray-50 rounded-lg transition">
                            <i class="fas fa-money-bill-wave text-green-600 ml-3"></i>
                            <span>تقارير مالية</span>
                        </a>
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
                        @foreach(Auth::user()->activityLogs()->latest()->take(10)->get() as $activity)
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-circle text-xs {{ $activity->action == 'user_logged_in' ? 'text-green-500' : 'text-blue-500' }} mt-1"></i>
                            </div>
                            <div class="mr-3">
                                <p class="text-sm text-gray-800">{{ $activity->getDescriptionAttribute() }}</p>
                                <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        @endforeach
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
