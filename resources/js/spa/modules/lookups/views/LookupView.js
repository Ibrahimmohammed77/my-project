import { DOM } from '../../../core/utils/dom.js';
import { XssProtection } from '../../../core/security/XssProtection.js';

export class LookupView {
    constructor() {
        this.tbody = document.getElementById('lookups-tbody');
        this.loadingState = document.getElementById('loading-state');
        this.emptyState = document.getElementById('empty-state');
        this.searchInput = document.getElementById('lookups-search');
        
        // Modal & Value Form Elements
        this.modal = document.getElementById('lookup-modal');
        this.modalTitle = document.getElementById('modal-title');
        this.valuesTbody = document.getElementById('values-tbody');
        this.valueForm = document.getElementById('value-form');
        this.valueIdInput = document.getElementById('value-id');
        this.masterIdInput = document.getElementById('lookup-master-id');
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
        if (this.tbody) this.tbody.innerHTML = '';
    }

    hideEmpty() {
        this.emptyState?.classList.add('hidden');
    }

    renderLookups(masters) {
        if (!this.tbody) return;
        if (masters.length === 0) {
            this.showEmpty();
            return;
        }
        this.hideEmpty();
        this.tbody.innerHTML = '';
        masters.forEach(master => {
            const row = this.createMasterRow(master);
            this.tbody.appendChild(row);
        });
        if (window['lookups_initPagination']) window['lookups_initPagination']();
    }

    createMasterRow(master) {
        const tr = DOM.create('tr', { className: 'hover:bg-gray-50 transition-colors group' });

        const nameCell = DOM.create('td', { className: 'px-6 py-4' });
        const nameDiv = DOM.create('div', { className: 'font-bold text-gray-900' });
        XssProtection.setTextContent(nameDiv, master.name);
        const codeDiv = DOM.create('div', { className: 'text-xs text-gray-500 font-mono' });
        XssProtection.setTextContent(codeDiv, master.code);
        nameCell.appendChild(nameDiv);
        nameCell.appendChild(codeDiv);

        const valuesCell = DOM.create('td', { className: 'px-6 py-4' });
        const valuesWrapper = DOM.create('div', { className: 'flex flex-wrap gap-1' });
        if (master.values && master.values.length > 0) {
            master.values.forEach(val => {
                const badge = DOM.create('span', { className: 'px-2 py-0.5 bg-gray-100 text-gray-600 rounded-lg text-xs border border-gray-200' });
                XssProtection.setTextContent(badge, val.name);
                valuesWrapper.appendChild(badge);
            });
        }
        valuesCell.appendChild(valuesWrapper);

        const actionsCell = DOM.create('td', { className: 'px-6 py-4 text-center' });
        const cogBtn = DOM.create('button', {
            className: 'text-blue-600 hover:text-blue-800 transition-colors p-2 rounded hover:bg-blue-50',
            title: 'إدارة القيم',
            onclick: () => window.editMaster?.(master.id)
        });
        cogBtn.appendChild(DOM.create('i', { className: 'fas fa-cog' }));
        actionsCell.appendChild(cogBtn);

        tr.appendChild(nameCell);
        tr.appendChild(valuesCell);
        tr.appendChild(actionsCell);
        return tr;
    }

    renderValuesTable(values) {
        if (!this.valuesTbody) return;
        this.valuesTbody.innerHTML = '';
        values.forEach(val => {
            const row = this.createValueRow(val);
            this.valuesTbody.appendChild(row);
        });
    }

    createValueRow(val) {
        const tr = DOM.create('tr');

        const nameCell = DOM.create('td', { className: 'px-6 py-4 whitespace-nowrap text-sm text-gray-900' });
        XssProtection.setTextContent(nameCell, val.name);

        const codeCell = DOM.create('td', { className: 'px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono' });
        XssProtection.setTextContent(codeCell, val.code);

        const actionsCell = DOM.create('td', { className: 'px-6 py-4 whitespace-nowrap text-right text-sm font-medium' });
        const editBtn = DOM.create('button', {
            className: 'text-indigo-600 hover:text-indigo-900 ml-2',
            onclick: () => window.editValue?.(val.id)
        });
        XssProtection.setTextContent(editBtn, 'تعديل');
        
        const deleteBtn = DOM.create('button', {
            className: 'text-red-600 hover:text-red-900',
            onclick: () => window.deleteValue?.(val.id)
        });
        XssProtection.setTextContent(deleteBtn, 'حذف');

        actionsCell.appendChild(editBtn);
        actionsCell.appendChild(deleteBtn);
        
        tr.appendChild(nameCell);
        tr.appendChild(codeCell);
        tr.appendChild(actionsCell);
        return tr;
    }

    openModal(title, masterId) {
        if (this.modalTitle) this.modalTitle.textContent = title;
        if (this.masterIdInput) this.masterIdInput.value = masterId;
        this.modal?.classList.remove('hidden');
    }

    closeModal() {
        this.modal?.classList.add('hidden');
        this.resetValueForm();
    }

    populateValueForm(val) {
        if (!this.valueForm) return;
        this.valueIdInput.value = val.id;
        document.getElementById('value-name').value = val.name;
        document.getElementById('value-code').value = val.code;
        document.getElementById('value-description').value = val.description || '';
    }

    resetValueForm() {
        this.valueForm?.reset();
        if (this.valueIdInput) this.valueIdInput.value = '';
    }
}

export default LookupView;
