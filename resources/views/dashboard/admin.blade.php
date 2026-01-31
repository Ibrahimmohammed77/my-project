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
            @include('dashboard.partials.welcome-header', [
                'title' => 'لوحة تحكم المسؤول',
                'greeting' => 'مرحباً ' . Auth::user()->name,
                'subtitle' => 'نظرة عامة على إحصائيات النظام'
            ])

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @foreach($stats as $key => $value)
                    @php
                        $display = $dashboardService->getStatDisplay($key);
                        $formattedValue = $dashboardService->formatValue($key, $value);
                    @endphp

                    @include('dashboard.partials.stat-card', [
                        'title' => $display['label'],
                        'value' => $formattedValue,
                        'icon' => $display['icon'],
                        'color' => $display['color']
                    ])
                @endforeach
            </div>

            <!-- Quick Actions Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                @include('dashboard.partials.quick-actions', [
                    'title' => 'إدارة الحسابات والمستخدمين',
                    'actions' => [
                        ['route' => 'spa.accounts', 'label' => 'عرض وإدارة الحسابات', 'icon' => 'fas fa-users', 'color' => 'blue']
                    ]
                ])

                @include('dashboard.partials.quick-actions', [
                    'title' => 'إدارة الاستوديوهات والمدارس',
                    'actions' => [
                        ['route' => 'spa.studios', 'label' => 'عرض الاستوديوهات', 'icon' => 'fas fa-building', 'color' => 'purple'],
                        ['route' => 'spa.schools', 'label' => 'عرض المدارس', 'icon' => 'fas fa-school', 'color' => 'red'],
                        ['route' => 'spa.subscribers', 'label' => 'عرض المشتركين', 'icon' => 'fas fa-user-tag', 'color' => 'green']
                    ]
                ])

                @include('dashboard.partials.quick-actions', [
                    'title' => 'التقارير والإحصائيات',
                    'actions' => [
                        ['route' => null, 'label' => 'الإحصائيات العامة معروضة أعلاه. تقارير مفصلة قريباً.', 'icon' => 'fas fa-chart-bar', 'color' => 'gray']
                    ]
                ])
            </div>

            <!-- Recent Activity -->
            @include('dashboard.partials.recent-activity', [
                'title' => 'آخر النشاطات',
                'activities' => $recentActivities,
                'emptyMessage' => 'لا توجد نشاطات مسجلة.'
            ])
        </div>
    </div>
</div>
@endsection
