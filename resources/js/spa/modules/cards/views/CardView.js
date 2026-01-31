import { DOM } from '../../../core/utils/dom.js';
import { XssProtection } from '../../../core/security/XssProtection.js';

export class CardView {
    constructor() {
        this.tbodyGroups = document.querySelector('#card-groups-table-tbody');
        this.tbodyCards = document.querySelector('#cards-tbody');
        
        this.modal = document.getElementById('modal');
        this.modalTitle = document.getElementById('modal-title');
        this.form = document.getElementById('modal-form');
        
        this.loadingState = document.getElementById('loading-state');
        this.emptyState = document.getElementById('empty-state');
    }

    showLoading() {
        const body = this.tbodyGroups || this.tbodyCards;
        if (!body) return;
        body.innerHTML = '';
        this.loadingState?.classList.remove('hidden');
        this.emptyState?.classList.add('hidden');
    }

    hideLoading() {
        this.loadingState?.classList.add('hidden');
    }

    renderGroups(groups) {
        if (!this.tbodyGroups) return;
        
        if (groups.length === 0) {
            this.tbodyGroups.innerHTML = '';
            this.emptyState?.classList.remove('hidden');
            return;
        }
        
        this.emptyState?.classList.add('hidden');
        this.tbodyGroups.innerHTML = groups.map(group => `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">
                    <div class="font-bold text-gray-900">${XssProtection.escape(group.name)}</div>
                    <div class="text-xs text-gray-500">${XssProtection.escape(group.description || '')}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">المتاح: ${group.available_cards || 0}</div>
                    <div class="text-xs text-gray-500">تم الاستهلاك: ${group.sub_card_used || 0}</div>
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="flex justify-center gap-2">
                        <a href="/admin/cards/groups/${group.id || group.group_id}/cards" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="عرض الكروت">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button onclick="editGroup(${group.id || group.group_id})" class="p-2 text-accent hover:bg-accent/5 rounded-lg transition-colors" title="تعديل">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteGroup(${group.id || group.group_id})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="حذف">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    renderCards(cards) {
        if (!this.tbodyCards) return;
        
        if (cards.length === 0) {
            this.tbodyCards.innerHTML = '';
            this.emptyState?.classList.remove('hidden');
            return;
        }
        
        this.emptyState?.classList.add('hidden');
        this.tbodyCards.innerHTML = cards.map(card => `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">
                    <div class="font-bold text-gray-900 font-mono">${XssProtection.escape(card.card_number)}</div>
                    <div class="text-[10px] text-gray-400 font-mono">${XssProtection.escape(card.card_uuid)}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-semibold text-gray-700">${XssProtection.escape(card.type?.name || '-')}</span>
                        <span class="text-[10px] text-gray-500">${XssProtection.escape(card.holder?.name || 'غير مخصص')}</span>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-[10px] font-bold ${this.getStatusClass(card.status?.code)} border border-current/10">
                        <span class="w-1 h-1 rounded-full bg-current"></span>
                        ${XssProtection.escape(card.status?.name || 'غير محدد')}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="flex justify-center gap-2">
                        <button onclick="editCard(${card.id || card.card_id})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="تعديل">
                            <i class="fas fa-edit text-xs"></i>
                        </button>
                        <button onclick="deleteCard(${card.id || card.card_id})" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="حذف">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    getStatusClass(code) {
        const classes = {
            'ACTIVE': 'bg-green-50 text-green-700',
            'INACTIVE': 'bg-gray-50 text-gray-700',
            'SUSPENDED': 'bg-red-50 text-red-700',
            'PENDING': 'bg-yellow-50 text-yellow-700'
        };
        return classes[code] || 'bg-gray-50 text-gray-700';
    }

    openModal(title) {
        if (!this.modal) return;
        if (this.modalTitle) this.modalTitle.textContent = title;
        this.modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    closeModal() {
        if (!this.modal) return;
        this.modal.classList.add('hidden');
        document.body.style.overflow = '';
        this.clearForm();
    }

    populateForm(data) {
        if (!this.form) return;
        DOM.setFormData(this.form, data);
    }

    clearForm() {
        if (!this.form) return;
        this.form.reset();
        const idField = this.form.querySelector('[name="id"]');
        if (idField) idField.value = '';
    }

    disableForm() {
        if (!this.form) return;
        DOM.disableForm(this.form);
    }

    enableForm() {
        if (!this.form) return;
        DOM.enableForm(this.form);
    }
}

export default CardView;

