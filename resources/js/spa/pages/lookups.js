import { LookupService } from '../services/LookupService';

const tbody = document.querySelector('#lookups-tbody');
const loadingState = document.getElementById('loading-state');
const emptyState = document.getElementById('empty-state');
const lookupModal = document.getElementById('lookup-modal');
const modalTitle = document.getElementById('modal-title');
const valuesTbody = document.getElementById('values-tbody');
const valueForm = document.getElementById('value-form');
const valueIdInput = document.getElementById('value-id');
const masterIdInput = document.getElementById('lookup-master-id');

let masters = [];

async function loadLookups() {
    if (!tbody) return;
    tbody.innerHTML = '';
    loadingState?.classList.remove('hidden');
    emptyState?.classList.add('hidden');

    try {
        masters = await LookupService.getAll();
        loadingState?.classList.add('hidden');
        renderLookups(masters);
    } catch (error) {
        if(loadingState) loadingState.innerHTML = '<p class="text-red-500 text-center py-4">حدث خطأ أثناء تحميل البيانات</p>';
    }
}

function renderLookups(mastersToRender) {
    if (!tbody) return;

    if (mastersToRender.length === 0) {
        emptyState?.classList.remove('hidden');
        tbody.innerHTML = '';
        return;
    }

    emptyState?.classList.add('hidden');
    tbody.innerHTML = mastersToRender.map(master => `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-6 py-4">
                <div class="font-bold text-gray-900">${master.name}</div>
                <div class="text-xs text-gray-500 font-mono">${master.code}</div>
            </td>
            <td class="px-6 py-4">
                <div class="flex flex-wrap gap-1">
                    ${master.values.map(val => `
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-lg text-xs border border-gray-200">${val.name}</span>
                    `).join('')}
                </div>
            </td>
            <td class="px-6 py-4 text-center">
                <button onclick="editMaster(${master.id})" class="text-blue-600 hover:text-blue-800 transition-colors p-2 rounded hover:bg-blue-50" title="إدارة القيم">
                    <i class="fas fa-cog"></i>
                </button>
            </td>
        </tr>
    `).join('');
}

window.editMaster = (id) => {
    const master = masters.find(m => m.id === id);
    if (!master) return;

    if(modalTitle) modalTitle.textContent = `إدارة القيم: ${master.name}`;
    masterIdInput.value = master.id;
    
    // Render Values table
    renderValues(master.values);
    
    resetValueForm();
    lookupModal.classList.remove('hidden');
};

function renderValues(values) {
    if (!valuesTbody) return;
    valuesTbody.innerHTML = values.map(val => `
        <tr>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${val.name}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">${val.code}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <button onclick="editValue(${val.id})" class="text-indigo-600 hover:text-indigo-900 ml-2">تعديل</button>
                <button onclick="deleteValue(${val.id})" class="text-red-600 hover:text-red-900">حذف</button>
            </td>
        </tr>
    `).join('');
}

window.resetValueForm = () => {
    valueForm.reset();
    valueIdInput.value = '';
    // Keep masterId
};

window.editValue = (valId) => {
    const masterId = parseInt(masterIdInput.value);
    const master = masters.find(m => m.id === masterId);
    if (!master) return;
    
    const val = master.values.find(v => v.id === valId);
    if (!val) return;
    
    valueIdInput.value = val.id;
    document.getElementById('value-name').value = val.name;
    document.getElementById('value-code').value = val.code;
    document.getElementById('value-description').value = val.description || '';
};

window.deleteValue = async (valId) => {
    if (!confirm('هل أنت متأكد من حذف هذه القيمة؟')) return;
    try {
        await LookupService.deleteValue(valId);
        // Refresh
        await loadLookups();
        // Update Modal List
        const masterId = parseInt(masterIdInput.value);
        const master = masters.find(m => m.id === masterId);
        if (master) renderValues(master.values);
    } catch (error) {
        alert('حدث خطأ أثناء الحذف: ' + (error.response?.data?.message || error.message));
    }
};

window.closeModal = () => {
    lookupModal.classList.add('hidden');
};

document.addEventListener('DOMContentLoaded', () => {
    loadLookups();
    
    if (valueForm) {
        valueForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const valId = valueIdInput.value;
            const masterId = masterIdInput.value;
            
            const formData = {
                lookup_master_id: masterId,
                name: document.getElementById('value-name').value,
                code: document.getElementById('value-code').value,
                description: document.getElementById('value-description').value,
                is_active: true
            };
            
            try {
                if (valId) {
                    await LookupService.updateValue(valId, formData);
                } else {
                    await LookupService.createValue(formData);
                }
                
                resetValueForm();
                await loadLookups();
                 // Update Modal List
                const master = masters.find(m => m.id === parseInt(masterId));
                if (master) renderValues(master.values);
                
            } catch (error) {
                alert('حدث خطأ: ' + (error.response?.data?.message || 'Error occurred'));
            }
        });
    }
});
