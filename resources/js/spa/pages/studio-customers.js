import { StudioCustomerService } from '../services/StudioCustomerService';
import { Toast } from '../components/Toast';

class StudioCustomersPage {
    constructor() {
        this.customers = [];
        this.tableBody = document.querySelector('#customers-table tbody');
        this.searchField = document.getElementById('search');
        
        this.init();
    }

    async init() {
        if (this.searchField) {
            this.searchField.addEventListener('input', (e) => this.handleSearch(e));
        }
        await this.loadCustomers();
    }

    async loadCustomers() {
        try {
            this.renderLoading();
            this.customers = await StudioCustomerService.getAll();
            this.renderCustomers(this.customers);
        } catch (error) {
            Toast.error('خطأ في تحميل العملاء');
        }
    }

    renderLoading() {
        if (this.tableBody) {
            this.tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="py-10 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <i class="fas fa-spinner fa-spin text-accent text-2xl"></i>
                            <span class="text-sm text-gray-500">جاري تحميل العملاء...</span>
                        </div>
                    </td>
                </tr>
            `;
        }
    }

    renderEmpty() {
        if (this.tableBody) {
            this.tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="py-20 text-center">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-gray-300">
                            <i class="fa-solid fa-users text-gray-300 text-3xl"></i>
                        </div>
                        <h4 class="text-gray-800 font-bold">لا يوجد عملاء حالياً</h4>
                        <p class="text-gray-500 text-sm mt-1">لم يتم تسجيل أي عملاء تابعين للاستوديو بعد</p>
                    </td>
                </tr>
            `;
        }
    }

    renderCustomers(customers) {
        if (!this.tableBody) return;
        
        if (customers.length === 0) {
            this.renderEmpty();
            return;
        }

        this.tableBody.innerHTML = customers.map(customer => `
            <tr class="hover:bg-gray-50/50 transition-colors group">
                <td class="py-4 px-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-accent/10 flex items-center justify-center text-accent font-bold">
                            ${customer.full_name ? customer.full_name.substring(0, 1) : '?'}
                        </div>
                        <span class="font-bold text-gray-800">${customer.full_name || customer.username}</span>
                    </div>
                </td>
                <td class="py-4 px-4 text-sm text-gray-600">${customer.email}</td>
                <td class="py-4 px-4 text-sm text-gray-600">${customer.phone || '-'}</td>
                <td class="py-4 px-4 text-sm text-gray-500">${new Date(customer.created_at).toLocaleDateString('ar-EG')}</td>
                <td class="py-4 px-4">
                    <span class="text-[10px] font-bold px-2 py-1 rounded-full bg-green-50 text-green-600 border border-green-100">نشط</span>
                </td>
                <td class="py-4 px-4">
                    <div class="flex gap-2">
                        <button class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-accent hover:border-accent transition-all flex items-center justify-center shadow-soft">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    handleSearch(e) {
        const query = e.target.value.toLowerCase();
        const filtered = this.customers.filter(c => 
            (c.full_name && c.full_name.toLowerCase().includes(query)) || 
            c.username.toLowerCase().includes(query) || 
            c.email.toLowerCase().includes(query)
        );
        this.renderCustomers(filtered);
    }
}

new StudioCustomersPage();
