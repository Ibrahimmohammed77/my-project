import { DOM } from '../../../core/utils/dom.js';
import { XssProtection } from '../../../core/security/XssProtection.js';

export class CardView {
    constructor() {
        this.tbodyGroups = document.querySelector('#card-groups-table-tbody');
        this.tbodyCards = document.querySelector('#cards-tbody');
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
            this.emptyState?.classList.remove('hidden');
            return;
        }
        this.tbodyGroups.innerHTML = groups.map(group => `
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">
                    <div class="font-bold text-gray-900">${XssProtection.escape(group.name)}</div>
                    <div class="text-xs text-gray-500">${XssProtection.escape(group.description || '')}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">المتاح: ${group.available_cards}</div>
                    <div class="text-xs text-gray-500">المستخدم: ${group.sub_card_used} / ${group.sub_card_available}</div>
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="flex justify-center gap-2">
                        <a href="/admin/cards/groups/${group.id}/cards" class="p-2 text-accent hover:bg-accent/5 rounded-lg transition-colors" title="عرض الكروت">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    renderCards(cards) {
        if (!this.tbodyCards) return;
        if (cards.length === 0) {
            this.emptyState?.classList.remove('hidden');
            return;
        }
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
                        <button onclick="window.editCard?.(${card.id})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                            <i class="fas fa-edit text-xs"></i>
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
}

export default CardView;
