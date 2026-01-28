import { Subscriber } from '../models/Subscriber';
import { SubscriberService } from '../services/SubscriberService';

let subscribers = [];

// DOM Elements
const tbody = document.getElementById('subscribers-tbody');
const loadingState = document.getElementById('loading-state');
const emptyState = document.getElementById('empty-state');
const searchInput = document.getElementById('search');
const subscriberModal = document.getElementById('subscriber-modal');
const subscriberForm = document.getElementById('subscriber-form');
const modalTitle = document.getElementById('modal-title');
const subscriberIdInput = document.getElementById('subscriber-id');

// Global Functions (attached to window for onclick access from innerHTML)
window.editSubscriber = async (id) => {
    const subscriber = subscribers.find(s => s.subscriber_id === id);
    if (!subscriber) return;
    
    if(modalTitle) modalTitle.innerHTML = '<span class="w-2 h-6 bg-orange-500 rounded-full"></span><span>تعديل مشترك</span>';
    
    subscriberIdInput.value = subscriber.subscriber_id;
    document.getElementById('account_id').value = subscriber.account_id || '';
    document.getElementById('subscriber_status_id').value = subscriber.subscriber_status_id || '';
    
    // Settings handling if needed - for now just basic fields
    
    subscriberModal.classList.remove('hidden');
};

window.deleteSubscriber = async (id) => {
    if (!confirm('هل أنت متأكد من حذف هذا المشترك؟')) return;
    try {
        await SubscriberService.delete(id);
        loadSubscribers();
    } catch (error) {
        alert('حدث خطأ: ' + (error.response?.data?.message || error.message));
    }
};

window.showCreateModal = () => {
    if(modalTitle) modalTitle.innerHTML = '<span class="w-2 h-6 bg-accent rounded-full"></span><span>إضافة مشترك جديد</span>';
    
    subscriberForm.reset();
    subscriberIdInput.value = '';
    
    subscriberModal.classList.remove('hidden');
};

window.closeModal = () => {
    subscriberModal.classList.add('hidden');
};

// Main Load Function
async function loadSubscribers() {
    if(!tbody) return;

    tbody.innerHTML = '';
    loadingState?.classList.remove('hidden');
    emptyState?.classList.add('hidden');

    try {
        subscribers = await SubscriberService.getAll();
        loadingState?.classList.add('hidden');
        renderSubscribers();
    } catch (error) {
        if(loadingState) loadingState.innerHTML = '<p class="text-red-500 text-center py-4">حدث خطأ أثناء تحميل البيانات</p>';
    }
}

function renderSubscribers() {
    if (!tbody) return;

    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    
    const filteredSubscribers = subscribers.filter(subscriber => {
        const accountName = subscriber.account ? subscriber.account.full_name?.toLowerCase() : '';
        const username = subscriber.account ? subscriber.account.username?.toLowerCase() : '';
        return accountName.includes(searchTerm) || username.includes(searchTerm);
    });

    if (filteredSubscribers.length === 0) {
        emptyState?.classList.remove('hidden');
        tbody.innerHTML = '';
        return;
    }

    emptyState?.classList.add('hidden');

    tbody.innerHTML = filteredSubscribers.map(subscriber => `
        <tr class="hover:bg-gray-50/80 transition-colors group">
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center text-blue-600 font-bold text-sm uppercase shrink-0">
                        <i class="fas fa-user"></i>
                    </div>
                    <div>
                        <div class="font-bold text-gray-900 text-sm">${subscriber.account ? subscriber.account.full_name : 'Unknown Account'}</div>
                        <div class="text-xs text-gray-500 font-mono">@${subscriber.account ? subscriber.account.username : '-'}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4">
                 <div class="text-xs font-mono text-gray-500">
                    ${subscriber.subscriber_id}
                 </div>
            </td>
            <td class="px-6 py-4">
                 <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium border ${
                    subscriber.status?.code === 'ACTIVE' ? 'bg-green-50 text-green-700 border-green-100' : 
                    (subscriber.status?.code === 'INACTIVE' ? 'bg-red-50 text-red-700 border-red-100' : 'bg-gray-50 text-gray-600 border-gray-100')
                 }">
                    <span class="w-1.5 h-1.5 rounded-full ${subscriber.status?.code === 'ACTIVE' ? 'bg-green-500' : (subscriber.status?.code === 'INACTIVE' ? 'bg-red-500' : 'bg-gray-400')}"></span>
                    ${subscriber.status ? subscriber.status.name : '-'}
                 </span>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                    <button onclick="editSubscriber(${subscriber.subscriber_id})" class="h-9 w-9 rounded-xl bg-white border border-gray-200 text-blue-600 hover:bg-blue-50 hover:border-blue-200 flex items-center justify-center transition-all shadow-sm hover:shadow-md" title="تعديل">
                        <i class="fas fa-pen text-xs"></i>
                    </button>
                    <button onclick="deleteSubscriber(${subscriber.subscriber_id})" class="h-9 w-9 rounded-xl bg-white border border-gray-200 text-red-600 hover:bg-red-50 hover:border-red-200 flex items-center justify-center transition-all shadow-sm hover:shadow-md" title="حذف">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    if (searchInput) searchInput.addEventListener('input', renderSubscribers);
    
    if (subscriberForm) {
        subscriberForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = subscriberIdInput.value;
            
            const subscriberData = new Subscriber({
                account_id: document.getElementById('account_id').value,
                subscriber_status_id: document.getElementById('subscriber_status_id').value,
                // Accessing settings might require more complex form logic if needed
            });
            
            try {
                if (id) await SubscriberService.update(id, subscriberData);
                else await SubscriberService.create(subscriberData);
                
                closeModal();
                loadSubscribers();
            } catch (error) {
                alert('حدث خطأ: ' + (error.response?.data?.message || 'تأكد من صحة البيانات'));
            }
        });
    }

    // Initial Load
    loadSubscribers();
});
