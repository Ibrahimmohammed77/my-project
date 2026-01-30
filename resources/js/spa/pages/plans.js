import { PlanService } from '../services/PlanService';

document.addEventListener('DOMContentLoaded', () => {
    loadPlans();
});

const tbody = document.querySelector('#plans-tbody');
const loadingState = document.getElementById('loading-state');
const emptyState = document.getElementById('empty-state');
const planModal = document.getElementById('plan-modal');
const planForm = document.getElementById('plan-form');
const modalTitle = document.getElementById('modal-title');
const planIdInput = document.getElementById('plan-id');

let plans = [];

async function loadPlans() {
    if (!tbody) return;
    tbody.innerHTML = '';
    loadingState?.classList.remove('hidden');
    emptyState?.classList.add('hidden');

    try {
        plans = await PlanService.getAll();
        loadingState?.classList.add('hidden');
        renderPlans(plans);
    } catch (error) {
        if(loadingState) loadingState.innerHTML = '<p class="text-red-500 text-center py-4">حدث خطأ أثناء تحميل البيانات</p>';
    }
}

function renderPlans(plansToRender) {
    if (!tbody) return;

    if (plansToRender.length === 0) {
        emptyState?.classList.remove('hidden');
        tbody.innerHTML = '';
        return;
    }

    emptyState?.classList.add('hidden');
    tbody.innerHTML = plansToRender.map(plan => `
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
                <div class="flex items-center justify-center gap-2">
                    <button onclick="editPlan(${plan.id})" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="تعديل">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deletePlan(${plan.id})" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="حذف">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

window.showCreateModal = () => {
    if(modalTitle) modalTitle.textContent = 'إضافة خطة جديدة';
    planForm.reset();
    planIdInput.value = '';
    
    // Reset toggle
    const toggle = document.getElementById('is_active');
    if(toggle) toggle.checked = true;

    planModal.classList.remove('hidden');
};

window.editPlan = (id) => {
    const plan = plans.find(p => p.id === id);
    if (!plan) return;

    if(modalTitle) modalTitle.textContent = 'تعديل الخطة';
    planIdInput.value = plan.id;
    
    document.getElementById('name').value = plan.name;
    document.getElementById('description').value = plan.description || '';
    document.getElementById('price_monthly').value = plan.price_monthly;
    document.getElementById('price_yearly').value = plan.price_yearly;
    
    // Features are JSON, might need parsing if form has feature inputs
    // For now assuming basic fields

    const toggle = document.getElementById('is_active');
    if(toggle) toggle.checked = plan.is_active;

    planModal.classList.remove('hidden');
};

window.closeModal = () => {
    planModal.classList.add('hidden');
};

window.deletePlan = async (id) => {
    if (!confirm('هل أنت متأكد من حذف هذه الخطة؟')) return;
    try {
        await PlanService.delete(id);
        loadPlans(); // Reload
        // showToast('تم الحذف بنجاح', 'success'); // If toast util exists
    } catch (error) {
        alert('حدث خطأ أثناء الحذف');
    }
};


document.addEventListener('DOMContentLoaded', () => {
    loadPlans();

    if (planForm) {
        planForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = planIdInput.value;
            
            // Construct Plan object
            // Ideally use Plan model, or simple object
            const formData = {
                name: document.getElementById('name').value,
                description: document.getElementById('description').value,
                price_monthly: document.getElementById('price_monthly').value,
                price_yearly: document.getElementById('price_yearly').value,
                is_active: document.getElementById('is_active').checked
            };
            // Map to Plan Model logic if needed properly

            try {
                if (id) {
                    await PlanService.update(id, new Plan({ ...formData, id }));
                } else {
                    await PlanService.create(new Plan(formData));
                }
                closeModal();
                loadPlans();
            } catch (error) {
                 alert('حدث خطأ: ' + (error.response?.data?.message || error.message));
            }
        });
    }
});
