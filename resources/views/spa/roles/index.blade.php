@extends('layouts.app')

@section('title', 'إدارة الأدوار')
@section('page-title', 'إدارة الأدوار')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between">
        <input type="text" id="search" placeholder="بحث..." class="px-4 py-2 border rounded-lg">
        <button onclick="showCreateModal()" class="bg-accent text-white px-6 py-2 rounded-lg">
            <i class="fas fa-plus ml-2"></i>إضافة دور
        </button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الاسم</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الوصف</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الصلاحيات</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                </tr>
            </thead>
            <tbody id="roles-tbody" class="divide-y divide-gray-200"></tbody>
        </table>
    </div>
</div>

<div id="role-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-xl font-bold mb-4" id="modal-title">إضافة دور</h3>
        <form id="role-form" class="space-y-4">
            <input type="hidden" id="role-id">
            <div>
                <label class="block text-sm font-medium mb-1">الاسم</label>
                <input type="text" id="name" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">الوصف</label>
                <textarea id="description" class="w-full px-4 py-2 border rounded-lg" rows="3"></textarea>
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
let roles = [];

async function loadRoles() {
    const res = await axios.get('/roles');
    roles = res.data.data.roles;
    renderRoles();
}

function renderRoles() {
    document.getElementById('roles-tbody').innerHTML = roles.map(role => `
        <tr>
            <td class="px-6 py-4 font-medium">${role.name}</td>
            <td class="px-6 py-4 text-sm text-gray-600">${role.description || '-'}</td>
            <td class="px-6 py-4 text-sm">${role.permissions?.length || 0} صلاحية</td>
            <td class="px-6 py-4">
                ${!role.is_system ? `
                    <button onclick="editRole(${role.role_id})" class="text-blue-600 hover:text-blue-800 ml-3">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteRole(${role.role_id})" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-trash"></i>
                    </button>
                ` : '<span class="text-gray-400">نظامي</span>'}
            </td>
        </tr>
    `).join('');
}

function showCreateModal() {
    document.getElementById('modal-title').textContent = 'إضافة دور';
    document.getElementById('role-form').reset();
    document.getElementById('role-id').value = '';
    document.getElementById('role-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('role-modal').classList.add('hidden');
}

async function editRole(id) {
    const role = roles.find(r => r.role_id === id);
    document.getElementById('modal-title').textContent = 'تعديل دور';
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
        if (id) {
            await axios.put(`/roles/${id}`, data);
        } else {
            await axios.post('/roles', data);
        }
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
