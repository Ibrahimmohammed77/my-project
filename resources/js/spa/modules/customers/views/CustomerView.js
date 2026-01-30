import { DOM } from '../../../core/utils/dom.js';
import { XssProtection } from '../../../core/security/XssProtection.js';

export class CustomerView {
    constructor() {
        this.tbody = document.getElementById('customers-tbody');
        this.loadingState = document.getElementById('loading-state');
        this.emptyState = document.getElementById('empty-state');
    }

    showLoading() {
        if (!this.tbody) return;
        this.tbody.innerHTML = '';
        this.loadingState?.classList.remove('hidden');
        this.emptyState?.classList.add('hidden');
    }

    hideLoading() {
        this.loadingState?.classList.add('hidden');
    }

    showEmpty() {
        this.emptyState?.classList.remove('hidden');
    }

    renderCustomers(customers) {
        if (!this.tbody) return;
        if (customers.length === 0) {
            this.showEmpty();
            return;
        }
        this.emptyState?.classList.add('hidden');
        this.tbody.innerHTML = '';
        customers.forEach(customer => {
            const row = this.createCustomerRow(customer);
            this.tbody.appendChild(row);
        });
        if (window['customers_initPagination']) window['customers_initPagination']();
    }

    createCustomerRow(customer) {
        const tr = DOM.create('tr', { className: 'hover:bg-gray-50 transition-colors' });

        const nameCell = DOM.create('td', { className: 'px-6 py-4' });
        const nameDiv = DOM.create('div', { className: 'font-bold text-gray-900' });
        XssProtection.setTextContent(nameDiv, customer.fullName);
        const emailDiv = DOM.create('div', { className: 'text-xs text-gray-500' });
        XssProtection.setTextContent(emailDiv, customer.user?.email || '');
        nameCell.appendChild(nameDiv);
        nameCell.appendChild(emailDiv);

        const phoneCell = DOM.create('td', { className: 'px-6 py-4 text-sm text-gray-600' });
        XssProtection.setTextContent(phoneCell, customer.user?.phone || '-');

        const genderCell = DOM.create('td', { className: 'px-6 py-4 text-sm text-gray-600' });
        XssProtection.setTextContent(genderCell, customer.gender?.name || '-');

        const actionsCell = DOM.create('td', { className: 'px-6 py-4 text-center' });
        const viewBtn = DOM.create('a', {
            className: 'p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors inline-block',
            href: `/admin/customers/${customer.id}`,
            title: 'عرض'
        });
        viewBtn.appendChild(DOM.create('i', { className: 'fas fa-eye' }));
        actionsCell.appendChild(viewBtn);

        tr.appendChild(nameCell);
        tr.appendChild(phoneCell);
        tr.appendChild(genderCell);
        tr.appendChild(actionsCell);
        return tr;
    }
}

export default CustomerView;
