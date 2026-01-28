import { School } from '../models/School';
import { SchoolService } from '../services/SchoolService';

let schools = [];

// DOM Elements
const tbody = document.getElementById('schools-tbody');
const loadingState = document.getElementById('loading-state');
const emptyState = document.getElementById('empty-state');
const searchInput = document.getElementById('search');
const statusFilter = document.getElementById('status-filter');
const schoolModal = document.getElementById('school-modal');
const schoolForm = document.getElementById('school-form');
const modalTitle = document.getElementById('school-modal-title');
const schoolIdInput = document.getElementById('school-id');

// Global Functions
window.editSchool = async (id) => {
    const school = schools.find(s => s.school_id === id);
    if (!school) return;
    
    if(modalTitle) modalTitle.innerHTML = '<span class="w-2 h-6 bg-orange-500 rounded-full"></span><span>تعديل مدرسة</span>';
    
    schoolIdInput.value = school.school_id;
    document.getElementById('name').value = school.name;
    document.getElementById('email').value = school.email || '';
    document.getElementById('phone').value = school.phone || '';
    document.getElementById('city').value = school.city || '';
    
    if(school.school_status_id) {
        document.getElementById('school_status_id').value = school.school_status_id;
    }
    if(school.school_type_id) {
        document.getElementById('school_type_id').value = school.school_type_id;
    }
    if(school.school_level_id) {
        document.getElementById('school_level_id').value = school.school_level_id;
    }

    schoolModal.classList.remove('hidden');
};

window.deleteSchool = async (id) => {
    if (!confirm('هل أنت متأكد من حذف هذه المدرسة؟')) return;
    try {
        await SchoolService.delete(id);
        loadSchools();
    } catch (error) {
        alert('حدث خطأ: ' + (error.response?.data?.message || error.message));
    }
};

window.showCreateModal = () => {
    if(modalTitle) modalTitle.innerHTML = '<span class="w-2 h-6 bg-accent rounded-full"></span><span>إضافة مدرسة جديدة</span>';
    
    schoolForm.reset();
    schoolIdInput.value = '';
    
    schoolModal.classList.remove('hidden');
};

window.closeModal = () => {
    schoolModal.classList.add('hidden');
};

// Main Load Function
async function loadSchools() {
    if(!tbody) return;

    tbody.innerHTML = '';
    loadingState?.classList.remove('hidden');
    emptyState?.classList.add('hidden');

    try {
        schools = await SchoolService.getAll();
        loadingState?.classList.add('hidden');
        renderSchools();
        
        // Init pagination
        if (window['schools_initPagination']) {
            window['schools_initPagination']();
        }
    } catch (error) {
        if(loadingState) loadingState.innerHTML = '<p class="text-red-500 text-center py-4">حدث خطأ أثناء تحميل البيانات</p>';
    }
}

function renderSchools() {
    if (!tbody) return;

    const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
    const statusValue = statusFilter ? statusFilter.value : '';
    
    const filteredSchools = schools.filter(school => {
        const matchesSearch = (school.name.toLowerCase().includes(searchTerm) || 
                             (school.email && school.email.toLowerCase().includes(searchTerm)));
        const matchesStatus = !statusValue || school.status?.code === statusValue;
        return matchesSearch && matchesStatus;
    });

    if (filteredSchools.length === 0) {
        emptyState?.classList.remove('hidden');
        tbody.innerHTML = '';
        if (window['schools_initPagination']) window['schools_initPagination']();
        return;
    }

    emptyState?.classList.add('hidden');

    tbody.innerHTML = filteredSchools.map(school => `
        <tr class="group">
            <td class="px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-700 font-bold text-sm uppercase shrink-0">
                        ${school.name.charAt(0)}
                    </div>
                    <div>
                        <div class="font-bold text-gray-900 text-sm">${school.name}</div>
                        ${school.city ? `<div class="text-xs text-gray-500"><i class="fas fa-map-marker-alt text-xs mr-1"></i>${school.city}</div>` : ''}
                    </div>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="flex flex-col gap-1 text-xs">
                    ${school.email ? `
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="fas fa-envelope text-gray-400 w-3"></i>
                            <span class="font-mono">${school.email}</span>
                        </div>
                    ` : ''}
                    ${school.phone ? `
                        <div class="flex items-center gap-2 text-gray-600">
                            <i class="fas fa-phone text-gray-400 w-3"></i>
                            <span class="font-mono">${school.phone}</span>
                        </div>
                    ` : ''}
                </div>
            </td>
             <td class="px-6 py-4">
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-semibold text-gray-700">${school.type?.name || '-'}</span>
                    <span class="text-[10px] text-gray-500">${school.level?.name || '-'}</span>
                </div>
            </td>
            <td class="px-6 py-4">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold ${getStatusClass(school.status?.code)} border border-current/10">
                    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                    ${school.status?.name || 'غير محدد'}
                </span>
            </td>
            <td class="px-6 py-4 text-center">
                <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-all">
                    <button onclick="editSchool(${school.school_id})" class="h-8 w-8 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all flex items-center justify-center" title="تعديل">
                        <i class="fas fa-pen text-xs"></i>
                    </button>
                    <button onclick="deleteSchool(${school.school_id})" class="h-8 w-8 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition-all flex items-center justify-center" title="حذف">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');

    // Re-init pagination
    if (window['schools_initPagination']) {
        window['schools_initPagination']();
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
    if (searchInput) searchInput.addEventListener('input', renderSchools);
    if (statusFilter) statusFilter.addEventListener('change', renderSchools);
    if (schoolForm) {
        schoolForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = schoolIdInput.value;
            
            const schoolData = new School({
                name: document.getElementById('name').value,
                email: document.getElementById('email').value || null,
                phone: document.getElementById('phone').value || null,
                city: document.getElementById('city').value || null,
                school_status_id: document.getElementById('school_status_id').value,
                school_type_id: document.getElementById('school_type_id').value,
                school_level_id: document.getElementById('school_level_id').value
            });
            
            try {
                if (id) await SchoolService.update(id, schoolData);
                else await SchoolService.create(schoolData);
                
                closeModal();
                loadSchools();
            } catch (error) {
                alert('حدث خطأ: ' + (error.response?.data?.message || 'تأكد من صحة البيانات'));
            }
        });
    }

    // Initial Load
    loadSchools();
});
