import { Studio } from '../models/Studio';
import { StudioService } from '../services/StudioService';

let studios = [];

// DOM Elements
const tbody = document.getElementById('studios-tbody');
const loadingState = document.getElementById('loading-state');
const emptyState = document.getElementById('empty-state');
const searchInput = document.getElementById('search');
const statusFilter = document.getElementById('status-filter');
const studioModal = document.getElementById('studio-modal');
const studioForm = document.getElementById('studio-form');
const createBtn = document.getElementById('create-btn');
const modalTitle = document.getElementById('studio-modal-title');
const studioIdInput = document.getElementById('studio-id');

// Global Function to be accessible from HTML onclick attributes
window.editStudio = async (id) => {
    const studio = studios.find(s => s.studio_id === id);
    if (!studio) return;
    
    if(modalTitle) modalTitle.innerHTML = '<span class="w-2 h-6 bg-orange-500 rounded-full"></span><span>تعديل استوديو</span>';
    
    studioIdInput.value = studio.studio_id;
    document.getElementById('name').value = studio.name;
    document.getElementById('email').value = studio.email || '';
    document.getElementById('phone').value = studio.phone || '';
    document.getElementById('website').value = studio.website || '';
    
    if(studio.studio_status_id) {
        document.getElementById('studio_status_id').value = studio.studio_status_id;
    }

    studioModal.classList.remove('hidden');
};

window.deleteStudio = async (id) => {
    if (!confirm('هل أنت متأكد من حذف هذا الاستوديو؟')) return;
    try {
        await StudioService.delete(id);
        loadStudios();
    } catch (error) {
        alert('حدث خطأ: ' + (error.response?.data?.message || error.message));
    }
};

window.showCreateModal = () => {
    if(modalTitle) modalTitle.innerHTML = '<span class="w-2 h-6 bg-accent rounded-full"></span><span>إضافة استوديو جديد</span>';
    
    studioForm.reset();
    studioIdInput.value = '';
    
    studioModal.classList.remove('hidden');
};

window.closeModal = () => {
    studioModal.classList.add('hidden');
};

// Main Load Function
async function loadStudios() {
    if(!tbody) return;

    tbody.innerHTML = '';
    loadingState?.classList.remove('hidden');
    emptyState?.classList.add('hidden');

    try {
        studios = await StudioService.getAll();
        loadingState?.classList.add('hidden');
        renderStudios();
        
        // Init pagination if available
        if (window['studios_initPagination']) {
            window['studios_initPagination']();
        }
    } catch (error) {
        if(loadingState) loadingState.innerHTML = '<p class="text-red-500 text-center py-4">حدث خطأ أثناء تحميل البيانات</p>';
    }
}

function renderStudios() {
    if (!tbody) return;

    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    const statusValue = statusFilter ? statusFilter.value : '';
    
    const filteredStudios = studios.filter(studio => {
        const matchesSearch = (studio.name.toLowerCase().includes(searchTerm) || 
                             (studio.email && studio.email.toLowerCase().includes(searchTerm)));
        const matchesStatus = !statusValue || studio.status?.code === statusValue;
        return matchesSearch && matchesStatus;
    });

    if (filteredStudios.length === 0) {
        emptyState?.classList.remove('hidden');
        tbody.innerHTML = '';
        // If pagination exists, update it to show 0
         if (window['studios_initPagination']) window['studios_initPagination']();
        return;
    }

    emptyState?.classList.add('hidden');

    tbody.innerHTML = filteredStudios.map(studio => `
        <tr class="group">
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-700 font-bold text-sm uppercase shrink-0">
                        ${studio.name.charAt(0)}
                    </div>
                    <div>
                        <div class="font-bold text-gray-900 text-sm">${studio.name}</div>
                        ${studio.website ? `<a href="${studio.website}" target="_blank" class="text-xs text-blue-500 hover:underline">${studio.website}</a>` : ''}
                    </div>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="flex flex-col gap-1 text-xs">
                    ${studio.email ? `
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="fas fa-envelope text-gray-400 w-3"></i>
                            <span class="font-mono">${studio.email}</span>
                        </div>
                    ` : ''}
                    ${studio.phone ? `
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="fas fa-phone text-gray-400 w-3"></i>
                            <span class="font-mono">${studio.phone}</span>
                        </div>
                    ` : ''}
                </div>
            </td>
            <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold ${getStatusClass(studio.status?.code)} border border-current/10">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                    ${studio.status?.name || 'غير محدد'}
                </span>
            </td>
            <td class="px-6 py-4 text-center">
                <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-all">
                    <button onclick="editStudio(${studio.studio_id})" class="h-8 w-8 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all flex items-center justify-center" title="تعديل">
                        <i class="fas fa-pen text-xs"></i>
                    </button>
                    <button onclick="deleteStudio(${studio.studio_id})" class="h-8 w-8 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all flex items-center justify-center" title="حذف">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');

    // Re-init pagination to handle new rows
     if (window['studios_initPagination']) {
        window['studios_initPagination']();
    }
}

function getStatusClass(code) {
    const classes = {
        'ACTIVE': 'bg-green-50 text-green-700',
        'PENDING': 'bg-yellow-50 text-yellow-700',
        'SUSPENDED': 'bg-red-50 text-red-700'
    };
    return classes[code] || 'bg-gray-50 text-gray-700';
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    if (searchInput) searchInput.addEventListener('input', renderStudios);
    if (statusFilter) statusFilter.addEventListener('change', renderStudios);
    if (studioForm) {
        studioForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = studioIdInput.value;
            
            const studioData = new Studio({
                name: document.getElementById('name').value,
                email: document.getElementById('email').value || null,
                phone: document.getElementById('phone').value || null,
                website: document.getElementById('website').value || null,
                studio_status_id: document.getElementById('studio_status_id').value
            });
            
            try {
                if (id) await StudioService.update(id, studioData);
                else await StudioService.create(studioData);
                
                closeModal();
                loadStudios();
            } catch (error) {
                alert('حدث خطأ: ' + (error.response?.data?.message || 'تأكد من صحة البيانات'));
            }
        });
    }

    // Initial Load
    loadStudios();
});
