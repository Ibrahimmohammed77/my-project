import { Permission } from '../models/Permission';
import { PermissionService } from '../services/PermissionService';

let permissions = [];

// DOM Elements
const tbody = document.getElementById('permissions-tbody');
const loadingState = document.getElementById('loading-state');
const emptyState = document.getElementById('empty-state');
const searchInput = document.getElementById('search');
const permissionModal = document.getElementById('permission-modal');
const permissionForm = document.getElementById('permission-form');
const modalTitle = document.getElementById('modal-title');
const permissionIdInput = document.getElementById('permission-id');

// Global Functions
window.editPermission = async (id) => {
    const perm = permissions.find(p => p.permission_id === id);
    if (!perm) return;
    
    if(modalTitle) modalTitle.innerHTML = '<span class="w-2 h-6 bg-orange-500 rounded-full"></span><span>تعديل صلاحية</span>';
    
    permissionIdInput.value = perm.permission_id;
    document.getElementById('name').value = perm.name;
    document.getElementById('resource_type').value = perm.resource_type;
    document.getElementById('action').value = perm.action;
    document.getElementById('description').value = perm.description || '';
    
    permissionModal.classList.remove('hidden');
};

window.deletePermission = async (id) => {
    if (!confirm('هل أنت متأكد من حذف هذه الصلاحية؟')) return;
    try {
        await PermissionService.delete(id);
        loadPermissions();
    } catch (error) {
        alert('حدث خطأ: ' + (error.response?.data?.message || error.message));
    }
};

window.showCreateModal = () => {
    if(modalTitle) modalTitle.innerHTML = '<span class="w-2 h-6 bg-accent rounded-full"></span><span>إضافة صلاحية جديدة</span>';
    
    permissionForm.reset();
    permissionIdInput.value = '';
    
    permissionModal.classList.remove('hidden');
};

window.closeModal = () => {
    permissionModal.classList.add('hidden');
};

// Main Load Function
async function loadPermissions() {
    if(!tbody) return;

    tbody.innerHTML = '';
    loadingState?.classList.remove('hidden');
    emptyState?.classList.add('hidden');

    try {
        permissions = await PermissionService.getAll();
        loadingState?.classList.add('hidden');
        renderPermissions();
        // Init pagination if available
        if (window['permissions_initPagination']) window['permissions_initPagination']();
    } catch (error) {
        if(loadingState) loadingState.innerHTML = '<p class="text-red-500 text-center py-4">حدث خطأ أثناء تحميل البيانات</p>';
    }
}

function renderPermissions() {
    if (!tbody) return;

    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    
    const filteredPerms = permissions.filter(perm => 
        perm.name.toLowerCase().includes(searchTerm) || 
        perm.resource_type.toLowerCase().includes(searchTerm) || 
        perm.action.toLowerCase().includes(searchTerm)
    );

    if (filteredPerms.length === 0) {
        emptyState?.classList.remove('hidden');
        tbody.innerHTML = '';
        if (window['permissions_initPagination']) window['permissions_initPagination']();
        return;
    }

    emptyState?.classList.add('hidden');

    tbody.innerHTML = filteredPerms.map(perm => `
        <tr class="hover:bg-gray-50/80 transition-colors group">
            <td class="px-6 py-4">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-2xl ${getResourceColor(perm.resource_type)} flex items-center justify-center font-bold text-lg shrink-0 shadow-sm border border-white">
                        <i class="fas ${getResourceIcon(perm.resource_type)}"></i>
                    </div>
                    <div>
                        <div class="font-bold text-gray-900 text-sm mb-1">${perm.name}</div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[10px] text-gray-400 font-mono bg-gray-50 px-1.5 rounded border border-gray-100">ID: ${perm.permission_id}</span>
                            <span class="text-[11px] text-gray-400">${perm.resource_type}</span>
                        </div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4">
                <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold ${getActionClass(perm.action)} border border-current/10 shadow-sm">
                    <i class="fas ${getActionIcon(perm.action)} text-xs"></i>
                    <span class="uppercase tracking-wider">${perm.action}</span>
                </span>
            </td>
            <td class="px-6 py-4">
                <div class="max-w-xs">
                    <p class="text-sm text-gray-600 leading-relaxed line-clamp-2" title="${perm.description}">
                        ${perm.description || '<span class="text-gray-400 italic font-light">لا يوجد وصف متاح لهذه الصلاحية</span>'}
                    </p>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                    <button onclick="editPermission(${perm.permission_id})" class="h-9 w-9 rounded-xl bg-white border border-gray-200 text-blue-600 hover:bg-blue-50 hover:border-blue-200 flex items-center justify-center transition-all shadow-sm hover:shadow-md" title="تعديل">
                        <i class="fas fa-pen text-xs"></i>
                    </button>
                    <button onclick="deletePermission(${perm.permission_id})" class="h-9 w-9 rounded-xl bg-white border border-gray-200 text-red-600 hover:bg-red-50 hover:border-red-200 flex items-center justify-center transition-all shadow-sm hover:shadow-md" title="حذف">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
    
     // Re-init pagination
    if (window['permissions_initPagination']) {
        window['permissions_initPagination']();
    }
}

function getActionClass(action) {
    const map = {
        'create': 'bg-green-50 text-green-700',
        'read': 'bg-blue-50 text-blue-700',
        'update': 'bg-orange-50 text-orange-700',
        'delete': 'bg-red-50 text-red-700',
        'manage': 'bg-purple-50 text-purple-700'
    };
    return map[action.toLowerCase()] || 'bg-gray-100 text-gray-700';
}

function getActionIcon(action) {
    const map = {
        'create': 'fa-plus',
        'read': 'fa-eye',
        'update': 'fa-pen-to-square',
        'delete': 'fa-trash-can',
        'manage': 'fa-sliders'
    };
    return map[action.toLowerCase()] || 'fa-circle';
}

function getResourceIcon(resource) {
    const map = {
        'accounts': 'fa-users',
        'roles': 'fa-user-shield',
        'permissions': 'fa-key',
        'logs': 'fa-clipboard-list',
        'settings': 'fa-gear',
        'dashboard': 'fa-chart-pie'
    };
    return map[resource.toLowerCase()] || 'fa-box-open';
}

function getResourceColor(resource) {
    const map = {
        'accounts': 'bg-blue-100 text-blue-600',
        'roles': 'bg-purple-100 text-purple-600',
        'permissions': 'bg-amber-100 text-amber-600',
        'logs': 'bg-gray-100 text-gray-600',
        'settings': 'bg-slate-100 text-slate-600'
    };
    return map[resource.toLowerCase()] || 'bg-indigo-50 text-indigo-600';
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    if (searchInput) searchInput.addEventListener('input', renderPermissions);
    
    if (permissionForm) {
        permissionForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = permissionIdInput.value;
            
            const permissionData = new Permission({
                name: document.getElementById('name').value,
                resource_type: document.getElementById('resource_type').value,
                action: document.getElementById('action').value,
                description: document.getElementById('description').value
            });
            
            try {
                if (id) await PermissionService.update(id, permissionData);
                else await PermissionService.create(permissionData);
                
                closeModal();
                loadPermissions();
            } catch (error) {
                alert('حدث خطأ: ' + (error.response?.data?.message || 'تأكد من صحة البيانات'));
            }
        });
    }

    // Initial Load
    loadPermissions();
});
