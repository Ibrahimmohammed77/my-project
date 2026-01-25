@extends('layouts.app')

@section('title', 'إدارة الصلاحيات')
@section('page-title', 'إدارة الصلاحيات')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between">
        <input type="text" id="search" placeholder="بحث..." class="px-4 py-2 border rounded-lg">
        <button onclick="showCreateModal()" class="bg-accent text-white px-6 py-2 rounded-lg">
            <i class="fas fa-plus ml-2"></i>إضافة صلاحية
        </button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الاسم</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">نوع المورد</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراء</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الوصف</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                </tr>
            </thead>
            <tbody id="permissions-tbody" class="divide-y divide-gray-200"></tbody>
        </table>
    </div>
</div>

<div id="permission-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-xl font-bold mb-4" id="modal-title">إضافة صلاحية</h3>
        <form id="permission-form" class="space-y-4">
            <input type="hidden" id="permission-id">
            <div>
                <label class="block text-sm font-medium mb-1">الاسم</label>
                <input type="text" id="name" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">نوع المورد</label>
                <input type="text" id="resource_type" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">الإجراء</label>
                <input type="text" id="action" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">الوصف</label>
                <textarea id="description" class="w-full px-4 py-2 border rounded-lg" rows="2"></textarea>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeModal()" class="px-4 py-2 border rounded-lg">إلغاء</button>
                <button type="submit" class="px-4 py-2 bg-accent text-white rounded-lg">حفظ</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let permissions = [];

async function loadPermissions() {
    const res = await axios.get('/permissions');
    permissions = res.data.data.permissions;
    renderPermissions();
}

function renderPermissions() {
    document.getElementById('permissions-tbody').innerHTML = permissions.map(perm => `
        <tr>
            <td class="px-6 py-4 font-medium">${perm.name}</td>
            <td class="px-6 py-4 text-sm">${perm.resource_type}</td>
            <td class="px-6 py-4 text-sm">${perm.action}</td>
            <td class="px-6 py-4 text-sm text-gray-600">${perm.description || '-'}</td>
            <td class="px-6 py-4">
                <button onclick="editPermission(${perm.permission_id})" class="text-blue-600 hover:text-blue-800 ml-3">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="deletePermission(${perm.permission_id})" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function showCreateModal() {
    document.getElementById('modal-title').textContent = 'إضافة صلاحية';
    document.getElementById('permission-form').reset();
    document.getElementById('permission-id').value = '';
    document.getElementById('permission-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('permission-modal').classList.add('hidden');
}

async function editPermission(id) {
    const perm = permissions.find(p => p.permission_id === id);
    document.getElementById('modal-title').textContent = 'تعديل صلاحية';
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
        if (id) {
            await axios.put(`/permissions/${id}`, data);
        } else {
            await axios.post('/permissions', data);
        }
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
