@extends('layouts.app')

@section('title', 'إدارة الأدوار')
@section('header', 'إدارة الأدوار')

@section('content')
    <x-page-header title="إدارة الأدوار">
        <div class="relative min-w-[240px]">
            <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" id="search" placeholder="بحث عن دور..." class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
        </div>
        
        <x-button onclick="showCreateModal()" variant="primary">
            <i class="fas fa-plus text-xs"></i>
            <span>إضافة دور جديد</span>
        </x-button>
    </x-page-header>

    <!-- Roles Table -->
    <x-table :headers="[
        ['name' => 'الدور', 'class' => 'w-1/4'],
        ['name' => 'الوصف', 'class' => 'w-1/3'],
        ['name' => 'الصلاحيات'],
        ['name' => 'إجراءات', 'class' => 'text-center']
    ]" id="roles">
        <!-- JS renders rows here -->
    </x-table>

<!-- Create/Edit Modal -->
<div id="role-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>

    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-2xl bg-white text-right shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
            <!-- Modal Header -->
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2" id="modal-title">
                    <span class="w-2 h-6 bg-accent rounded-full"></span>
                    <span>إضافة دور جديد</span>
                </h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="px-6 py-6">
                <form id="role-form" class="space-y-5">
                    <input type="hidden" id="role-id">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">اسم الدور <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <i class="fas fa-tag absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            <input type="text" id="name" required class="w-full pr-8 pl-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm" placeholder="مثال: مدير المبيعات">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 mb-1.5">الوصف</label>
                        <textarea id="description" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm" rows="3" placeholder="وصف وتوضيح مهام هذا الدور..."></textarea>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex flex-row-reverse gap-3">
                <button type="submit" form="role-form" class="flex-1 sm:flex-none justify-center rounded-xl bg-accent px-6 py-2.5 text-sm font-bold text-white shadow-lg hover:bg-accent-hover active:scale-95 transition-all">حفظ التغييرات</button>
                <button type="button" onclick="closeModal()" class="flex-1 sm:flex-none justify-center rounded-xl border border-gray-200 bg-white px-6 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50 hover:text-gray-900 active:scale-95 transition-all">إلغاء</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let roles = [];

async function loadRoles() {
    const tbody = document.getElementById('roles-tbody');
    const loadingState = document.getElementById('loading-state');
    const emptyState = document.getElementById('empty-state');
    
    tbody.innerHTML = '';
    loadingState.classList.remove('hidden');
    emptyState.classList.add('hidden');

    try {
        const res = await axios.get('/roles');
        roles = res.data.data.roles;
        loadingState.classList.add('hidden');
        renderRoles();
    } catch (error) {
        console.error('Error loading roles:', error);
        loadingState.innerHTML = '<p class="text-red-500">حدث خطأ أثناء تحميل البيانات</p>';
    }
}

function renderRoles() {
    const tbody = document.getElementById('roles-tbody');
    const emptyState = document.getElementById('empty-state');
    const searchTerm = document.getElementById('search').value.toLowerCase();
    
    const filteredRoles = roles.filter(role => 
        role.name.toLowerCase().includes(searchTerm) || 
        (role.description && role.description.toLowerCase().includes(searchTerm))
    );

    if (filteredRoles.length === 0) {
        emptyState.classList.remove('hidden');
        tbody.innerHTML = '';
        return;
    }

    emptyState.classList.add('hidden');

    tbody.innerHTML = filteredRoles.map(role => `
        <tr class="hover:bg-gray-50/80 transition-colors group">
            <td class="px-6 py-4">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-2xl ${role.is_system ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600'} flex items-center justify-center font-bold text-lg shrink-0 shadow-sm border border-white">
                        <i class="fas ${role.is_system ? 'fa-shield-halved' : 'fa-user-shield'}"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-900 text-sm">${role.name}</span>
                            ${role.is_system ? 
                                '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-100"><i class="fas fa-lock text-[9px]"></i> نظامي</span>' 
                                : ''}
                        </div>
                        <div class="text-[11px] text-gray-400 mt-0.5 font-mono">ID: ${role.role_id}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="max-w-md">
                    <p class="text-sm text-gray-600 leading-relaxed line-clamp-2" title="${role.description}">
                        ${role.description || '<span class="text-gray-400 italic font-light">لا يوجد وصف متاح لهذا الدور</span>'}
                    </p>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                    <div class="flex -space-x-2 space-x-reverse overflow-hidden p-1">
                        <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 border-2 border-white text-gray-400 text-xs shadow-sm">
                            <i class="fas fa-key"></i>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold bg-gray-50 text-gray-700 border border-gray-200 shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full ${role.permissions?.length > 0 ? 'bg-green-500' : 'bg-gray-400'}"></span>
                        ${role.permissions?.length || 0} صلاحية
                    </span>
                </div>
            </td>
            <td class="px-6 py-4">
                ${!role.is_system ? `
                    <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                        <button onclick="editRole(${role.role_id})" class="h-9 w-9 rounded-xl bg-white border border-gray-200 text-blue-600 hover:bg-blue-50 hover:border-blue-200 flex items-center justify-center transition-all shadow-sm hover:shadow-md" title="تعديل">
                            <i class="fas fa-pen text-xs"></i>
                        </button>
                        <button onclick="deleteRole(${role.role_id})" class="h-9 w-9 rounded-xl bg-white border border-gray-200 text-red-600 hover:bg-red-50 hover:border-red-200 flex items-center justify-center transition-all shadow-sm hover:shadow-md" title="حذف">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                ` : `
                    <div class="flex justify-center">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium bg-gray-50 text-gray-400 border border-gray-100 opacity-50 cursor-not-allowed">
                            <i class="fas fa-lock text-[10px]"></i>
                            محمي
                        </span>
                    </div>
                `}
            </td>
        </tr>
    `).join('');
}

// Search
document.getElementById('search').addEventListener('input', renderRoles);

function showCreateModal() {
    document.getElementById('modal-title').innerHTML = '<span class="w-2 h-6 bg-accent rounded-full"></span><span>إضافة دور جديد</span>';
    document.getElementById('role-form').reset();
    document.getElementById('role-id').value = '';
    document.getElementById('role-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('role-modal').classList.add('hidden');
}

async function editRole(id) {
    const role = roles.find(r => r.role_id === id);
    if (!role) return;
    
    document.getElementById('modal-title').innerHTML = '<span class="w-2 h-6 bg-orange-500 rounded-full"></span><span>تعديل دور</span>';
    document.getElementById('role-id').value = role.role_id;
    document.getElementById('name').value = role.name;
    document.getElementById('description').value = role.description || '';
    document.getElementById('role-modal').classList.remove('hidden');
}

document.getElementById('role-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('role-id').value;
    const data = {
        name: document.getElementById('name').value,
        description: document.getElementById('description').value
    };
    
    try {
        if (id) await axios.put(`/roles/${id}`, data);
        else await axios.post('/roles', data);
        
        closeModal();
        loadRoles();
    } catch (error) {
        alert('حدث خطأ: ' + (error.response?.data?.message || error.message));
    }
});

async function deleteRole(id) {
    if (!confirm('هل أنت متأكد من حذف هذا الدور؟')) return;
    try {
        await axios.delete(`/roles/${id}`);
        loadRoles();
    } catch (error) {
        alert('حدث خطأ: ' + (error.response?.data?.message || error.message));
    }
}

loadRoles();
</script>
@endpush
@endsection
