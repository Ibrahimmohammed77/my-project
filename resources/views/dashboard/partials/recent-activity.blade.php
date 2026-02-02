@props(['title', 'activities' => [], 'emptyMessage'])

<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-800">{{ $title }}</h2>
            @if($activities->count() > 0)
                <span class="text-xs text-gray-500">
                    آخر تحديث: {{ now()->format('H:i') }}
                </span>
            @endif
        </div>
    </div>

    <div class="p-6">
        <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2">
            @forelse($activities as $activity)
                <div class="flex items-start group">
                    <div class="flex-shrink-0 mr-3 mt-1">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center
                            @if(in_array($activity->action ?? '', ['login', 'user_logged_in']))
                                bg-green-50 text-green-600
                            @elseif(in_array($activity->action ?? '', ['create', 'store', 'user_created']))
                                bg-blue-50 text-blue-600
                            @elseif(in_array($activity->action ?? '', ['update', 'edit', 'user_updated']))
                                bg-yellow-50 text-yellow-600
                            @elseif(in_array($activity->action ?? '', ['delete', 'destroy', 'user_deleted']))
                                bg-red-50 text-red-600
                            @else
                                bg-gray-50 text-gray-600
                            @endif">
                            <i class="fas fa-{{ match(true) {
                                in_array($activity->action ?? '', ['login', 'user_logged_in']) => 'sign-in-alt',
                                in_array($activity->action ?? '', ['logout', 'user_logged_out']) => 'sign-out-alt',
                                in_array($activity->action ?? '', ['create', 'store', 'user_created', 'register']) => 'plus',
                                in_array($activity->action ?? '', ['update', 'edit', 'user_updated']) => 'pen',
                                in_array($activity->action ?? '', ['delete', 'destroy', 'user_deleted']) => 'trash',
                                default => 'circle'
                            } }} text-sm"></i>
                        </div>
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-800">
                            {{ $activity->description ?? 'نشاط غير معروف' }}
                        </p>
                        <div class="mt-1 flex items-center text-xs text-gray-500">
                            <i class="far fa-clock ml-1"></i>
                            <span>{{ $activity->created_at->diffForHumans() }}</span>

                            @if($activity->causer)
                                <span class="mx-2">•</span>
                                <i class="far fa-user ml-1"></i>
                                <span>{{ $activity->causer->name ?? 'مستخدم' }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <i class="far fa-calendar text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">{{ $emptyMessage }}</p>
                </div>
            @endforelse
        </div>

        @if($activities->count() > 0)
            <div class="mt-6 pt-4 border-t border-gray-200">
                {{-- <a href="{{ route('activity-logs.index') }}"
                   class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                    عرض جميع النشاطات
                    <i class="fas fa-arrow-left mr-1"></i>
                </a> --}}
            </div>
        @endif
    </div>
</div>
