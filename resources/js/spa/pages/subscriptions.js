import '../../bootstrap';
import { Subscription } from '../models/Subscription';
import { SubscriptionService } from '../services/SubscriptionService';
import { showToast, showErrors, clearErrors } from '../utils/toast';

let subscriptions = [];

// DOM Elements
const tbody = document.getElementById('subscriptions-tbody');
const loadingState = document.getElementById('loading-state');
const emptyState = document.getElementById('empty-state');
const searchInput = document.getElementById('search');
const planFilter = document.getElementById('plan-filter');
const subscriptionModal = document.getElementById('subscription-modal');
const subscriptionForm = document.getElementById('subscription-form');
const modalTitle = document.getElementById('subscription-modal-title');

// Internal Logic
async function loadSubscriptions() {
    if (loadingState) loadingState.classList.remove('hidden');
    if (tbody) {
        // Clear previous rows except loading state
        const rows = tbody.querySelectorAll('tr:not(#loading-state)');
        rows.forEach(row => row.remove());
    }
    
    try {
        const filters = {
            search: searchInput ? searchInput.value : '',
            plan_id: planFilter ? planFilter.value : ''
        };
        subscriptions = await SubscriptionService.getAll(filters);
        renderSubscriptions();
    } catch (error) {
        console.error(error);
        showToast('خطأ في تحميل الاشتراكات', 'error');
    } finally {
        if (loadingState) loadingState.classList.add('hidden');
    }
}

function renderSubscriptions() {
    if (!tbody) return;
    
    if (subscriptions.length === 0) {
        if (emptyState) emptyState.classList.remove('hidden');
        return;
    }
    
    if (emptyState) emptyState.classList.add('hidden');
    
    const html = subscriptions.map(sub => `
        <tr class="hover:bg-gray-50/50 transition-colors duration-200 border-b border-gray-100 last:border-0">
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-accent/10 flex items-center justify-center text-accent font-bold text-sm">
                        ${sub.user ? sub.user.name.substring(0, 1).toUpperCase() : '?'}
                    </div>
                    <div>
                        <div class="text-sm font-bold text-gray-900">${sub.user ? sub.user.name : 'مستخدم مجهول'}</div>
                        <div class="text-xs text-gray-500">@${sub.user ? sub.user.username : '---'}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="flex flex-col gap-1">
                    <span class="text-sm font-bold text-gray-900">${sub.plan ? sub.plan.name : '---'}</span>
                    <span class="text-xs text-gray-500">${sub.auto_renew ? 'تجديد تلقائي' : 'لا يوجد تجديد'}</span>
                </div>
            </td>
            <td class="px-6 py-4">
                <span class="px-3 py-1 rounded-full text-xs font-bold ${sub.status?.code === 'ACTIVE' ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600'}">
                    ${sub.status ? sub.status.name : '---'}
                </span>
            </td>
            <td class="px-6 py-4">
                <div class="text-sm text-gray-600">${sub.end_date ? new Date(sub.end_date).toLocaleDateString('ar-YE') : '---'}</div>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-2">
                    <button onclick="deleteSubscription(${sub.subscription_id})" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-200 flex items-center justify-center shadow-sm" title="حذف/إلغاء">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');

    tbody.insertAdjacentHTML('beforeend', html);
}

window.closeModal = () => {
    if (subscriptionModal) subscriptionModal.classList.add('hidden');
    if (subscriptionForm) subscriptionForm.reset();
    clearErrors();
};

window.showCreateModal = () => {
    if (subscriptionModal) subscriptionModal.classList.remove('hidden');
};

window.deleteSubscription = async (id) => {
    if (!confirm('هل أنت متأكد من رغبتك في حذف هذا الاشتراك؟')) return;
    
    try {
        await SubscriptionService.delete(id);
        showToast('تم حذف الاشتراك بنجاح', 'success');
        loadSubscriptions();
    } catch (error) {
        console.error(error);
        showToast('حدث خطأ أثناء حذف الاشتراك', 'error');
    }
};

document.addEventListener('DOMContentLoaded', () => {
    if (searchInput) searchInput.addEventListener('input', loadSubscriptions);
    if (planFilter) planFilter.addEventListener('change', loadSubscriptions);

    if (subscriptionForm) {
        subscriptionForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            clearErrors();
            
            try {
                const data = {
                    user_id: document.getElementById('user_id').value,
                    plan_id: document.getElementById('plan_id').value,
                    billing_cycle: document.getElementById('billing_cycle').value,
                    auto_renew: document.getElementById('auto_renew').checked
                };

                await SubscriptionService.save(data);
                
                closeModal();
                loadSubscriptions();
                showToast('تم منح الاشتراك بنجاح', 'success');
            } catch (error) {
                console.error(error);
                if (error.response && error.response.status === 422) {
                    showErrors(error.response.data.errors);
                    showToast('يرجى التحقق من البيانات المدخلة', 'error');
                } else {
                    showToast('حدث خطأ أثناء حفظ الاشتراك', 'error');
                }
            }
        });
    }

    // Initial Load
    loadSubscriptions();
});
