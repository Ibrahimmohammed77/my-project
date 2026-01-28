import { Role } from '../models/Role';
import { RoleService } from '../services/RoleService';

let roles = [];

// DOM Elements
const tbody = document.getElementById('roles-tbody');
const loadingState = document.getElementById('loading-state');
const emptyState = document.getElementById('empty-state');
const searchInput = document.getElementById('search');
const roleModal = document.getElementById('role-modal');
const roleForm = document.getElementById('role-form');
const modalTitle = document.getElementById('modal-title');
const roleIdInput = document.getElementById('role-id');

// Global Functions
window.editRole = async (id) => {
    const role = roles.find(r => r.role_id === id);
    if (!role) return;
    
    if(modalTitle) modalTitle.innerHTML = '<span class="w-2 h-6 bg-orange-500 rounded-full"></span><span>تعديل دور</span>';
    
    roleIdInput.value = role.role_id;
    document.getElementById('name').value = role.name;
    document.getElementById('description').value = role.description || '';
    
    roleModal.classList.remove('hidden');
};

window.deleteRole = async (id) => {
    if (!confirm('هل أنت متأكد من حذف هذا الدور؟')) return;
    try {
        await RoleService.delete(id);
        loadRoles();
    } catch (error) {
        alert('حدث خطأ: ' + (error.response?.data?.message || error.message));
    }
};

window.showCreateModal = () => {
    if(modalTitle) modalTitle.innerHTML = '<span class="w-2 h-6 bg-accent rounded-full"></span><span>إضافة دور جديد</span>';
    
    roleForm.reset();
    roleIdInput.value = '';
    
    roleModal.classList.remove('hidden');
};

window.closeModal = () => {
    roleModal.classList.add('hidden');
};

// Main Load Function
async function loadRoles() {
    if(!tbody) return;

    tbody.innerHTML = '';
    loadingState?.classList.remove('hidden');
    emptyState?.classList.add('hidden');

    try {
        roles = await RoleService.getAll();
        loadingState?.classList.add('hidden');
        renderRoles();
    } catch (error) {
        if(loadingState) loadingState.innerHTML = '<p class="text-red-500 text-center py-4">حدث خطأ أثناء تحميل البيانات</p>';
    }
}

function renderRoles() {
    if (!tbody) return;

    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    
    const filteredRoles = roles.filter(role => 
        role.name.toLowerCase().includes(searchTerm) || 
        (role.description && role.description.toLowerCase().includes(searchTerm))
    );

    if (filteredRoles.length === 0) {
        emptyState?.classList.remove('hidden');
        tbody.innerHTML = '';
        return;
    }

    emptyState?.classList.add('hidden');

    tbody.innerHTML = filteredRoles.map(role => `
        <tr class="hover:bg-gray-50/80 transition-colors group">
            <td class="px-6 py-4">
                <div class="flex items-center gap-4">
                    <div class="h-12 w-12 rounded-2xl ${role.is_system ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600'} flex items-center justify-center font-bold text-lg shrink-0 shadow-sm border border-white">
                        <i class="fas ${role.is_system ? 'fa-shield-halved' : 'fa-user-shield'}"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-gray-900 text-sm">${role.name}</span>
                            ${role.is_system ? 
                                '<span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-100"><i class="fas fa-lock text-[9px]"></i> نظامي</span>' 
                                : ''}
                        </div>
                        <div class="text-[11px] text-gray-400 mt-0.5 font-mono">ID: ${role.role_id}</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="max-w-md">
                    <p class="text-sm text-gray-600 leading-relaxed line-clamp-2" title="${role.description}">
                        ${role.description || '<span class="text-gray-400 italic font-light">لا يوجد وصف متاح لهذا الدور</span>'}
                    </p>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center gap-2">
                    <div class="flex -space-x-2 space-x-reverse overflow-hidden p-1">
                        <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 border-2 border-white text-gray-400 text-xs shadow-sm">
                            <i class="fas fa-key"></i>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold bg-gray-50 text-gray-700 border border-gray-200 shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full ${role.permissions?.length > 0 ? 'bg-green-500' : 'bg-gray-400'}"></span>
                        ${role.permissions?.length || 0} صلاحية
                    </span>
                </div>
            </td>
            <td class="px-6 py-4">
                ${!role.is_system ? `
                    <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0">
                        <button onclick="editRole(${role.role_id})" class="h-9 w-9 rounded-xl bg-white border border-gray-200 text-blue-600 hover:bg-blue-50 hover:border-blue-200 flex items-center justify-center transition-all shadow-sm hover:shadow-md" title="تعديل">
                            <i class="fas fa-pen text-xs"></i>
                        </button>
                        <button onclick="deleteRole(${role.role_id})" class="h-9 w-9 rounded-xl bg-white border border-gray-200 text-red-600 hover:bg-red-50 hover:border-red-200 flex items-center justify-center transition-all shadow-sm hover:shadow-md" title="حذف">
                            <i class="fas fa-trash text-xs"></i>
                        </button>
                    </div>
                ` : `
                    <div class="flex justify-center">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium bg-gray-50 text-gray-400 border border-gray-100 opacity-50 cursor-not-allowed">
                            <i class="fas fa-lock text-[10px]"></i>
                            محمي
                        </span>
                    </div>
                `}
            </td>
        </tr>
    `).join('');
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    if (searchInput) searchInput.addEventListener('input', renderRoles);
    
    if (roleForm) {
        roleForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = roleIdInput.value;
            
            const roleData = new Role({
                name: document.getElementById('name').value,
                description: document.getElementById('description').value
            });
            
            try {
                if (id) await RoleService.update(id, roleData);
                else await RoleService.create(roleData);
                
                closeModal();
                loadRoles();
            } catch (error) {
                alert('حدث خطأ: ' + (error.response?.data?.message || 'تأكد من صحة البيانات'));
            }
        });
    }

    // Initial Load
    loadRoles();
});
