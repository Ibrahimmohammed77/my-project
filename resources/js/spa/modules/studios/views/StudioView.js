/**
 * Studio View
 * Same pattern as AccountView: DOM rendering, XSS-safe, modal/form
 */

import { XssProtection } from '../../../core/security/XssProtection.js';
import { DOM } from '../../../core/utils/dom.js';

function getStatusClass(code) {
    const classes = {
        'ACTIVE': 'bg-green-50 text-green-600',
        'PENDING': 'bg-yellow-50 text-yellow-600',
        'SUSPENDED': 'bg-red-50 text-red-600'
    };
    return classes[code] || 'bg-gray-50 text-gray-600';
}

export class StudioView {
    constructor(options = {}) {
        this.tbody = options.tbody || DOM.query('#studios-tbody');
        this.loadingState = options.loadingState || DOM.query('#loading-state');
        this.emptyState = options.emptyState || DOM.query('#empty-state');
        this.modal = options.modal || DOM.query('#studio-modal');
        this.modalTitle = options.modalTitle || DOM.query('#studio-modal-title');
        this.form = options.form || DOM.query('#studio-form');
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

    render(studios) {
        if (!this.tbody) return;
        DOM.empty(this.tbody);

        if (!studios || studios.length === 0) {
            this.showEmpty();
            return;
        }
        this.hideEmpty();

        studios.forEach(studio => {
            this.tbody.appendChild(this.createStudioRow(studio));
        });
    }

    createStudioRow(studio) {
        const tr = DOM.create('tr', {
            className: 'hover:bg-gray-50/50 transition-colors duration-200 border-b border-gray-100 last:border-0'
        });

        const nameCell = DOM.create('td', { className: 'px-6 py-4' });
        const nameDiv = DOM.create('div', { className: 'flex items-center gap-3' });
        const avatar = DOM.create('div', {
            className: 'w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-700 font-bold text-sm uppercase shrink-0'
        });
        XssProtection.setTextContent(avatar, (studio.name || '').charAt(0) || '-');
        const infoDiv = DOM.create('div');
        const nameTitle = DOM.create('div', { className: 'text-sm font-bold text-gray-900' });
        XssProtection.setTextContent(nameTitle, studio.name || 'N/A');
        infoDiv.appendChild(nameTitle);
        nameDiv.appendChild(avatar);
        nameDiv.appendChild(infoDiv);
        nameCell.appendChild(nameDiv);

        const contactCell = DOM.create('td', { className: 'px-6 py-4' });
        const contactDiv = DOM.create('div', { className: 'flex flex-col gap-1 text-xs' });
        if (studio.email) {
            const emailRow = DOM.create('div', { className: 'flex items-center gap-2 text-gray-600' });
            emailRow.innerHTML = '<i class="fas fa-envelope text-gray-400 w-3"></i>';
            const emailSpan = DOM.create('span', { className: 'font-mono' });
            XssProtection.setTextContent(emailSpan, studio.email);
            emailRow.appendChild(emailSpan);
            contactDiv.appendChild(emailRow);
        }
        if (studio.phone) {
            const phoneRow = DOM.create('div', { className: 'flex items-center gap-2 text-gray-600' });
            phoneRow.innerHTML = '<i class="fas fa-phone text-gray-400 w-3"></i>';
            const phoneSpan = DOM.create('span', { className: 'font-mono' });
            XssProtection.setTextContent(phoneSpan, studio.phone);
            phoneRow.appendChild(phoneSpan);
            contactDiv.appendChild(phoneRow);
        }
        if (!studio.email && !studio.phone) {
            XssProtection.setTextContent(contactDiv, '-');
        }
        contactCell.appendChild(contactDiv);

        const statusCell = DOM.create('td', { className: 'px-6 py-4' });
        const statusCode = studio.status?.code || '';
        const statusBadge = DOM.create('span', {
            className: `inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold ${getStatusClass(statusCode)} border border-current/10`
        });
        statusBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-current"></span>';
        const statusText = DOM.create('span');
        XssProtection.setTextContent(statusText, studio.status?.name || 'غير محدد');
        statusBadge.appendChild(statusText);
        statusCell.appendChild(statusBadge);

        const actionsCell = DOM.create('td', { className: 'px-6 py-4 text-center' });
        const actionsDiv = DOM.create('div', { className: 'flex items-center justify-center gap-2' });
        const editBtn = DOM.create('button', {
            type: 'button',
            className: 'w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white transition-all duration-200 flex items-center justify-center shadow-sm',
            title: 'تعديل',
            dataset: { action: 'edit', studioId: studio.studio_id }
        });
        editBtn.innerHTML = '<i class="fas fa-pen text-xs"></i>';
        const deleteBtn = DOM.create('button', {
            type: 'button',
            className: 'w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-200 flex items-center justify-center shadow-sm',
            title: 'حذف',
            dataset: { action: 'delete', studioId: studio.studio_id }
        });
        deleteBtn.innerHTML = '<i class="fas fa-trash text-xs"></i>';
        actionsDiv.appendChild(editBtn);
        actionsDiv.appendChild(deleteBtn);
        actionsCell.appendChild(actionsDiv);

        tr.appendChild(nameCell);
        tr.appendChild(contactCell);
        tr.appendChild(statusCell);
        tr.appendChild(actionsCell);

        return tr;
    }

    openModal(title = 'إضافة استوديو جديد') {
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
        const credentialsSection = DOM.query('#credentials-section');
        if (credentialsSection) DOM.show(credentialsSection);
    }

    populateForm(studio) {
        if (!this.form) return;

        const fields = {
            'studio-id': studio.studio_id,
            'name': studio.name,
            'email': studio.email || '',
            'phone': studio.phone || '',
            'city': studio.city || '',
            'address': studio.address || '',
            'studio_status_id': studio.studio_status_id || ''
        };

        Object.entries(fields).forEach(([fieldId, value]) => {
            const field = document.getElementById(fieldId);
            if (field && value !== undefined && value !== null) {
                field.value = value;
            }
        });

        const credentialsSection = DOM.query('#credentials-section');
        if (credentialsSection) DOM.hide(credentialsSection);
    }

    clearForm() {
        if (!this.form) return;
        this.form.reset();
        const studioIdInput = document.getElementById('studio-id');
        if (studioIdInput) studioIdInput.value = '';
        const credentialsSection = DOM.query('#credentials-section');
        if (credentialsSection) DOM.show(credentialsSection);
    }

    disableForm() {
        if (this.form) DOM.disableForm(this.form);
    }

    enableForm() {
        if (this.form) DOM.enableForm(this.form);
    }
}

export default StudioView;
