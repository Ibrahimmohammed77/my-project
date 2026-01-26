@extends('layouts.app')
@section('title', 'إدارة الحسابات')
@section('header', 'إدارة الحسابات')

@section('content')
    <x-page-header title="إدارة الحسابات">
        <!-- Search -->
        <div class="relative min-w-[240px]">
            <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" id="search" placeholder="بحث..." class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm">
        </div>

        <!-- Filter -->
        <div class="relative min-w-[160px]">
            <i class="fas fa-filter absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <select id="status-filter" class="w-full pl-4 pr-10 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-accent focus:ring-2 focus:ring-accent/20 outline-none transition-all text-sm appearance-none cursor-pointer text-gray-600">
                <option value="">جميع الحالات</option>
                <option value="ACTIVE">نشط</option>
                <option value="PENDING">قيد المراجعة</option>
                <option value="SUSPENDED">موقوف</option>
            </select>
            <i class="fas fa-chevron-down absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
        </div>
        
        <!-- Create Button -->
        <x-button onclick="showCreateModal()" variant="primary">
            <i class="fas fa-plus text-xs"></i>
            <span>حساب جديد</span>
        </x-button>
    </x-page-header>

    <!-- Generic Table Component -->
    <x-table :headers="[
        ['name' => 'المستخدم', 'class' => 'w-1/4'],
        ['name' => 'معلومات الاتصال'],
        ['name' => 'الحالة'],
        ['name' => 'الأدوار'],
        ['name' => 'إجراءات', 'class' => 'text-center']
    ]" id="accounts">
        <!-- JS renders rows here -->
    </x-table>

    <!-- Create/Edit Modal Component -->
    <x-modal id="account-modal" title="إضافة حساب جديد">
        <form id="account-form" class="space-y-4">
            <input type="hidden" id="account-id">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-form.input name="username" label="اسم المستخدم" required icon="fa-at" placeholder="username" />
                <x-form.input name="full_name" label="الاسم الكامل" required icon="fa-user" placeholder="الاسم الظاهر" />
            </div>

            <x-form.input name="email" label="البريد الإلكتروني" type="email" icon="fa-envelope" placeholder="example@domain.com" />
            
            <x-form.input name="phone" label="رقم الهاتف" required icon="fa-phone" placeholder="05xxxxxxxx" />

            <div id="password-field">
                <x-form.input name="password" label="كلمة المرور" type="password" required icon="fa-lock" placeholder="••••••••" />
            </div>
        </form>

        <x-slot name="footer">
            <x-button type="submit" form="account-form" variant="primary">حفظ التغييرات</x-button>
            <x-button type="button" onclick="closeModal()" variant="secondary">إلغاء</x-button>
        </x-slot>
    </x-modal>

@push('scripts')
<script>
let accounts = [];

// Load data with loading state
async function loadAccounts() {
    const tbody = document.getElementById('accounts-tbody');
    const loadingState = document.getElementById('loading-state');
    const emptyState = document.getElementById('empty-state');
    
    if(!tbody) return; // Safety check

    tbody.innerHTML = '';
    loadingState?.classList.remove('hidden');
    emptyState?.classList.add('hidden');

    try {
        const res = await axios.get('/accounts');
        accounts = res.data.data.accounts;
        loadingState?.classList.add('hidden');
        renderAccounts();
    } catch (error) {
        console.error('Error loading accounts:', error);
        if(loadingState) loadingState.innerHTML = '<p class="text-red-500 text-center py-4">حدث خطأ أثناء تحميل البيانات</p>';
    }
}

function renderAccounts() {
    const tbody = document.getElementById('accounts-tbody');
    const emptyState = document.getElementById('empty-state');
    const searchInput = document.getElementById('search');
    const statusSelect = document.getElementById('status-filter');
    
    if (!tbody) return;

    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    const statusFilter = statusSelect ? statusSelect.value : '';
    
    const filteredAccounts = accounts.filter(account => {
        const matchesSearch = (account.full_name.toLowerCase().includes(searchTerm) || 
                             account.username.toLowerCase().includes(searchTerm) || 
                             (account.email && account.email.toLowerCase().includes(searchTerm)));
        const matchesStatus = !statusFilter || account.status?.code === statusFilter;
        return matchesSearch && matchesStatus;
    });

    if (filteredAccounts.length === 0) {
        emptyState?.classList.remove('hidden');
        tbody.innerHTML = '';
        return;
    }

    emptyState?.classList.add('hidden');

    tbody.innerHTML = filteredAccounts.map(account => `
        <tr class="hover:bg-gray-50/50 transition-colors border-b border-gray-50 last:border-0 group">
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-700 font-bold text-sm uppercase shrink-0 group-hover:bg-white group-hover:shadow-sm transition-all">
                        ${account.full_name.charAt(0)}
                    </div>
                    <div>
                        <div class="font-bold text-gray-900 text-sm">${account.full_name}</div>
                        <div class="text-xs text-gray-500 font-mono">@${account.username}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="flex flex-col gap-1 text-xs">
                    ${account.email ? `
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="fas fa-envelope text-gray-400 w-3"></i>
                            <span class="font-mono">${account.email}</span>
                        </div>
                    ` : ''}
                    <div class="flex items-center gap-2 text-gray-600">
                        <i class="fas fa-phone text-gray-400 w-3"></i>
                        <span class="font-mono">${account.phone}</span>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold ${getStatusClass(account.status?.code)} border border-current/10">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                    ${account.status?.name || 'غير محدد'}
                </span>
            </td>
            <td class="px-6 py-4">
                <div class="flex flex-wrap gap-1">
                    ${account.roles?.length ? account.roles.map(r => `
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold ${r.is_system ? 'bg-purple-50 text-purple-700 border border-purple-100' : 'bg-blue-50 text-blue-700 border border-blue-100'}">
                            ${r.name}
                        </span>
                    `).join('') : '<span class="text-xs text-gray-400 italic">لا توجد أدوار</span>'}
                </div>
            </td>
            <td class="px-6 py-4 text-center">
                <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-all">
                    <button onclick="editAccount(${account.account_id})" class="h-8 w-8 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all flex items-center justify-center" title="تعديل">
                        <i class="fas fa-pen text-xs"></i>
                    </button>
                    <button onclick="deleteAccount(${account.account_id})" class="h-8 w-8 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all flex items-center justify-center" title="حذف">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function getStatusClass(code) {
    const classes = {
        'ACTIVE': 'bg-green-50 text-green-700',
        'PENDING': 'bg-yellow-50 text-yellow-700',
        'SUSPENDED': 'bg-red-50 text-red-700'
    };
    return classes[code] || 'bg-gray-50 text-gray-700';
}

// Add event listeners safely
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('search');
    const statusFilter = document.getElementById('status-filter');

    if (searchInput) searchInput.addEventListener('input', renderAccounts);
    if (statusFilter) statusFilter.addEventListener('change', renderAccounts);

    loadAccounts();
});

// Modal functions
function showCreateModal() {
    const modalTitle = document.getElementById('account-modal-title');
    if(modalTitle) modalTitle.innerHTML = '<span class="w-2 h-6 bg-accent rounded-full"></span><span>إضافة حساب جديد</span>';
    
    document.getElementById('account-form').reset();
    document.getElementById('account-id').value = '';
    
    const pwdField = document.getElementById('password-field');
    const pwdInput = document.getElementById('password');
    if(pwdField) pwdField.style.display = 'block';
    if(pwdInput) pwdInput.required = true;
    
    document.getElementById('account-modal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('account-modal').classList.add('hidden');
}

async function editAccount(id) {
    const account = accounts.find(a => a.account_id === id);
    if (!account) return;
    
    const modalTitle = document.getElementById('account-modal-title');
    if(modalTitle) modalTitle.innerHTML = '<span class="w-2 h-6 bg-orange-500 rounded-full"></span><span>تعديل حساب</span>';
    
    document.getElementById('account-id').value = account.account_id;
    document.getElementById('username').value = account.username;
    document.getElementById('full_name').value = account.full_name;
    document.getElementById('email').value = account.email || '';
    document.getElementById('phone').value = account.phone;
    
    const pwdField = document.getElementById('password-field');
    const pwdInput = document.getElementById('password');
    if(pwdField) pwdField.style.display = 'none';
    if(pwdInput) pwdInput.required = false;

    document.getElementById('account-modal').classList.remove('hidden');
}

// Form submission
const accountForm = document.getElementById('account-form');
if (accountForm) {
    accountForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('account-id').value;
        
        // Manual form data gathering because Components might structure inputs differently (though here they are standard inputs with names)
        const data = {
            username: document.getElementById('username').value,
            full_name: document.getElementById('full_name').value,
            email: document.getElementById('email').value || null,
            phone: document.getElementById('phone').value,
            account_status_id: 1
        };
        
        if (!id) data.password = document.getElementById('password').value;
        
        try {
            if (id) await axios.put(`/accounts/${id}`, data);
            else await axios.post('/accounts', data);
            
            closeModal();
            loadAccounts();
        } catch (error) {
            alert('حدث خطأ: ' + (error.response?.data?.message || error.message));
        }
    });
}

async function deleteAccount(id) {
    if (!confirm('هل أنت متأكد من حذف هذا الحساب؟')) return;
    try {
        await axios.delete(`/accounts/${id}`);
        loadAccounts();
    } catch (error) {
        alert('حدث خطأ: ' + (error.response?.data?.message || error.message));
    }
}
</script>
@endpush
@endsection
