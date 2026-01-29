import { CardService } from '../services/CardService';

document.addEventListener('DOMContentLoaded', () => {
    loadCardGroups();
});

const tbody = document.querySelector('#card-groups-table-tbody');
const loadingState = document.getElementById('loading-state');
const emptyState = document.getElementById('empty-state');

async function loadCardGroups() {
    if (!tbody) return;
    tbody.innerHTML = '';
    loadingState?.classList.remove('hidden');
    emptyState?.classList.add('hidden');

    try {
        const groups = await CardService.getAllGroups();
        loadingState?.classList.add('hidden');
        renderGroups(groups);
    } catch (error) {
        if(loadingState) loadingState.innerHTML = '<p class="text-red-500 text-center py-4">حدث خطأ أثناء تحميل البيانات</p>';
    }
}

function renderGroups(groups) {
    if (!tbody) return;

    if (groups.length === 0) {
        emptyState?.classList.remove('hidden');
        tbody.innerHTML = '';
        return;
    }

    emptyState?.classList.add('hidden');
    tbody.innerHTML = groups.map(group => `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4">
                <div class="font-bold text-gray-900">${group.name}</div>
                <div class="text-xs text-gray-500">${group.description || ''}</div>
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
                    <button onclick="editGroup(${group.id})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}
