import { DOM } from '../../../core/utils/dom.js';
import { XssProtection } from '../../../core/security/XssProtection.js';

export class PlanView {
    constructor() {
        this.tbody = document.getElementById('plans-tbody');
        this.loadingState = document.getElementById('loading-state');
        this.emptyState = document.getElementById('empty-state');
        this.modal = document.getElementById('plan-modal');
        this.form = document.getElementById('plan-form');
        this.modalTitle = document.getElementById('modal-title');
        this.idInput = document.getElementById('plan-id');
        this.activeToggle = document.getElementById('is_active');
        this.detailsModal = document.getElementById('plan-details-modal');
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

    renderPlans(plans) {
        if (!this.tbody) return;

        if (plans.length === 0) {
            this.showEmpty();
            return;
        }

        this.hideEmpty();
        this.tbody.innerHTML = '';

        plans.forEach(plan => {
            const row = this.createPlanRow(plan);
            this.tbody.appendChild(row);
        });
        
        if (window['plans_initPagination']) {
            window['plans_initPagination']();
        }
    }

    createPlanRow(plan) {
        const tr = DOM.create('tr', { className: 'hover:bg-gray-50 transition-colors' });

        // Name & Description
        const nameCell = DOM.create('td', { className: 'px-6 py-4' });
        const nameDiv = DOM.create('div', { className: 'font-bold text-gray-900' });
        XssProtection.setTextContent(nameDiv, plan.name);
        
        const infoDiv = DOM.create('div', { className: 'text-xs text-gray-500 mt-1 max-w-xs' });
        let infoText = plan.description || '';
        if (plan.features && plan.features.length > 0) {
            const featuresText = plan.features.slice(0, 3).join(' • ');
            infoText += (infoText ? ' | ' : '') + featuresText + (plan.features.length > 3 ? '...' : '');
        }
        XssProtection.setTextContent(infoDiv, infoText);
        
        nameCell.appendChild(nameDiv);
        nameCell.appendChild(infoDiv);

        // Pricing
        const priceCell = DOM.create('td', { className: 'px-6 py-4' });
        const monthlyDiv = DOM.create('div', { className: 'text-sm font-bold text-gray-900' });
        XssProtection.setTextContent(monthlyDiv, `${plan.price_monthly} ريال / شهر`);
        const yearlyDiv = DOM.create('div', { className: 'text-xs text-gray-500' });
        XssProtection.setTextContent(yearlyDiv, `${plan.price_yearly} ريال / سنة`);
        priceCell.appendChild(monthlyDiv);
        priceCell.appendChild(yearlyDiv);

        // Status
        const statusCell = DOM.create('td', { className: 'px-6 py-4' });
        const statusBadge = DOM.create('span', {
            className: `px-2.5 py-1 rounded-lg text-xs font-bold ${plan.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}`
        });
        XssProtection.setTextContent(statusBadge, plan.is_active ? 'نشط' : 'غير نشط');
        statusCell.appendChild(statusBadge);

        // Storage
        const storageCell = DOM.create('td', { className: 'px-6 py-4' });
        const storageDiv = DOM.create('div', { className: 'text-sm font-bold text-gray-900' });
        const storageGB = Math.round((plan.storage_limit || 0) / (1024 * 1024 * 1024));
        XssProtection.setTextContent(storageDiv, `${storageGB} جيجابايت`);
        storageCell.appendChild(storageDiv);

        // Actions
        const actionsCell = DOM.create('td', { className: 'px-6 py-4 text-center' });
        const actionsDiv = DOM.create('div', { className: 'flex items-center justify-center gap-2' });

        const viewBtn = DOM.create('button', {
            className: 'p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors',
            title: 'عرض التفاصيل',
            onclick: () => window.viewPlan?.(plan.id)
        });
        viewBtn.appendChild(DOM.create('i', { className: 'fas fa-eye' }));

        const editBtn = DOM.create('button', {
            className: 'p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors',
            title: 'تعديل',
            onclick: () => window.editPlan?.(plan.id)
        });
        editBtn.appendChild(DOM.create('i', { className: 'fas fa-edit' }));

        const deleteBtn = DOM.create('button', {
            className: 'p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors',
            title: 'حذف',
            onclick: () => window.deletePlan?.(plan.id)
        });
        deleteBtn.appendChild(DOM.create('i', { className: 'fas fa-trash' }));

        actionsDiv.appendChild(viewBtn);
        actionsDiv.appendChild(editBtn);
        actionsDiv.appendChild(deleteBtn);
        actionsCell.appendChild(actionsDiv);

        tr.appendChild(nameCell);
        tr.appendChild(priceCell);
        tr.appendChild(storageCell);
        tr.appendChild(statusCell);
        tr.appendChild(actionsCell);

        return tr;
    }

    openModal(title) {
        if (this.modalTitle) this.modalTitle.textContent = title;
        this.modal?.classList.remove('hidden');
    }

    closeModal() {
        this.modal?.classList.add('hidden');
        this.form?.reset();
    }

    openDetailsModal(plan) {
        if (!this.detailsModal) return;

        XssProtection.setTextContent(document.getElementById('detail-name'), plan.name);
        XssProtection.setTextContent(document.getElementById('detail-price-monthly'), `${plan.price_monthly} ريال`);
        XssProtection.setTextContent(document.getElementById('detail-price-yearly'), `${plan.price_yearly} ريال`);
        
        const storageGB = Math.round((plan.storage_limit || 0) / (1024 * 1024 * 1024));
        XssProtection.setTextContent(document.getElementById('detail-storage'), `${storageGB} جيجابايت`);
        XssProtection.setTextContent(document.getElementById('detail-description'), plan.description || 'لا يوجد وصف');

        const statusEl = document.getElementById('detail-status');
        statusEl.textContent = plan.is_active ? 'نشط' : 'غير نشط';
        statusEl.className = `text-xs font-bold mt-1 ${plan.is_active ? 'text-green-600' : 'text-red-600'}`;

        const featuresList = document.getElementById('detail-features');
        featuresList.innerHTML = '';
        (plan.features || []).forEach(feature => {
            const li = DOM.create('li', { className: 'flex items-center gap-2 text-sm text-gray-600' });
            li.innerHTML = '<i class="fas fa-check-circle text-accent text-xs"></i>';
            const span = DOM.create('span');
            XssProtection.setTextContent(span, feature);
            li.appendChild(span);
            featuresList.appendChild(li);
        });

        const editBtn = document.getElementById('edit-from-details');
        if (editBtn) {
            editBtn.onclick = () => {
                this.closeDetailsModal();
                window.editPlan(plan.id);
            };
        }

        this.detailsModal.classList.remove('hidden');
    }

    closeDetailsModal() {
        this.detailsModal?.classList.add('hidden');
    }

    populateForm(plan) {
        if (!this.form) return;
        this.idInput.value = plan.id;
        document.getElementById('name').value = plan.name;
        document.getElementById('description').value = plan.description || '';
        document.getElementById('price_monthly').value = plan.price_monthly;
        document.getElementById('price_yearly').value = plan.price_yearly;
        document.getElementById('storage_limit').value = Math.round((plan.storage_limit || 0) / (1024 * 1024 * 1024));
        document.getElementById('features').value = (plan.features || []).join('\n');
        if (this.activeToggle) this.activeToggle.checked = plan.is_active;
    }
}

export default PlanView;
