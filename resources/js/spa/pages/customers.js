import { Customer } from '../models/Customer';
import { CustomerService } from '../services/CustomerService';

let customers = [];

// DOM Elements
const tbody = document.getElementById('customers-tbody');
const loadingState = document.getElementById('loading-state');
const emptyState = document.getElementById('empty-state');
const searchInput = document.getElementById('search');
const customerModal = document.getElementById('customer-modal');
const customerForm = document.getElementById('customer-form');
const modalTitle = document.getElementById('modal-title');
const customerIdInput = document.getElementById('customer-id');

// Global Functions
window.editCustomer = async (id) => {
    const customer = customers.find(c => c.customer_id === id);
    if (!customer) return;
    
    if(modalTitle) modalTitle.innerHTML = '<span class="w-2 h-6 bg-orange-500 rounded-full"></span><span>تعديل عميل</span>';
    
    customerIdInput.value = customer.customer_id;
    document.getElementById('first_name').value = customer.first_name;
    document.getElementById('last_name').value = customer.last_name;
    document.getElementById('email').value = customer.email;
    document.getElementById('phone').value = customer.phone;
    
    // Format date for input type="date"
    if (customer.date_of_birth) {
        const date = new Date(customer.date_of_birth);
        const yyyy = date.getFullYear();
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const dd = String(date.getDate()).padStart(2, '0');
        document.getElementById('date_of_birth').value = `${yyyy}-${mm}-${dd}`;
    } else {
        document.getElementById('date_of_birth').value = '';
    }
    
    document.getElementById('gender_id').value = customer.gender_id || '';
    
    // For account_id, if we want to change it. 
    // Assuming logged in user context or management view, field might be hidden or populated.
    // For now, let's look for field but it might not be editable easily without list of accounts.
    // Assuming backend validates.
    if(document.getElementById('account_id')) {
        document.getElementById('account_id').value = customer.account_id || '';
    }

    customerModal.classList.remove('hidden');
};

window.deleteCustomer = async (id) => {
    if (!confirm('هل أنت متأكد من حذف هذا العميل؟')) return;
    try {
        await CustomerService.delete(id);
        loadCustomers();
    } catch (error) {
        alert('حدث خطأ: ' + (error.response?.data?.message || error.message));
    }
};

window.showCreateModal = () => {
    if(modalTitle) modalTitle.innerHTML = '<span class="w-2 h-6 bg-accent rounded-full"></span><span>إضافة عميل جديد</span>';
    
    customerForm.reset();
    customerIdInput.value = '';
    
    customerModal.classList.remove('hidden');
};

window.closeModal = () => {
    customerModal.classList.add('hidden');
};

// Main Load Function
async function loadCustomers() {
    if(!tbody) return;

    tbody.innerHTML = '';
    loadingState?.classList.remove('hidden');
    emptyState?.classList.add('hidden');

    try {
        customers = await CustomerService.getAll();
        loadingState?.classList.add('hidden');
        renderCustomers();
    } catch (error) {
        if(loadingState) loadingState.innerHTML = '<p class="text-red-500 text-center py-4">حدث خطأ أثناء تحميل البيانات</p>';
    }
}

function renderCustomers() {
    if (!tbody) return;

    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    
    const filteredCustomers = customers.filter(customer => 
        customer.first_name.toLowerCase().includes(searchTerm) || 
        customer.last_name.toLowerCase().includes(searchTerm) || 
        customer.email.toLowerCase().includes(searchTerm) ||
        customer.phone.includes(searchTerm)
    );

    if (filteredCustomers.length === 0) {
        emptyState?.classList.remove('hidden');
        tbody.innerHTML = '';
        return;
    }

    emptyState?.classList.add('hidden');

    tbody.innerHTML = filteredCustomers.map(customer => `
        <tr class="hover:bg-gray-50/80 transition-colors group">
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm uppercase shrink-0">
                        ${customer.first_name.charAt(0)}${customer.last_name.charAt(0)}
                    </div>
                    <div>
                        <div class="font-bold text-gray-900 text-sm">${customer.first_name} ${customer.last_name}</div>
                        <div class="text-xs text-gray-500 font-mono">ID: ${customer.customer_id}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="flex flex-col gap-1 text-xs">
                    <div class="flex items-center gap-2 text-gray-600">
                        <i class="fas fa-envelope text-gray-400 w-3"></i>
                        <span class="font-mono">${customer.email}</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <i class="fas fa-phone text-gray-400 w-3"></i>
                        <span class="font-mono">${customer.phone}</span>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4">
                 <div class="text-xs text-gray-600">
                    ${customer.gender ? `<span class="inline-flex items-center gap-1"><i class="fas fa-venus-mars text-gray-400"></i> ${customer.gender.name}</span>` : '-'}
                 </div>
                 <div class="text-[11px] text-gray-400 mt-1">
                    ${customer.date_of_birth ? new Date(customer.date_of_birth).toLocaleDateString('ar-EG') : '-'}
                 </div>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                    <button onclick="editCustomer(${customer.customer_id})" class="h-9 w-9 rounded-xl bg-white border border-gray-200 text-blue-600 hover:bg-blue-50 hover:border-blue-200 flex items-center justify-center transition-all shadow-sm hover:shadow-md" title="تعديل">
                        <i class="fas fa-pen text-xs"></i>
                    </button>
                    <button onclick="deleteCustomer(${customer.customer_id})" class="h-9 w-9 rounded-xl bg-white border border-gray-200 text-red-600 hover:bg-red-50 hover:border-red-200 flex items-center justify-center transition-all shadow-sm hover:shadow-md" title="حذف">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    if (searchInput) searchInput.addEventListener('input', renderCustomers);
    
    if (customerForm) {
        customerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = customerIdInput.value;
            
            // Assuming account_id is handled (e.g. either sent from form or managed by backend/auth context)
            // But validation requires it. For now let's assume hidden field or default.
            
            const customerData = new Customer({
                first_name: document.getElementById('first_name').value,
                last_name: document.getElementById('last_name').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                date_of_birth: document.getElementById('date_of_birth').value,
                gender_id: document.getElementById('gender_id').value,
                account_id: document.getElementById('account_id')?.value || '' // Ensure this exists or is handled
            });
            
            try {
                if (id) await CustomerService.update(id, customerData);
                else await CustomerService.create(customerData);
                
                closeModal();
                loadCustomers();
            } catch (error) {
                alert('حدث خطأ: ' + (error.response?.data?.message || 'تأكد من صحة البيانات'));
            }
        });
    }

    // Initial Load
    loadCustomers();
});
