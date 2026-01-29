import { LookupService } from '../services/LookupService';

document.addEventListener('DOMContentLoaded', () => {
    loadLookups();
});

const tbody = document.querySelector('#lookups-tbody');
const loadingState = document.getElementById('loading-state');
const emptyState = document.getElementById('empty-state');

async function loadLookups() {
    if (!tbody) return;
    tbody.innerHTML = '';
    loadingState?.classList.remove('hidden');
    emptyState?.classList.add('hidden');

    try {
        const masters = await LookupService.getAll();
        loadingState?.classList.add('hidden');
        renderLookups(masters);
    } catch (error) {
        if(loadingState) loadingState.innerHTML = '<p class="text-red-500 text-center py-4">حدث خطأ أثناء تحميل البيانات</p>';
    }
}

function renderLookups(masters) {
    if (!tbody) return;

    if (masters.length === 0) {
        emptyState?.classList.remove('hidden');
        tbody.innerHTML = '';
        return;
    }

    emptyState?.classList.add('hidden');
    tableBody.innerHTML = masters.map(master => `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4">
                <div class="font-bold text-gray-900">${master.name}</div>
                <div class="text-xs text-gray-500">${master.code}</div>
            </td>
            <td class="px-6 py-4">
                <div class="flex flex-wrap gap-1">
                    ${master.values.map(val => `
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-lg text-xs border border-gray-200">${val.name}</span>
                    `).join('')}
                </div>
            </td>
            <td class="px-6 py-4 text-center">
                <button onclick="editMaster(${master.id})" class="text-accent hover:text-accent-hover transition-colors">
                    <i class="fas fa-edit"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

window.showCreateModal = () => {
    // Implementation for showing modal
};
