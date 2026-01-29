import { CardService } from '../services/CardService';

document.addEventListener('DOMContentLoaded', () => {
    const groupId = document.getElementById('group-id')?.value;
    if (groupId) {
        loadGroupCards(groupId);
    }
});

const tbody = document.querySelector('#cards-tbody');
const loadingState = document.getElementById('loading-state');
const emptyState = document.getElementById('empty-state');

async function loadGroupCards(groupId) {
    if (!tbody) return;
    tbody.innerHTML = '';
    loadingState?.classList.remove('hidden');
    emptyState?.classList.add('hidden');

    try {
        const cards = await CardService.getGroupCards(groupId);
        loadingState?.classList.add('hidden');
        renderCards(cards);
    } catch (error) {
        if(loadingState) loadingState.innerHTML = '<p class="text-red-500 text-center py-4">حدث خطأ أثناء تحميل البيانات</p>';
    }
}

function renderCards(cards) {
    if (!tbody) return;

    if (cards.length === 0) {
        emptyState?.classList.remove('hidden');
        tbody.innerHTML = '';
        return;
    }

    emptyState?.classList.add('hidden');
    tbody.innerHTML = cards.map(card => `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4">
                <div class="font-bold text-gray-900 font-mono">${card.number}</div>
                <div class="text-[10px] text-gray-400 font-mono">${card.uuid}</div>
            </td>
            <td class="px-6 py-4">
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-semibold text-gray-700">${card.type?.name || '-'}</span>
                    <span class="text-[10px] text-gray-500">${card.holder?.full_name || 'غير مخصص'}</span>
                </div>
            </td>
            <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-[10px] font-bold ${getStatusClass(card.status?.code)} border border-current/10">
                    <span class="w-1 h-1 rounded-full bg-current"></span>
                    ${card.status?.name || 'غير محدد'}
                </span>
            </td>
            <td class="px-6 py-4 text-center">
                <div class="flex justify-center gap-2">
                    <button onclick="editCard(${card.id})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                        <i class="fas fa-edit text-xs"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

function getStatusClass(code) {
    const classes = {
        'ACTIVE': 'bg-green-50 text-green-700',
        'INACTIVE': 'bg-gray-50 text-gray-700',
        'SUSPENDED': 'bg-red-50 text-red-700',
        'PENDING': 'bg-yellow-50 text-yellow-700'
    };
    return classes[code] || 'bg-gray-50 text-gray-700';
}
