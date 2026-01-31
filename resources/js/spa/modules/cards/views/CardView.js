import { DOM } from '../../../core/utils/dom.js';
import { XssProtection } from '../../../core/security/XssProtection.js';

export class CardView {
    constructor() {
        this.tbodyGroups = document.querySelector('#card-groups-table-tbody'); // Legacy
        this.tbodyCards = document.querySelector('#cards-tbody');
        
        // Main Modal (Cards or Groups)
        this.modal = document.getElementById('modal');
        this.modalTitle = document.getElementById('modal-title');
        this.form = document.getElementById('modal-form');
        
        this.loadingState = document.getElementById('loading-state');
        // empty-state might differ per view, but usually one #empty-state
        this.emptyState = document.getElementById('empty-state');

        // Sub-branches (Groups) Grid Container
        // This ID is now in groups/index.blade.php
        this.subBranchesGrid = document.getElementById('sub-branches-grid');
        this.subBranchesContainer = document.getElementById('sub-branches-container'); // Optional container wrapper
        
        // Group Modal (if separate)
        this.groupModal = document.getElementById('group-modal');
        this.groupModalTitle = this.groupModal?.querySelector('h3');
        this.groupForm = document.getElementById('group-modal-form');
    }

    renderSubBranches(groups) {
        if (!this.subBranchesGrid || !this.subBranchesContainer) return;

        if (!groups || groups.length === 0) {
            this.subBranchesContainer.classList.add('hidden');
            return;
        }

        this.subBranchesContainer.classList.remove('hidden');
        this.subBranchesGrid.innerHTML = groups.map(group => `
            <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow cursor-pointer group relative" onclick="window.location.href='/admin/cards/groups/${group.id || group.group_id}/cards'">
                <div class="flex items-start justify-between mb-3">
                    <div class="p-2 bg-accent/10 rounded-lg text-accent">
                        <i class="fas fa-folder text-xl"></i>
                    </div>
                    <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity" onclick="event.stopPropagation()">
                        <button onclick="editGroup(${group.id || group.group_id})" class="p-1 text-blue-600 hover:bg-blue-50 rounded" title="تعديل">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteGroup(${group.id || group.group_id})" class="p-1 text-red-600 hover:bg-red-50 rounded" title="حذف">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <h4 class="font-bold text-gray-800 mb-1 truncate" title="${XssProtection.escape(group.name)}">${XssProtection.escape(group.name)}</h4>
                <p class="text-xs text-gray-500 mb-3 line-clamp-2 h-8">${XssProtection.escape(group.description || 'لا يوجد وصف')}</p>
                
                <div class="flex items-center justify-between text-xs text-gray-400 border-t border-gray-50 pt-2">
                    <span>${group.available_cards || 0} كرت متاح</span>
                    <span>${group.sub_card_used || 0} مستخدم</span>
                </div>
            </div>
        `).join('');
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
        this.tbodyCards.innerHTML = cards.map(card => {
            let activationDate = '-';
            let expiryDate = '-';

            try {
                if (card.activation_date) {
                    const date = new Date(card.activation_date);
                    if (!isNaN(date.getTime())) {
                        activationDate = date.toLocaleDateString('ar-EG', { 
                            year: 'numeric', month: 'short', day: 'numeric', 
                            hour: '2-digit', minute: '2-digit' 
                        });
                    }
                }
                if (card.expiry_date) {
                    const date = new Date(card.expiry_date);
                    if (!isNaN(date.getTime())) {
                        expiryDate = date.toLocaleDateString('ar-EG', { 
                            year: 'numeric', month: 'short', day: 'numeric' 
                        });
                    }
                }
            } catch (e) {
                console.warn('Date formatting error:', e);
            }

            return `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-gray-900 font-mono">${XssProtection.escape(card.card_number)}</div>
                        <div class="text-[10px] text-gray-400 font-mono">${XssProtection.escape(card.card_uuid)}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">${XssProtection.escape(card.holder?.name || 'غير مخصص')}</div>
                        <div class="text-[10px] text-gray-500">${XssProtection.escape(card.holder?.phone || '')}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1.5">
                            <span class="text-xs font-semibold text-gray-700">${XssProtection.escape(card.type?.name || '-')}</span>
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg text-[10px] font-bold ${this.getStatusClass(card.status?.code)} border border-current/10 w-fit">
                                <span class="w-1 h-1 rounded-full bg-current"></span>
                                ${XssProtection.escape(card.status?.name || 'غير محدد')}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2 text-[10px]">
                                <span class="text-gray-400 w-12 italic text-left">التنشيط:</span>
                                <span class="text-gray-600 font-medium">${activationDate}</span>
                            </div>
                            <div class="flex items-center gap-2 text-[10px]">
                                <span class="text-gray-400 w-12 italic text-left">الانتهاء:</span>
                                <span class="text-red-500 font-bold">${expiryDate}</span>
                            </div>
                        </div>
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
            `;
        }).join('');
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
        
        // Prepare data for form (format dates for inputs)
        const formData = { ...data };
        
        if (formData.activation_date) {
            const date = new Date(formData.activation_date);
            // Format for datetime-local: YYYY-MM-DDTHH:MM
            formData.activation_date = date.toISOString().slice(0, 16);
        }
        
        if (formData.expiry_date) {
            const date = new Date(formData.expiry_date);
            // Format for date: YYYY-MM-DD
            formData.expiry_date = date.toISOString().split('T')[0];
        }

        DOM.setFormData(this.form, formData);
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

    // Group Modal Helpers
    openGroupModal(title) {
        if (!this.groupModal) return;
        if (this.groupModalTitle) this.groupModalTitle.textContent = title;
        this.groupModal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    closeGroupModal() {
        if (!this.groupModal) return;
        this.groupModal.classList.add('hidden');
        document.body.style.overflow = '';
        if (this.groupForm) {
            this.groupForm.reset();
            const idField = this.groupForm.querySelector('[name="id"]');
            if (idField) idField.value = '';
        }
    }
}

export default CardView;

