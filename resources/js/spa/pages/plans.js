import { PlanService } from '../services/PlanService';

document.addEventListener('DOMContentLoaded', () => {
    loadPlans();
});

const tbody = document.querySelector('#plans-tbody');
const loadingState = document.getElementById('loading-state');
const emptyState = document.getElementById('empty-state');

async function loadPlans() {
    if (!tbody) return;
    tbody.innerHTML = '';
    loadingState?.classList.remove('hidden');
    emptyState?.classList.add('hidden');

    try {
        const plans = await PlanService.getAll();
        loadingState?.classList.add('hidden');
        renderPlans(plans);
    } catch (error) {
        if(loadingState) loadingState.innerHTML = '<p class="text-red-500 text-center py-4">حدث خطأ أثناء تحميل البيانات</p>';
    }
}

function renderPlans(plans) {
    if (!tbody) return;

    if (plans.length === 0) {
        emptyState?.classList.remove('hidden');
        tbody.innerHTML = '';
        return;
    }

    emptyState?.classList.add('hidden');
    tbody.innerHTML = plans.map(plan => `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4">
                <div class="font-bold text-gray-900">${plan.name}</div>
                <div class="text-xs text-gray-500">${plan.description || ''}</div>
            </td>
            <td class="px-6 py-4">
                <div class="text-sm font-bold text-gray-900">${plan.price_monthly} ريال / شهر</div>
                <div class="text-xs text-gray-500">${plan.price_yearly} ريال / سنة</div>
            </td>
            <td class="px-6 py-4">
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold ${plan.is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">
                    ${plan.is_active ? 'نشط' : 'غير نشط'}
                </span>
            </td>
            <td class="px-6 py-4 text-center">
                <button onclick="editPlan(${plan.id})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                    <i class="fas fa-edit"></i>
                </button>
            </td>
        </tr>
    `).join('');
}
