export class CustomerView {
    constructor() {
        this.tableBody = document.querySelector('#customers-table tbody');
        this.searchField = document.getElementById('search');
    }

    bindSearch(handler) {
        if (!this.searchField) return;
        this.searchField.addEventListener('input', (e) => handler(e.target.value));
    }

    renderTable(customers) {
        if (!this.tableBody) return;
        
        this.tableBody.innerHTML = '';
        
        if (customers.length === 0) {
            this.renderEmpty();
            return;
        }

        customers.forEach(customer => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50/50 transition-colors border-b border-gray-100';
            
            tr.innerHTML = `
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-sm">
                            ${customer.name ? customer.name.charAt(0) : 'U'}
                        </div>
                        <div>
                            <p class="font-bold text-gray-700"></p>
                            <p class="text-[10px] text-gray-400 font-mono"></p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600 font-mono"></td>
                <td class="px-6 py-4 text-sm text-gray-600"></td>
                <td class="px-6 py-4">
                    <span class="px-2 py-0.5 bg-accent/5 text-accent border border-accent/10 rounded text-[10px] font-bold">
                         ${customer.cards_count || 0} كروت
                    </span>
                </td>
                <td class="px-6 py-4 text-xs text-gray-500">${new Date(customer.created_at).toLocaleDateString('ar-EG')}</td>
                <td class="px-6 py-4">
                    <div class="flex items-center justify-center gap-2">
                        <button class="view-btn w-8 h-8 rounded-lg bg-gray-50 text-gray-500 hover:text-accent hover:bg-accent/5 transition-all flex items-center justify-center" title="عرض التفاصيل">
                            <i class="fas fa-eye text-xs"></i>
                        </button>
                    </div>
                </td>
            `;

            // Safe Text Insertion
            tr.querySelector('div > p:nth-child(1)').textContent = customer.name;
            tr.querySelector('div > p:nth-child(2)').textContent = '@' + (customer.username || '-');
            tr.children[1].textContent = customer.email;
            tr.children[2].textContent = customer.phone || 'غير متوفر';

            // Events
            tr.querySelector('.view-btn').addEventListener('click', () => {
                // Feature coming soon
                import('../../../../utils/toast.js').then(({ showToast }) => showToast('سيتم إضافة تفاصيل العميل قريباً', 'info'));
            });

            this.tableBody.appendChild(tr);
        });
    }

    renderEmpty() {
        this.tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="py-12 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                        <i class="fas fa-users text-3xl mb-3 text-gray-300"></i>
                        <p>لا يوجد عملاء حالياً</p>
                    </div>
                </td>
            </tr>
        `;
    }
}

