@extends('layouts.app')

@section('title', 'إدارة الحسابات')
@section('page-title', 'إدارة الحسابات')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex justify-between items-center">
        <div class="flex gap-3">
            <input type="text" id="search" placeholder="بحث..." class="px-4 py-2 border rounded-lg">
            <select id="status-filter" class="px-4 py-2 border rounded-lg">
                <option value="">جميع الحالات</option>
                <option value="ACTIVE">نشط</option>
                <option value="PENDING">قيد المراجعة</option>
                <option value="SUSPENDED">موقوف</option>
            </select>
        </div>
        <button onclick="showCreateModal()" class="bg-accent text-white px-6 py-2 rounded-lg hover:bg-accent-hover">
            <i class="fas fa-plus ml-2"></i>إضافة حساب
        </button>
    </div>

    <!-- Accounts Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المستخدم</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">البريد/الهاتف</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الأدوار</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">إجراءات</th>
                </tr>
            </thead>
            <tbody id="accounts-tbody" class="divide-y divide-gray-200">
                <!-- Data will be loaded here -->
            </tbody>
        </table>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="account-modal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md">
        <h3 class="text-xl font-bold mb-4" id="modal-title">إضافة حساب</h3>
        <form id="account-form" class="space-y-4">
            <input type="hidden" id="account-id">
            <div>
                <label class="block text-sm font-medium mb-1">اسم المستخدم</label>
                <input type="text" id="username" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">الاسم الكامل</label>
                <input type="text" id="full_name" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">البريد الإلكتروني</label>
                <input type="email" id="email" class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">رقم الهاتف</label>
                <input type="text" id="phone" required class="w-full px-4 py-2 border rounded-lg">
            </div>
            <div id="password-field">
                <label class="block text-sm font-medium mb-1">كلمة المرور</label>
                <input type="password" id="password" class="w-full px-4 py-2 border rounded-lg">
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
let accounts = [];
let statuses = [];

// Load data
async function loadAccounts() {
    try {
        const res = await axios.get('/accounts');
        accounts = res.data.data.accounts;
        renderAccounts();
    } catch (error) {
        console.error('Error loading accounts:', error);
    }
}

function renderAccounts() {
    const tbody = document.getElementById('accounts-tbody');
    tbody.innerHTML = accounts.map(account => `
        <tr>
            <td class="px-6 py-4">
                <div class="font-medium">${account.full_name}</div>
                <div class="text-sm text-gray-500">@${account.username}</div>
            </td>
            <td class="px-6 py-4 text-sm">
                <div>${account.email || '-'}</div>
                <div class="text-gray-500">${account.phone}</div>
            </td>
            <td class="px-6 py-4">
                <span class="px-2 py-1 text-xs rounded-full ${getStatusClass(account.status?.code)}">
                    ${account.status?.name || '-'}
                </span>
            </td>
            <td class="px-6 py-4 text-sm">
                ${account.roles?.map(r => r.name).join(', ') || '-'}
            </td>
            <td class="px-6 py-4">
                <button onclick="editAccount(${account.account_id})" class="text-blue-600 hover:text-blue-800 ml-3">
                    <i class="fas fa-edit"></i>
                </button>
                <button onclick="deleteAccount(${account.account_id})" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

function getStatusClass(code) {
    const classes = {
        'ACTIVE': 'bg-green-100 text-green-800',
        'PENDING': 'bg-yellow-100 text-yellow-800',
        'SUSPENDED': 'bg-red-100 text-red-800'
    };
    return classes[code] || 'bg-gray-100 text-gray-800';
}

function showCreateModal() {
    document.getElementById('modal-title').textContent = 'إضافة حساب';
    document.getElementById('account-form').reset();
    document.getElementById('account-id').value = '';
    document.getElementById('password-field').style.display = 'block';
    document.getElementById('password').required = true;
    document.getElementById('account-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('account-modal').classList.add('hidden');
}

async function editAccount(id) {
    const account = accounts.find(a => a.account_id === id);
    if (!account) return;
    
    document.getElementById('modal-title').textContent = 'تعديل حساب';
    document.getElementById('account-id').value = account.account_id;
    document.getElementById('username').value = account.username;
    document.getElementById('full_name').value = account.full_name;
    document.getElementById('email').value = account.email || '';
    document.getElementById('phone').value = account.phone;
    document.getElementById('password-field').style.display = 'none';
    document.getElementById('password').required = false;
    document.getElementById('account-modal').classList.remove('hidden');
}

document.getElementById('account-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const id = document.getElementById('account-id').value;
    const data = {
        username: document.getElementById('username').value,
        full_name: document.getElementById('full_name').value,
        email: document.getElementById('email').value || null,
        phone: document.getElementById('phone').value,
        account_status_id: 1 // Default status
    };
    
    if (!id) {
        data.password = document.getElementById('password').value;
    }
    
    try {
        if (id) {
            await axios.put(`/accounts/${id}`, data);
        } else {
            await axios.post('/accounts', data);
        }
        closeModal();
        loadAccounts();
    } catch (error) {
        alert('حدث خطأ: ' + (error.response?.data?.message || error.message));
    }
});

async function deleteAccount(id) {
    if (!confirm('هل أنت متأكد من حذف هذا الحساب؟')) return;
    
    try {
        await axios.delete(`/accounts/${id}`);
        loadAccounts();
    } catch (error) {
        alert('حدث خطأ: ' + (error.response?.data?.message || error.message));
    }
}

// Initialize
loadAccounts();
</script>
@endpush
@endsection
