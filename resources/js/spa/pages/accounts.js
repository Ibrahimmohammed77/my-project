console.log('Accounts JS Loaded: v3 (Bulletproof Fix)');
import '../../bootstrap';
import { Account } from '../models/Account';
import { AccountService } from '../services/AccountService';
import { saveAccount } from '../actions/accountActions';
import { showToast, showErrors, clearErrors } from '../utils/toast';

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
// derived inside functions for safety, but helpful to know it exists in DOM now

// Global Functions
// Global function to handle field visibility based on role or type code
window.updateConditionalFields = (roleName) => {
    // Hide all first
    const studioFields = document.getElementById('studio-fields');
    const schoolFields = document.getElementById('school-fields');
    const subscriberFields = document.getElementById('subscriber-fields');
    
    if(studioFields) studioFields.classList.add('hidden');
    if(schoolFields) schoolFields.classList.add('hidden');
    if(subscriberFields) subscriberFields.classList.add('hidden');
    
    // Show based on role name
    if (roleName === 'studio_owner' && studioFields) studioFields.classList.remove('hidden');
    else if (roleName === 'school_owner' && schoolFields) schoolFields.classList.remove('hidden');
    else if (roleName === 'customer' && subscriberFields) subscriberFields.classList.remove('hidden');
};

window.handleRoleChange = (selectElement) => {
    if (!selectElement || !selectElement.options) return;
    
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    if (!selectedOption) return;

    const roleName = selectedOption.getAttribute('data-role-name');
    if (!roleName) return;
    
    // Trigger visibility update directly from role
    window.updateConditionalFields(roleName);
};

// Internal Logic
async function loadAccounts() {
    if (loadingState) loadingState.classList.remove('hidden');
    if (tbody) tbody.innerHTML = '';
    
    try {
        accounts = await AccountService.getAll();
        renderAccounts();
    } catch (error) {
        console.error(error);
        showToast('خطأ في تحميل الحسابات', 'error');
    } finally {
        if (loadingState) loadingState.classList.add('hidden');
    }
}

function renderAccounts() {
    if (!tbody) return;
    
    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    const status = statusFilter ? statusFilter.value : '';
    
    const filtered = accounts.filter(account => {
        const matchesSearch = !searchTerm || 
            account.username.toLowerCase().includes(searchTerm) ||
            account.full_name.toLowerCase().includes(searchTerm) ||
            (account.email && account.email.toLowerCase().includes(searchTerm)) ||
            (account.phone && account.phone.includes(searchTerm));
            
        const matchesStatus = !status || (account.status && account.status.code === status);
        
        return matchesSearch && matchesStatus;
    });
    
    if (filtered.length === 0) {
        if (emptyState) emptyState.classList.remove('hidden');
        tbody.innerHTML = '';
        return;
    }
    
    if (emptyState) emptyState.classList.add('hidden');
    
    tbody.innerHTML = filtered.map(account => `
        <tr class="hover:bg-gray-50/50 transition-colors duration-200 border-b border-gray-100 last:border-0">
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-accent/10 flex items-center justify-center text-accent font-bold text-sm">
                        ${account.full_name ? account.full_name.substring(0, 1).toUpperCase() : '?'}
                    </div>
                    <div>
                        <div class="text-sm font-bold text-gray-900">${account.full_name}</div>
                        <div class="text-xs text-gray-500">@${account.username}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-envelope text-gray-400 w-4"></i>
                        <span>${account.email || '---'}</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="fas fa-phone text-gray-400 w-4"></i>
                        <span>${account.phone || '---'}</span>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4">
                <span class="px-3 py-1 rounded-full text-xs font-bold ${account.status?.code === 'ACTIVE' ? 'bg-green-50 text-green-600' : 'bg-orange-50 text-orange-600'}">
                    ${account.status?.name || 'مجهول'}
                </span>
            </td>
            <td class="px-6 py-4 text-sm text-gray-600">
                ${account.roles.map(r => r.name).join(', ')}
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-2">
                    <button onclick="editAccount(${account.account_id})" class="w-8 h-8 rounded-lg bg-orange-50 text-orange-500 hover:bg-orange-500 hover:text-white transition-all duration-200 flex items-center justify-center shadow-sm hover:shadow-orange-200" title="تعديل">
                        <i class="fas fa-edit text-xs"></i>
                    </button>
                    <button onclick="deleteAccount(${account.account_id})" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-200 flex items-center justify-center shadow-sm hover:shadow-red-200" title="حذف">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

window.closeModal = () => {
    if (accountModal) accountModal.classList.add('hidden');
    if (accountForm) accountForm.reset();
    accountIdInput.value = '';
    clearErrors();
};

window.deleteAccount = async (id) => {
    if (!confirm('هل أنت متأكد من رغبتك في حذف هذا الحساب؟')) return;
    
    try {
        await AccountService.delete(id);
        showToast('تم حذف الحساب بنجاح', 'success');
        loadAccounts();
    } catch (error) {
        console.error(error);
        showToast('حدث خطأ أثناء حذف الحساب', 'error');
    }
};

// Global exports for modal initialization
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

    const roleSelect = document.getElementById('role_id');
    if (account.role_id && roleSelect) {
        roleSelect.value = account.role_id;
    } else if (account.roles && account.roles.length > 0 && roleSelect) {
        roleSelect.value = account.roles[0].role_id;
    }
    
    // Trigger role change to ensure fields are shown correctly
    if (roleSelect) window.handleRoleChange(roleSelect);
    
    if(pwdField) pwdField.style.display = 'none';
    if(pwdInput) pwdInput.required = false;

    accountModal.classList.remove('hidden');
};

window.showCreateModal = () => {
    if(modalTitle) modalTitle.innerHTML = '<span class="w-2 h-6 bg-accent rounded-full"></span><span>إضافة حساب جديد</span>';
    
    accountForm.reset();
    accountIdInput.value = '';
    
    // Reset conditional fields visibility
    window.updateConditionalFields("");
    
    if(pwdField) pwdField.style.display = 'block';
    if(pwdInput) pwdInput.required = true;
    
    accountModal.classList.remove('hidden');
};

document.addEventListener('DOMContentLoaded', () => {
    if (searchInput) searchInput.addEventListener('input', renderAccounts);
    if (statusFilter) statusFilter.addEventListener('change', renderAccounts);
    
    const roleSelect = document.getElementById('role_id');
    if (roleSelect) {
        roleSelect.addEventListener('change', (e) => window.handleRoleChange(e.target));
    }

    if (accountForm) {
        accountForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            clearErrors(); // Clear previous errors
            
            try {
                const id = accountIdInput.value;
                const roleSelect = document.getElementById('role_id');
                let selectedRoleOption = null;
                if (roleSelect && roleSelect.options && roleSelect.selectedIndex !== -1) {
                    selectedRoleOption = roleSelect.options[roleSelect.selectedIndex];
                }
                const roleName = selectedRoleOption ? (selectedRoleOption.dataset.roleName || '') : '';
                
                // Basic Data
                const accountData = new Account({
                    username: document.getElementById('username').value,
                    full_name: document.getElementById('full_name').value,
                    email: document.getElementById('email').value || null,
                    phone: document.getElementById('phone').value,
                    account_status_id: document.getElementById('account_status_id').value,
                    role_id: roleSelect ? roleSelect.value : null
                });

                // Add Extra Data (Backend now handles Mapping, but we send school detail fields)
                if (roleName === 'school_owner') {
                    accountData.school_type_id = document.getElementById('school_type_id').value;
                    accountData.school_level_id = document.getElementById('school_level_id').value;
                }
                
                const password = pwdInput ? pwdInput.value : null;
                const passwordConfirmation = password; // Auto-confirm since we removed confirmation field

                await saveAccount(id, accountData, password, passwordConfirmation);
                
                closeModal();
                loadAccounts();
                showToast(id ? 'تم تحديث الحساب بنجاح' : 'تم إنشاء الحساب بنجاح', 'success');
            } catch (error) {
                console.error(error);
                if (error.response && error.response.status === 422) {
                    showErrors(error.response.data.errors);
                    showToast('يرجى التحقق من البيانات المدخلة', 'error');
                } else {
                    showToast('حدث خطأ: ' + (error.response?.data?.message || error.message || 'خطأ غير معروف'), 'error');
                }
            }
        });
    }

    // Initial Load
    loadAccounts();
});

