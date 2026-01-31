/**
 * Subscriber View
 * Same pattern as AccountView: DOM rendering, XSS-safe, modal/form
 */

import { XssProtection } from '../../../core/security/XssProtection.js';
import { DOM } from '../../../core/utils/dom.js';

export class SubscriberView {
    constructor(options = {}) {
        this.tbody = options.tbody || DOM.query('#subscribers-tbody');
        this.loadingState = options.loadingState || DOM.query('#loading-state');
        this.emptyState = options.emptyState || DOM.query('#empty-state');
        this.modal = options.modal || DOM.query('#subscriber-modal');
        this.modalTitle = options.modalTitle || DOM.query('#subscriber-modal-title');
        this.form = options.form || DOM.query('#subscriber-form');
    }

    showLoading() {
        DOM.show(this.loadingState);
        if (this.tbody) DOM.empty(this.tbody);
    }

    hideLoading() {
        DOM.hide(this.loadingState);
    }

    showEmpty() {
        DOM.show(this.emptyState);
        DOM.hide(this.tbody?.parentElement);
    }

    hideEmpty() {
        DOM.hide(this.emptyState);
        DOM.show(this.tbody?.parentElement);
    }

    render(subscribers) {
        if (!this.tbody) return;
        DOM.empty(this.tbody);

        if (!subscribers || subscribers.length === 0) {
            this.showEmpty();
            return;
        }
        this.hideEmpty();

        subscribers.forEach(subscriber => {
            this.tbody.appendChild(this.createSubscriberRow(subscriber));
        });
    }

    createSubscriberRow(subscriber) {
        const tr = DOM.create('tr', {
            className: 'hover:bg-gray-50/50 transition-colors duration-200 border-b border-gray-100 last:border-0'
        });

        const accountCell = DOM.create('td', { className: 'px-6 py-4' });
        const accountDiv = DOM.create('div', { className: 'flex items-center gap-3' });
        const avatar = DOM.create('div', {
            className: 'w-10 h-10 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm uppercase shrink-0'
        });
        avatar.innerHTML = '<i class="fas fa-user"></i>';
        const infoDiv = DOM.create('div');
        const nameDiv = DOM.create('div', { className: 'text-sm font-bold text-gray-900' });
        XssProtection.setTextContent(nameDiv, subscriber.account ? subscriber.account.full_name : 'N/A');
        const usernameDiv = DOM.create('div', { className: 'text-xs text-gray-500 font-mono' });
        XssProtection.setTextContent(usernameDiv, subscriber.account ? `@${subscriber.account.username || ''}` : '-');
        infoDiv.appendChild(nameDiv);
        infoDiv.appendChild(usernameDiv);
        accountDiv.appendChild(avatar);
        accountDiv.appendChild(infoDiv);
        accountCell.appendChild(accountDiv);

        const idCell = DOM.create('td', { className: 'px-6 py-4 text-xs font-mono text-gray-500' });
        XssProtection.setTextContent(idCell, subscriber.subscriber_id != null ? String(subscriber.subscriber_id) : '-');

        const statusCell = DOM.create('td', { className: 'px-6 py-4' });
        const code = subscriber.status?.code || '';
        const statusClass = code === 'ACTIVE' ? 'bg-green-50 text-green-600 border-green-100' :
            (code === 'INACTIVE' ? 'bg-red-50 text-red-600 border-red-100' : 'bg-gray-50 text-gray-600 border-gray-100');
        const statusBadge = DOM.create('span', {
            className: `inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium border ${statusClass}`
        });
        statusBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-current"></span>';
        const statusText = DOM.create('span');
        XssProtection.setTextContent(statusText, subscriber.status ? subscriber.status.name : '-');
        statusBadge.appendChild(statusText);
        statusCell.appendChild(statusBadge);

        const actionsCell = DOM.create('td', { className: 'px-6 py-4 text-center' });
        const actionsDiv = DOM.create('div', { className: 'flex items-center justify-center gap-2' });
        const editBtn = DOM.create('button', {
            type: 'button',
            className: 'w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white transition-all duration-200 flex items-center justify-center shadow-sm',
            title: 'تعديل',
            dataset: { action: 'edit', subscriberId: subscriber.subscriber_id }
        });
        editBtn.innerHTML = '<i class="fas fa-pen text-xs"></i>';
        const deleteBtn = DOM.create('button', {
            type: 'button',
            className: 'w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-200 flex items-center justify-center shadow-sm',
            title: 'حذف',
            dataset: { action: 'delete', subscriberId: subscriber.subscriber_id }
        });
        deleteBtn.innerHTML = '<i class="fas fa-trash text-xs"></i>';
        actionsDiv.appendChild(editBtn);
        actionsDiv.appendChild(deleteBtn);
        actionsCell.appendChild(actionsDiv);

        tr.appendChild(accountCell);
        tr.appendChild(idCell);
        tr.appendChild(statusCell);
        tr.appendChild(actionsCell);

        return tr;
    }

    openModal(title = 'إضافة مشترك جديد') {
        if (!this.modal) return;
        if (this.modalTitle) {
            this.modalTitle.innerHTML = '<span class="w-2 h-6 bg-accent rounded-full"></span><span>' + (title || '') + '</span>';
        }
        DOM.removeClass(this.modal, 'hidden');
    }

    closeModal() {
        if (!this.modal) return;
        DOM.addClass(this.modal, 'hidden');
        if (this.form) this.form.reset();
    }

    populateForm(subscriber) {
        if (!this.form) return;

        const accountIdEl = document.getElementById('account_id');
        const statusIdEl = document.getElementById('subscriber_status_id');
        const subscriberIdEl = document.getElementById('subscriber-id');

        if (subscriberIdEl) subscriberIdEl.value = subscriber.subscriber_id || '';
        if (accountIdEl) accountIdEl.value = subscriber.account_id || '';
        if (statusIdEl) statusIdEl.value = subscriber.subscriber_status_id || '';
    }

    clearForm() {
        if (!this.form) return;
        this.form.reset();
        const subscriberIdInput = document.getElementById('subscriber-id');
        if (subscriberIdInput) subscriberIdInput.value = '';
    }

    disableForm() {
        if (this.form) DOM.disableForm(this.form);
    }

    enableForm() {
        if (this.form) DOM.enableForm(this.form);
    }
}

export default SubscriberView;
