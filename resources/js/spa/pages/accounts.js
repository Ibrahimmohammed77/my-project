import { Account } from '../models/Account';
import { AccountService } from '../services/AccountService';

let accounts = [];

// DOM Elements
const tbody = document.getElementById('accounts-tbody');
const loadingState = document.getElementById('loading-state');
const emptyState = document.getElementById('empty-state');
const searchInput = document.getElementById('search');
const statusFilter = document.getElementById('status-filter');
const accountModal = document.getElementById('account-modal');
const accountForm = document.getElementById('account-form');
const modalTitle = document.getElementById('account-modal-title');
const accountIdInput = document.getElementById('account-id');
const pwdField = document.getElementById('password-field');
const pwdInput = document.getElementById('password');

// Global Functions
window.editAccount = async (id) => {
    const account = accounts.find(a => a.account_id === id);
    if (!account) return;
    
    if(modalTitle) modalTitle.innerHTML = '<span class="w-2 h-6 bg-orange-500 rounded-full"></span><span>تعديل حساب</span>';
    
    accountIdInput.value = account.account_id;
    document.getElementById('username').value = account.username;
    document.getElementById('full_name').value = account.full_name;
    document.getElementById('email').value = account.email || '';
    document.getElementById('phone').value = account.phone;
    
    if(account.account_status_id) {
        document.getElementById('account_status_id').value = account.account_status_id;
    }
    
    if(account.account_type_id) {
        document.getElementById('account_type_id').value = account.account_type_id;
    }
    
    if(pwdField) pwdField.style.display = 'none';
    if(pwdInput) pwdInput.required = false;

    accountModal.classList.remove('hidden');
};

window.deleteAccount = async (id) => {
    if (!confirm('هل أنت متأكد من حذف هذا الحساب؟')) return;
    try {
        await AccountService.delete(id);
        loadAccounts();
    } catch (error) {
        alert('حدث خطأ: ' + (error.response?.data?.message || error.message));
    }
};

window.showCreateModal = () => {
    if(modalTitle) modalTitle.innerHTML = '<span class="w-2 h-6 bg-accent rounded-full"></span><span>إضافة حساب جديد</span>';
    
    accountForm.reset();
    accountIdInput.value = '';
    
    if(pwdField) pwdField.style.display = 'block';
    if(pwdInput) pwdInput.required = true;
    
    accountModal.classList.remove('hidden');
};

window.closeModal = () => {
    accountModal.classList.add('hidden');
};

// Main Load Function
async function loadAccounts() {
    if(!tbody) return;

    tbody.innerHTML = '';
    loadingState?.classList.remove('hidden');
    emptyState?.classList.add('hidden');

    try {
        accounts = await AccountService.getAll();
        loadingState?.classList.add('hidden');
        renderAccounts();
        
        // Init pagination
        if (window['accounts_initPagination']) {
            window['accounts_initPagination']();
        }
    } catch (error) {
        if(loadingState) loadingState.innerHTML = '<p class="text-red-500 text-center py-4">حدث خطأ أثناء تحميل البيانات</p>';
    }
}

function renderAccounts() {
    if (!tbody) return;

    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    const statusValue = statusFilter ? statusFilter.value : '';
    
    const filteredAccounts = accounts.filter(account => {
        const matchesSearch = (account.full_name.toLowerCase().includes(searchTerm) || 
                             account.username.toLowerCase().includes(searchTerm) || 
                             (account.email && account.email.toLowerCase().includes(searchTerm)));
        const matchesStatus = !statusValue || account.status?.code === statusValue;
        return matchesSearch && matchesStatus;
    });

    if (filteredAccounts.length === 0) {
        emptyState?.classList.remove('hidden');
        tbody.innerHTML = '';
        if (window['accounts_initPagination']) window['accounts_initPagination']();
        return;
    }

    emptyState?.classList.add('hidden');

    tbody.innerHTML = filteredAccounts.map(account => `
        <tr class="group">
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

    // Re-init pagination
    if (window['accounts_initPagination']) {
        window['accounts_initPagination']();
    }
}

function getStatusClass(code) {
    const classes = {
        'ACTIVE': 'bg-green-50 text-green-700',
        'PENDING': 'bg-yellow-50 text-yellow-700',
        'SUSPENDED': 'bg-red-50 text-red-700'
    };
    return classes[code] || 'bg-gray-50 text-gray-700';
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    if (searchInput) searchInput.addEventListener('input', renderAccounts);
    if (statusFilter) statusFilter.addEventListener('change', renderAccounts);
    if (accountForm) {
        accountForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = accountIdInput.value;
            
            const accountData = new Account({
                username: document.getElementById('username').value,
                full_name: document.getElementById('full_name').value,
                email: document.getElementById('email').value || null,
                phone: document.getElementById('phone').value,
                account_status_id: document.getElementById('account_status_id').value,
                account_type_id: document.getElementById('account_type_id').value
            });
            
            if (!id && pwdInput) {
                accountData.password = pwdInput.value;
            }
            
            try {
                if (id) await AccountService.update(id, accountData);
                else await AccountService.create(accountData);
                
                closeModal();
                loadAccounts();
            } catch (error) {
                alert('حدث خطأ: ' + (error.response?.data?.message || 'تأكد من صحة البيانات'));
            }
        });
    }

    // Initial Load
    loadAccounts();
});
