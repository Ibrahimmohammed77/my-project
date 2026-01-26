@extends('layouts.app')

@section('title', 'إدارة الصلاحيات')
@section('header', 'إدارة الصلاحيات')

@section('content')
    <x-page-header title="إدارة الصلاحيات">
        <div class="relative min-w-[240px]">
            <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" id="search" placeholder="بحث عن صلاحية..." class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
        </div>
        
        <x-button onclick="showCreateModal()" variant="primary">
            <i class="fas fa-plus text-xs"></i>
            <span>إضافة صلاحية</span>
        </x-button>
    </x-page-header>

    <!-- Permissions Table -->
    <x-table :headers="[
        ['name' => 'الصلاحية والمورد', 'class' => 'w-1/3'],
        ['name' => 'الإجراء'],
        ['name' => 'الوصف', 'class' => 'w-1/2'],
        ['name' => 'إجراءات', 'class' => 'text-center']
    ]" id="permissions">
        <!-- JS renders rows here -->
    </x-table>

<!-- Create/Edit Modal -->
<div id="permission-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>

    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-right shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
            <!-- Modal Header -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2" id="modal-title">
                    <span class="w-2 h-6 bg-accent rounded-full"></span>
                    <span>إضافة صلاحية جديدة</span>
                </h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-6">
                <form id="permission-form" class="space-y-5">
                    <input type="hidden" id="permission-id">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">الاسم <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-tag absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            <input type="text" id="name" required class="w-full pr-8 pl-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm" placeholder="مثال: إنشاء مستخدم">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-5">
                        <div class="col-span-1">
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">نوع المورد <span class="text-red-500">*</span></label>
                            <input type="text" id="resource_type" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm" placeholder="مثال: accounts">
                        </div>
                        <div class="col-span-1">
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">الإجراء <span class="text-red-500">*</span></label>
                            <input type="text" id="action" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm" placeholder="مثال: create">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">الوصف</label>
                        <textarea id="description" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm" rows="2" placeholder="شرح مختصر لهذا الإجراء..."></textarea>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex flex-row-reverse gap-3">
                <button type="submit" form="permission-form" class="flex-1 sm:flex-none justify-center rounded-xl bg-accent px-6 py-2.5 text-sm font-bold text-white shadow-lg hover:bg-accent-hover active:scale-95 transition-all">حفظ التغييرات</button>
                <button type="button" onclick="closeModal()" class="flex-1 sm:flex-none justify-center rounded-xl border border-gray-200 bg-white px-6 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50 hover:text-gray-900 active:scale-95 transition-all">إلغاء</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let permissions = [];

async function loadPermissions() {
    const tbody = document.getElementById('permissions-tbody');
    const loadingState = document.getElementById('loading-state');
    const emptyState = document.getElementById('empty-state');
    
    tbody.innerHTML = '';
    loadingState.classList.remove('hidden');
    emptyState.classList.add('hidden');

    try {
        const res = await axios.get('/permissions');
        permissions = res.data.data.permissions;
        loadingState.classList.add('hidden');
        renderPermissions();
    } catch (error) {
        console.error('Error loading permissions:', error);
        loadingState.innerHTML = '<p class="text-red-500">حدث خطأ أثناء تحميل البيانات</p>';
    }
}

function renderPermissions() {
    const tbody = document.getElementById('permissions-tbody');
    const emptyState = document.getElementById('empty-state');
    const searchTerm = document.getElementById('search').value.toLowerCase();
    
    const filteredPerms = permissions.filter(perm => 
        perm.name.toLowerCase().includes(searchTerm) || 
        perm.resource_type.toLowerCase().includes(searchTerm) || 
        perm.action.toLowerCase().includes(searchTerm)
    );

    if (filteredPerms.length === 0) {
        emptyState.classList.remove('hidden');
        tbody.innerHTML = '';
        return;
    }

    emptyState.classList.add('hidden');

    tbody.innerHTML = filteredPerms.map(perm => `
        <tr class="hover:bg-gray-50/80 transition-colors group">
            <td class="px-6 py-4">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-2xl ${getResourceColor(perm.resource_type)} flex items-center justify-center font-bold text-lg shrink-0 shadow-sm border border-white">
                        <i class="fas ${getResourceIcon(perm.resource_type)}"></i>
                    </div>
                    <div>
                        <div class="font-bold text-gray-900 text-sm mb-1">${perm.name}</div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] text-gray-400 font-mono bg-gray-50 px-1.5 rounded border border-gray-100">ID: ${perm.permission_id}</span>
                            <span class="text-[11px] text-gray-400">${perm.resource_type}</span>
                        </div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold ${getActionClass(perm.action)} border border-current/10 shadow-sm">
                    <i class="fas ${getActionIcon(perm.action)} text-xs"></i>
                    <span class="uppercase tracking-wider">${perm.action}</span>
                </span>
            </td>
            <td class="px-6 py-4">
                <div class="max-w-xs">
                    <p class="text-sm text-gray-600 leading-relaxed line-clamp-2" title="${perm.description}">
                        ${perm.description || '<span class="text-gray-400 italic font-light">لا يوجد وصف متاح لهذه الصلاحية</span>'}
                    </p>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                    <button onclick="editPermission(${perm.permission_id})" class="h-9 w-9 rounded-xl bg-white border border-gray-200 text-blue-600 hover:bg-blue-50 hover:border-blue-200 flex items-center justify-center transition-all shadow-sm hover:shadow-md" title="تعديل">
                        <i class="fas fa-pen text-xs"></i>
                    </button>
                    <button onclick="deletePermission(${perm.permission_id})" class="h-9 w-9 rounded-xl bg-white border border-gray-200 text-red-600 hover:bg-red-50 hover:border-red-200 flex items-center justify-center transition-all shadow-sm hover:shadow-md" title="حذف">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function getActionClass(action) {
    const map = {
        'create': 'bg-green-50 text-green-700',
        'read': 'bg-blue-50 text-blue-700',
        'update': 'bg-orange-50 text-orange-700',
        'delete': 'bg-red-50 text-red-700',
        'manage': 'bg-purple-50 text-purple-700'
    };
    return map[action.toLowerCase()] || 'bg-gray-100 text-gray-700';
}

function getActionIcon(action) {
    const map = {
        'create': 'fa-plus',
        'read': 'fa-eye',
        'update': 'fa-pen-to-square',
        'delete': 'fa-trash-can',
        'manage': 'fa-sliders'
    };
    return map[action.toLowerCase()] || 'fa-circle';
}

function getResourceIcon(resource) {
    const map = {
        'accounts': 'fa-users',
        'roles': 'fa-user-shield',
        'permissions': 'fa-key',
        'logs': 'fa-clipboard-list',
        'settings': 'fa-gear',
        'dashboard': 'fa-chart-pie'
    };
    return map[resource.toLowerCase()] || 'fa-box-open';
}

function getResourceColor(resource) {
    const map = {
        'accounts': 'bg-blue-100 text-blue-600',
        'roles': 'bg-purple-100 text-purple-600',
        'permissions': 'bg-amber-100 text-amber-600',
        'logs': 'bg-gray-100 text-gray-600',
        'settings': 'bg-slate-100 text-slate-600'
    };
    return map[resource.toLowerCase()] || 'bg-indigo-50 text-indigo-600';
}

// Search
document.getElementById('search').addEventListener('input', renderPermissions);

function showCreateModal() {
    document.getElementById('modal-title').innerHTML = '<span class="w-2 h-6 bg-accent rounded-full"></span><span>إضافة صلاحية جديدة</span>';
    document.getElementById('permission-form').reset();
    document.getElementById('permission-id').value = '';
    document.getElementById('permission-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('permission-modal').classList.add('hidden');
}

async function editPermission(id) {
    const perm = permissions.find(p => p.permission_id === id);
    document.getElementById('modal-title').innerHTML = '<span class="w-2 h-6 bg-orange-500 rounded-full"></span><span>تعديل صلاحية</span>';
    document.getElementById('permission-id').value = perm.permission_id;
    document.getElementById('name').value = perm.name;
    document.getElementById('resource_type').value = perm.resource_type;
    document.getElementById('action').value = perm.action;
    document.getElementById('description').value = perm.description || '';
    document.getElementById('permission-modal').classList.remove('hidden');
}

document.getElementById('permission-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('permission-id').value;
    const data = {
        name: document.getElementById('name').value,
        resource_type: document.getElementById('resource_type').value,
        action: document.getElementById('action').value,
        description: document.getElementById('description').value
    };
    
    try {
        if (id) await axios.put(`/permissions/${id}`, data);
        else await axios.post('/permissions', data);
        
        closeModal();
        loadPermissions();
    } catch (error) {
        alert('حدث خطأ: ' + (error.response?.data?.message || error.message));
    }
});

async function deletePermission(id) {
    if (!confirm('هل أنت متأكد من حذف هذه الصلاحية؟')) return;
    try {
        await axios.delete(`/permissions/${id}`);
        loadPermissions();
    } catch (error) {
        alert('حدث خطأ: ' + (error.response?.data?.message || error.message));
    }
}

loadPermissions();
</script>
@endpush
@endsection
