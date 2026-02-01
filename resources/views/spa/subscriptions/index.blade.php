{{-- resources/views/spa/subscriptions/index.blade.php --}}
@extends('layouts.app')
@section('title', 'إدارة الاشتراكات')
@section('header', 'إدارة الاشتراكات')

@section('content')
    <x-page-header title="إدارة الاشتراكات">
        <!-- ACTION BAR -->
        <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
            
            <!-- 1. User Filter (Custom Dropdown) -->
            <div class="relative w-full md:w-64 group" id="user-filter-container">
                <button type="button" 
                        onclick="toggleUserDropdown('filter')"
                        class="w-full flex items-center justify-between px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl hover:border-accent/50 focus:border-accent transition-all text-sm text-gray-700">
                    <div class="flex items-center gap-2 truncate">
                        <i class="fas fa-user text-gray-400"></i>
                        <span id="user-filter-label" class="truncate">المستخدم: الكل</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200" id="user-filter-arrow"></i>
                </button>

                <!-- Dropdown Body -->
                <div id="user-filter-dropdown" class="hidden absolute top-full right-0 mt-2 w-72 bg-white border border-gray-100 rounded-xl shadow-xl z-[70] overflow-hidden">
                    <!-- Stage 1: Role Selection -->
                    <div id="user-filter-roles" class="p-2">
                        <div class="text-xs font-bold text-gray-400 px-2 py-1 mb-1 uppercase tracking-wider">اختر الدور</div>
                        <button onclick="selectUserRole('filter', null)" class="w-full text-right px-3 py-2 rounded-lg hover:bg-gray-50 text-sm font-medium text-gray-700 transition-colors">
                            <i class="fas fa-users w-5 text-gray-400"></i> الكل
                        </button>
                        <button onclick="selectUserRole('filter', 'customer')" class="w-full text-right px-3 py-2 rounded-lg hover:bg-blue-50 text-sm font-medium text-gray-700 hover:text-blue-700 transition-colors">
                            <i class="fas fa-user-tag w-5 text-blue-400"></i> مشترك (Customer)
                        </button>
                        <button onclick="selectUserRole('filter', 'school_owner')" class="w-full text-right px-3 py-2 rounded-lg hover:bg-orange-50 text-sm font-medium text-gray-700 hover:text-orange-700 transition-colors">
                            <i class="fas fa-school w-5 text-orange-400"></i> مدرسة
                        </button>
                        <button onclick="selectUserRole('filter', 'studio_owner')" class="w-full text-right px-3 py-2 rounded-lg hover:bg-purple-50 text-sm font-medium text-gray-700 hover:text-purple-700 transition-colors">
                            <i class="fas fa-camera w-5 text-purple-400"></i> استوديو
                        </button>
                    </div>

                    <!-- Stage 2: User Search (Hidden by default) -->
                    <div id="user-filter-search-area" class="hidden flex flex-col h-[320px]">
                        <div class="p-2 border-b border-gray-50 flex items-center gap-2">
                            <button onclick="backToRoles('filter')" class="p-2 hover:bg-gray-100 rounded-lg text-gray-500 transition-colors" title="عودة">
                                <i class="fas fa-arrow-right text-sm"></i>
                            </button>
                            <div class="relative flex-1">
                                <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                <input type="text" id="user-filter-input" 
                                       class="w-full pl-4 pr-9 py-1.5 bg-gray-50 border border-transparent focus:bg-white focus:border-accent/20 rounded-lg text-sm outline-none transition-all"
                                       placeholder="بحث...">
                                <i id="user-filter-spinner" class="fas fa-circle-notch fa-spin absolute left-3 top-1/2 -translate-y-1/2 text-accent text-xs hidden"></i>
                            </div>
                        </div>
                        <div id="user-filter-results" class="flex-1 overflow-y-auto p-2 space-y-1 bg-white">
                            <!-- Results Injected Here -->
                        </div>
                    </div>
                </div>
            </div>

             <!-- 2. Plan Filter (Similar Logic) -->
             <div class="relative w-full md:w-56 group" id="plan-filter-container">
                <button type="button" 
                        onclick="togglePlanDropdown('filter')"
                        class="w-full flex items-center justify-between px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl hover:border-accent/50 focus:border-accent transition-all text-sm text-gray-700">
                    <div class="flex items-center gap-2 truncate">
                        <i class="fas fa-box-open text-gray-400"></i>
                        <span id="plan-filter-label" class="truncate">الخطة: الكل</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200" id="plan-filter-arrow"></i>
                </button>

                <div id="plan-filter-dropdown" class="hidden absolute top-full right-0 mt-2 w-64 bg-white border border-gray-100 rounded-xl shadow-xl z-50 p-2">
                    <div class="relative mb-2">
                        <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" id="plan-filter-input" 
                               class="w-full pl-4 pr-9 py-2 bg-gray-50 border border-transparent focus:bg-white focus:border-accent/20 rounded-lg text-sm outline-none transition-all"
                               placeholder="بحث عن خطة...">
                    </div>
                    <div id="plan-filter-results" class="max-h-60 overflow-y-auto space-y-1">
                        <!-- Plans Injected Here -->
                        <button onclick="selectPlan('filter', null, 'الكل')" class="w-full text-right px-3 py-2 rounded-lg hover:bg-gray-50 text-sm font-medium text-gray-700 transition-colors">
                            الكل
                        </button>
                    </div>
                </div>
            </div>

            <!-- 3. Status Filter -->
            <div class="relative w-full md:w-48">
                <i class="fas fa-filter absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <select id="status-filter"
                        class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm appearance-none cursor-pointer text-gray-600 font-medium">
                    <option value="">كل الحالات</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status->lookup_value_id }}">{{ $status->name }}</option>
                    @endforeach
                </select>
                <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
            </div>
        </div>

        <!-- Create Button -->
        <x-button onclick="showCreateModal()" variant="primary" class="shadow-lg shadow-accent/20 hover:shadow-accent/30 hover:-translate-y-0.5 transition-all">
            <i class="fas fa-plus text-xs"></i>
            <span>منح اشتراك جديد</span>
        </x-button>
    </x-page-header>

    <!-- Subscriptions Table -->
    <x-table :headers="[
        ['name' => 'المستخدم', 'class' => 'w-1/4'],
        ['name' => 'الخطة', 'class' => 'w-1/4'],
        ['name' => 'الحالة', 'class' => 'w-1/6'],
        ['name' => 'تاريخ الانتهاء', 'class' => 'w-1/6'],
        ['name' => 'التجديد التلقائي', 'class' => 'w-1/6'],
        ['name' => 'إجراءات', 'class' => 'text-center']
    ]" id="subscriptions">
        <tbody id="subscriptions-tbody">
            <!-- Loading State -->
            <tr id="loading-state" class="hidden">
                <td colspan="6" class="px-6 py-20 text-center">
                    <div class="flex flex-col items-center gap-3 animate-pulse">
                        <div class="w-10 h-10 border-4 border-gray-200 border-t-accent rounded-full animate-spin"></div>
                        <span class="text-sm font-medium text-gray-500">جاري تحميل الاشتراكات...</span>
                    </div>
                </td>
            </tr>

            <!-- Empty State -->
            <tr id="empty-state" class="hidden">
                <td colspan="6" class="px-6 py-24 text-center">
                    <div class="flex flex-col items-center max-w-sm mx-auto">
                        <div class="bg-gray-50 w-24 h-24 rounded-full flex items-center justify-center mb-6 border-2 border-dashed border-gray-200">
                            <i class="fas fa-ticket-alt text-4xl text-gray-300 transform -rotate-12"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">لا يوجد اشتراكات</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">لم يتم العثور على اشتراكات تطابق البحث الحالي. حاول تغيير الفلاتر أو إنشاء اشتراك جديد.</p>
                    </div>
                </td>
            </tr>
        </tbody>
    </x-table>

    <!-- Pagination -->
    <div id="pagination-container" class="mt-6"></div>

    <!-- Create/Edit Modal -->
    <x-modal id="subscription-modal" title="منح اشتراك جديد" maxWidth="lg">
        <form id="subscription-form" class="space-y-6">
            <input type="hidden" id="subscription-id">
            <input type="hidden" id="user_id" name="user_id"> <!-- Linked to Custom Dropdown -->

            <!-- A. User Information -->
            <div class="space-y-4">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500">
                        <i class="fas fa-user text-sm"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-800">معلومات المستخدم</h3>
                </div>

                <!-- Custom User Picker (Modal) -->
                <div class="relative group" id="modal-user-picker">
                    <label class="block text-sm font-medium text-gray-700 mb-2">اختر المستخدم <span class="text-red-500">*</span></label>
                    
                    <!-- Selected User Display -->
                    <div id="modal-selected-user-display" class="hidden p-3 bg-blue-50/50 border border-blue-100 rounded-xl flex items-center justify-between group-hover:border-blue-200 transition-colors cursor-pointer" onclick="toggleUserDropdown('modal')">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-blue-50" id="modal-selected-user-avatar">
                                <i class="fas fa-user text-blue-300"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900" id="modal-selected-user-name">اسم المستخدم</h4>
                                <p class="text-xs text-gray-500" id="modal-selected-user-email">email@example.com</p>
                            </div>
                        </div>
                        <i class="fas fa-exchange-alt text-blue-400 text-sm opacity-0 group-hover:opacity-100 transition-opacity p-2"></i>
                    </div>

                    <!-- Picker Trigger (when no user selected) -->
                    <button type="button" 
                            id="modal-user-trigger"
                            onclick="toggleUserDropdown('modal')"
                            class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl hover:bg-white hover:border-accent/50 focus:border-accent hover:shadow-md transition-all text-sm text-gray-500">
                        <span><i class="fas fa-search text-gray-400 ml-2"></i>ابحث عن مستخدم...</span>
                        <i class="fas fa-chevron-down text-gray-300"></i>
                    </button>

                    <!-- Dropdown Body (Shared Logic) -->
                    <div id="modal-user-dropdown" class="hidden absolute top-full left-0 right-0 mt-2 bg-white border border-gray-100 rounded-xl shadow-2xl z-50 overflow-hidden min-h-[300px]">
                        <!-- Roles View -->
                        <div id="modal-user-roles" class="p-3 grid grid-cols-1 gap-2">
                            <div class="text-xs font-bold text-gray-400 px-1 mb-2">اختر فئة المستخدم</div>
                            <button onclick="selectUserRole('modal', 'customer')" type="button" class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-blue-200 hover:bg-blue-50 transition-all text-right group/btn">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 group-hover/btn:scale-110 transition-transform">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800 text-sm">مشترك (Customer)</div>
                                    <div class="text-xs text-gray-500">الأفراد والمشترين المباشرين</div>
                                </div>
                                <i class="fas fa-chevron-left mr-auto text-gray-300"></i>
                            </button>
                            <button onclick="selectUserRole('modal', 'school_owner')" type="button" class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-orange-200 hover:bg-orange-50 transition-all text-right group/btn">
                                <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 group-hover/btn:scale-110 transition-transform">
                                    <i class="fas fa-school"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800 text-sm">مدرسة</div>
                                    <div class="text-xs text-gray-500">مدراء المدارس والمؤسسات التعليمية</div>
                                </div>
                                <i class="fas fa-chevron-left mr-auto text-gray-300"></i>
                            </button>
                            <button onclick="selectUserRole('modal', 'studio_owner')" type="button" class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:border-purple-200 hover:bg-purple-50 transition-all text-right group/btn">
                                <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 group-hover/btn:scale-110 transition-transform">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800 text-sm">استوديو</div>
                                    <div class="text-xs text-gray-500">أصحاب الاستوديوهات والمصورين</div>
                                </div>
                                <i class="fas fa-chevron-left mr-auto text-gray-300"></i>
                            </button>
                        </div>

                        <!-- Search View -->
                        <div id="modal-user-search-area" class="hidden flex flex-col h-full absolute inset-0 bg-white">
                            <div class="p-3 border-b border-gray-50 flex items-center gap-2 bg-gray-50/50">
                                <button type="button" onclick="backToRoles('modal')" class="w-8 h-8 flex items-center justify-center hover:bg-white rounded-lg text-gray-500 shadow-sm border border-transparent hover:border-gray-200 transition-all" title="رجوع">
                                    <i class="fas fa-arrow-right text-sm"></i>
                                </button>
                                <div class="relative flex-1">
                                    <input type="text" id="modal-user-input" 
                                           class="w-full pl-4 pr-10 py-2 bg-white border border-gray-200 focus:border-accent rounded-lg text-sm outline-none transition-all shadow-sm"
                                           placeholder="بحث بالاسم أو البريد...">
                                    <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                    <div id="modal-user-spinner" class="absolute left-3 top-1/2 -translate-y-1/2 hidden">
                                        <i class="fas fa-circle-notch fa-spin text-accent text-xs"></i>
                                    </div>
                                </div>
                            </div>
                            <!-- Results List -->
                            <div id="modal-user-results" class="flex-1 overflow-y-auto p-2 space-y-1">
                                <!-- JS Injected Content -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="h-px bg-gray-100"></div>

            <!-- B. Subscription Details -->
            <div class="space-y-4">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center text-green-500">
                        <i class="fas fa-file-invoice text-sm"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-800">تفاصيل الاشتراك</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Plan Picker (Custom) -->
                    <div class="relative group" id="modal-plan-picker">
                        <label class="block text-sm font-medium text-gray-700 mb-2">الخطة <span class="text-red-500">*</span></label>
                        <input type="hidden" name="plan_id" id="plan_id" required>
                        
                        <button type="button" 
                                onclick="togglePlanDropdown('modal')"
                                class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl hover:bg-white hover:border-accent/50 focus:border-accent transition-all text-sm text-gray-700">
                            <div class="flex items-center gap-2 truncate">
                                <i class="fas fa-box-open text-gray-400"></i>
                                <span id="modal-plan-label" class="truncate">اختر الخطة...</span>
                            </div>
                            <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-200" id="modal-plan-arrow"></i>
                        </button>

                        <div id="modal-plan-dropdown" class="hidden absolute top-full left-0 right-0 mt-2 bg-white border border-gray-100 rounded-xl shadow-xl z-50 p-2 max-h-60 flex flex-col">
                             <div class="relative mb-2 shrink-0">
                                <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                <input type="text" id="modal-plan-input" 
                                       class="w-full pl-4 pr-9 py-2 bg-gray-50 border border-transparent focus:bg-white focus:border-accent/20 rounded-lg text-sm outline-none transition-all"
                                       placeholder="بحث عن خطة...">
                                <i id="modal-plan-spinner" class="fas fa-circle-notch fa-spin absolute left-3 top-1/2 -translate-y-1/2 text-accent text-xs hidden"></i>
                            </div>
                            <div id="modal-plan-results" class="overflow-y-auto flex-1 space-y-1">
                                <!-- Plans Injected Here -->
                            </div>
                        </div>
                    </div>

                    <!-- Billing Cycle -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">دورة الفوترة <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-sync absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            <select id="billing_cycle" name="billing_cycle" required
                                    class="w-full pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm appearance-none cursor-pointer">
                                <option value="monthly">شهري (Monthly)</option>
                                <option value="yearly">سنوي (Yearly)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Status (Hidden by default in create) -->
                    <div id="status-field-container" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                        <div class="relative">
                            <select id="status_id" name="status_id"
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent outline-none text-sm">
                                @foreach($statuses as $status)
                                    <option value="{{ $status->lookup_value_id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Price & Renew -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="relative flex items-center">
                            <input type="checkbox" id="auto_renew" name="auto_renew" checked
                                   class="peer h-5 w-5 cursor-pointer appearance-none rounded-md border border-gray-300 transition-all checked:border-accent checked:bg-accent hover:border-accent-hover">
                            <i class="fas fa-check absolute left-1 top-1 text-white text-[10px] opacity-0 peer-checked:opacity-100 pointer-events-none"></i>
                        </div>
                        <label for="auto_renew" class="text-sm font-bold text-gray-700 cursor-pointer select-none">
                            تجديد تلقائي
                            <span class="block text-[10px] text-gray-400 font-normal">يجدد عند انتهاء المدة</span>
                        </label>
                    </div>

                    <div id="price-display" class="text-left hidden">
                        <span class="block text-xs text-gray-400 mb-0.5">الإجمالي</span>
                        <div class="flex items-baseline gap-1">
                            <span class="text-xl font-black text-gray-900" id="selected-price">0.00</span>
                            <span class="text-xs font-bold text-gray-500" id="price-period">/شهر</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- C. Duration Info -->
            <div class="flex gap-4 p-4 bg-blue-50/50 rounded-xl border border-blue-100 text-sm">
                <div class="flex-1 text-center border-l border-blue-100">
                    <div class="text-xs text-blue-500 font-medium mb-1">تاريخ البدء</div>
                    <div class="font-bold text-gray-900" id="start-date">اليوم</div>
                </div>
                <div class="flex-1 text-center">
                    <div class="text-xs text-blue-500 font-medium mb-1">تاريخ الانتهاء</div>
                    <div class="font-bold text-gray-900" id="end-date">-</div>
                </div>
            </div>
        </form>

        <!-- D. Footer -->
        <x-slot name="footer">
            <div class="flex items-center gap-3 w-full">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2.5 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-50 font-bold text-sm transition-colors">
                    إلغاء
                </button>
                <button type="submit" form="subscription-form" class="flex-[2] px-4 py-2.5 rounded-xl bg-accent text-white hover:bg-accent-hover font-bold text-sm shadow-lg shadow-accent/20 transition-all transform active:scale-95 flex items-center justify-center gap-2">
                    <i class="fas fa-check"></i>
                    <span>حفظ الاشتراك</span>
                </button>
            </div>
        </x-slot>
    </x-modal>

@push('scripts')
    @vite('resources/js/spa/modules/subscriptions/index.js')
    
    <!-- Inline Scripts for UI Toggles (Alpine-free pure JS as requested/implied style) -->
    <script>
        // Dropdown Toggles
        function toggleUserDropdown(context) {
            const dropdown = document.getElementById(`${context}-user-dropdown`);
            const arrow = document.getElementById(`${context}-filter-arrow`); // For filter context
            if(dropdown) {
                dropdown.classList.toggle('hidden');
                // Reset to roles view when opening
                if(!dropdown.classList.contains('hidden')) {
                    backToRoles(context);
                }
            }
            if(arrow) arrow.classList.toggle('rotate-180');
        }

        function togglePlanDropdown(context) {
            const dropdown = document.getElementById(`${context}-plan-dropdown`);
            const arrow = document.getElementById(`${context}-plan-arrow`);
            if(dropdown) dropdown.classList.toggle('hidden');
            if(arrow) arrow.classList.toggle('rotate-180');
        }

        // View Switching in User Dropdown
        function selectUserRole(context, role) {
            const isFilter = context === 'filter';
            const rolesId = isFilter ? 'user-filter-roles' : 'modal-user-roles';
            const searchId = isFilter ? 'user-filter-search-area' : 'modal-user-search-area';
            const inputId = isFilter ? 'user-filter-input' : 'modal-user-input';
            
            if(isFilter) {
                // Visual update
                const label = document.getElementById('user-filter-label');
                const roleNames = {
                    'customer': 'مشترك',
                    'school_owner': 'مدرسة',
                    'studio_owner': 'استوديو'
                };
                if(label) label.textContent = role ? `المستخدم: ${roleNames[role]}` : 'المستخدم: الكل';
            }

            // Show search area
            const rolesView = document.getElementById(rolesId);
            const searchView = document.getElementById(searchId);
            
            if(rolesView && searchView) {
                rolesView.classList.add('hidden');
                searchView.classList.remove('hidden');
                searchView.classList.add('flex');
                // Force inline style to override any hidden class issues
                searchView.style.display = 'flex';
                
                // Focus input
                const input = document.getElementById(inputId);
                if(input) setTimeout(() => input.focus(), 100);

                // Show instant loading state
                const resultsContainer = document.getElementById(isFilter ? 'user-filter-results' : 'modal-user-results');
                if(resultsContainer) {
                     resultsContainer.style.display = 'block';
                     resultsContainer.innerHTML = `
                        <div class="flex flex-col items-center justify-center py-8 text-gray-400">
                            <i class="fas fa-circle-notch fa-spin text-2xl mb-2 text-accent"></i>
                            <span class="text-xs">جاري جلب المستخدمين...</span>
                        </div>
                     `;
                }
            }

            // Trigger Controller
            if(window.subscriptionController) {
                    if(isFilter) {
                        // Safe set for filter role
                        if(typeof window.subscriptionController.setFilterRole === 'function') {
                            window.subscriptionController.setFilterRole(role);
                        } else {
                            window.subscriptionController.filterRole = role;
                            window.subscriptionController.selectedFilterUser = null;
                            if(typeof window.subscriptionController.triggerFilter === 'function') {
                                window.subscriptionController.triggerFilter();
                            }
                        }
                    } else {
                        // Safe set for modal role
                         if(typeof window.subscriptionController.setModalRole === 'function') {
                            window.subscriptionController.setModalRole(role);
                        } else {
                            window.subscriptionController.modalRole = role;
                        }
                    }
                    
                    // Fetch latest 10 users for this role
                    if(typeof window.subscriptionController.searchUsers === 'function') {
                        window.subscriptionController.searchUsers('', role, context);
                    }
            }
        }

        function backToRoles(context) {
            const rolesView = document.getElementById(context === 'filter' ? 'user-filter-roles' : 'modal-user-roles');
            const searchView = document.getElementById(context === 'filter' ? 'user-filter-search-area' : 'modal-user-search-area');
            
            if(rolesView && searchView) {
                searchView.classList.add('hidden');
                searchView.classList.remove('flex');
                rolesView.classList.remove('hidden');
            }
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            const userContainer = document.getElementById('user-filter-container');
            const userDropdown = document.getElementById('user-filter-dropdown');
            if(userContainer && userDropdown && !userContainer.contains(e.target)) {
                userDropdown.classList.add('hidden');
            }

            const planContainer = document.getElementById('plan-filter-container');
            const planDropdown = document.getElementById('plan-filter-dropdown');
            if(planContainer && planDropdown && !planContainer.contains(e.target)) {
                planDropdown.classList.add('hidden');
            }
            
             // Modal user picker
            const modalUserPicker = document.getElementById('modal-user-picker');
            const modalUserDropdown = document.getElementById('modal-user-dropdown');
            if(modalUserPicker && modalUserDropdown && !modalUserPicker.contains(e.target) && !e.target.closest('#modal-user-trigger')) {
                 modalUserDropdown.classList.add('hidden');
            }
        });
    </script>
@endpush
@endsection
