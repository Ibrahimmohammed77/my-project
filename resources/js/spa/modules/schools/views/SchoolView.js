/**
 * School View
 * Handles DOM rendering and UI updates for schools (same pattern as AccountView)
 */

import { XssProtection } from '../../../core/security/XssProtection.js';
import { DOM } from '../../../core/utils/dom.js';

function getStatusClass(code) {
    const classes = {
        'ACTIVE': 'bg-green-50 text-green-600',
        'PENDING': 'bg-yellow-50 text-yellow-600',
        'SUSPENDED': 'bg-red-50 text-red-600'
    };
    return classes[code] || 'bg-gray-50 text-gray-600';
}

export class SchoolView {
    constructor(options = {}) {
        this.tbody = options.tbody || DOM.query('#schools-tbody');
        this.loadingState = options.loadingState || DOM.query('#loading-state');
        this.emptyState = options.emptyState || DOM.query('#empty-state');
        this.modal = options.modal || DOM.query('#school-modal');
        this.modalTitle = options.modalTitle || DOM.query('#school-modal-title');
        this.form = options.form || DOM.query('#school-form');
        this.detailsModal = options.detailsModal || DOM.query('#school-details-modal');
        this.detailsData = DOM.query('#school-details-data');
        this.detailsLoading = DOM.query('#school-details-loading');
    }

    showLoading() {
        DOM.show(this.loadingState);
        if (this.tbody) {
            DOM.empty(this.tbody);
        }
    }

    hideLoading() {
        DOM.hide(this.loadingState);
    }

    showEmpty() {
        DOM.show(this.emptyState);
        DOM.hide(this.tbody?.parentElement);
    }

    hideEmpty() {
        DOM.hide(this.emptyState);
        DOM.show(this.tbody?.parentElement);
    }

    render(schools) {
        if (!this.tbody) return;

        DOM.empty(this.tbody);

        if (!schools || schools.length === 0) {
            this.showEmpty();
            return;
        }

        this.hideEmpty();

        schools.forEach(school => {
            const row = this.createSchoolRow(school);
            this.tbody.appendChild(row);
        });
    }

    createSchoolRow(school) {
        const tr = DOM.create('tr', {
            className: 'hover:bg-gray-50/50 transition-colors duration-200 border-b border-gray-100 last:border-0'
        });

        const nameCell = DOM.create('td', { className: 'px-6 py-4' });
        const nameDiv = DOM.create('div', { className: 'flex items-center gap-3' });
        const avatar = DOM.create('div', {
            className: 'w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center text-gray-700 font-bold text-sm uppercase shrink-0'
        });
        XssProtection.setTextContent(avatar, (school.name || '').charAt(0) || '-');
        const infoDiv = DOM.create('div');
        const nameTitle = DOM.create('div', { className: 'text-sm font-bold text-gray-900' });
        XssProtection.setTextContent(nameTitle, school.name || 'N/A');
        let cityDiv = null;
        if (school.city) {
            cityDiv = DOM.create('div', { className: 'text-xs text-gray-500' });
            cityDiv.innerHTML = '<i class="fas fa-map-marker-alt text-xs mr-1"></i>';
            cityDiv.appendChild(document.createTextNode(school.city));
        }
        infoDiv.appendChild(nameTitle);
        if (cityDiv) infoDiv.appendChild(cityDiv);
        nameDiv.appendChild(avatar);
        nameDiv.appendChild(infoDiv);
        nameCell.appendChild(nameDiv);

        const contactCell = DOM.create('td', { className: 'px-6 py-4' });
        const contactDiv = DOM.create('div', { className: 'flex flex-col gap-1 text-xs' });
        if (school.email) {
            const emailRow = DOM.create('div', { className: 'flex items-center gap-2 text-gray-600' });
            emailRow.innerHTML = '<i class="fas fa-envelope text-gray-400 w-3"></i>';
            const emailSpan = DOM.create('span', { className: 'font-mono' });
            XssProtection.setTextContent(emailSpan, school.email);
            emailRow.appendChild(emailSpan);
            contactDiv.appendChild(emailRow);
        }
        if (school.phone) {
            const phoneRow = DOM.create('div', { className: 'flex items-center gap-2 text-gray-600' });
            phoneRow.innerHTML = '<i class="fas fa-phone text-gray-400 w-3"></i>';
            const phoneSpan = DOM.create('span', { className: 'font-mono' });
            XssProtection.setTextContent(phoneSpan, school.phone);
            phoneRow.appendChild(phoneSpan);
            contactDiv.appendChild(phoneRow);
        }
        if (!school.email && !school.phone) {
            XssProtection.setTextContent(contactDiv, '-');
        }
        contactCell.appendChild(contactDiv);

        const typeLevelCell = DOM.create('td', { className: 'px-6 py-4' });
        const typeLevelDiv = DOM.create('div', { className: 'flex flex-col gap-1' });
        const typeSpan = DOM.create('span', { className: 'text-xs font-semibold text-gray-700' });
        XssProtection.setTextContent(typeSpan, school.type?.name || '-');
        const levelSpan = DOM.create('span', { className: 'text-[10px] text-gray-500' });
        XssProtection.setTextContent(levelSpan, school.level?.name || '-');
        typeLevelDiv.appendChild(typeSpan);
        typeLevelDiv.appendChild(levelSpan);
        typeLevelCell.appendChild(typeLevelDiv);

        const statusCell = DOM.create('td', { className: 'px-6 py-4' });
        const statusCode = school.status?.code || '';
        const statusBadge = DOM.create('span', {
            className: `inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold ${getStatusClass(statusCode)} border border-current/10`
        });
        statusBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-current"></span>';
        const statusText = DOM.create('span');
        XssProtection.setTextContent(statusText, school.status?.name || 'غير محدد');
        statusBadge.appendChild(statusText);
        statusCell.appendChild(statusBadge);

        const actionsCell = DOM.create('td', { className: 'px-6 py-4 text-center' });
        const actionsDiv = DOM.create('div', { className: 'flex items-center justify-center gap-2' });
        
        const viewBtn = DOM.create('button', {
            type: 'button',
            className: 'w-8 h-8 rounded-lg bg-green-50 text-green-500 hover:bg-green-500 hover:text-white transition-all duration-200 flex items-center justify-center shadow-sm',
            title: 'عرض التفاصيل',
            dataset: { action: 'view', schoolId: school.school_id }
        });
        viewBtn.innerHTML = '<i class="fas fa-eye text-xs"></i>';

        const editBtn = DOM.create('button', {
            type: 'button',
            className: 'w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white transition-all duration-200 flex items-center justify-center shadow-sm',
            title: 'تعديل',
            dataset: { action: 'edit', schoolId: school.school_id }
        });
        editBtn.innerHTML = '<i class="fas fa-pen text-xs"></i>';
        
        const deleteBtn = DOM.create('button', {
            type: 'button',
            className: 'w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-200 flex items-center justify-center shadow-sm',
            title: 'حذف',
            dataset: { action: 'delete', schoolId: school.school_id }
        });
        deleteBtn.innerHTML = '<i class="fas fa-trash text-xs"></i>';
        
        actionsDiv.appendChild(viewBtn);
        actionsDiv.appendChild(editBtn);
        actionsDiv.appendChild(deleteBtn);
        actionsCell.appendChild(actionsDiv);

        tr.appendChild(nameCell);
        tr.appendChild(contactCell);
        tr.appendChild(typeLevelCell);
        tr.appendChild(statusCell);
        tr.appendChild(actionsCell);

        return tr;
    }

    openModal(title = 'إضافة مدرسة جديدة') {
        if (!this.modal) return;
        if (this.modalTitle) {
            this.modalTitle.innerHTML = '<span class="w-2 h-6 bg-accent rounded-full"></span><span>' + (title || '') + '</span>';
        }
        DOM.removeClass(this.modal, 'hidden');
    }

    closeModal() {
        if (!this.modal) return;
        DOM.addClass(this.modal, 'hidden');
        if (this.form) this.form.reset();
        const credentialsSection = DOM.query('#credentials-section');
        if (credentialsSection) DOM.show(credentialsSection);
    }

    openDetailsModal(school) {
        if (!this.detailsModal) return;

        // Reset display
        DOM.show(this.detailsLoading);
        DOM.hide(this.detailsData);

        // Basic Info
        XssProtection.setTextContent(DOM.query('#detail-name'), school.name || 'N/A');
        XssProtection.setTextContent(DOM.query('#detail-type-level'), `${school.type?.name || '-'} • ${school.level?.name || '-'}`);
        XssProtection.setTextContent(DOM.query('#detail-city'), school.city || 'غير محدد');
        XssProtection.setTextContent(DOM.query('#detail-email'), school.email || 'غير متوفر');
        XssProtection.setTextContent(DOM.query('#detail-phone'), school.phone || 'غير متوفر');

        const statusBadge = DOM.query('#detail-status');
        if (statusBadge) {
            const statusCode = school.status?.code || '';
            statusBadge.className = `px-3 py-1 rounded-full text-xs font-bold border ${getStatusClass(statusCode)}`;
            XssProtection.setTextContent(statusBadge, school.status?.name || 'غير محدد');
        }

        const logoDiv = DOM.query('#detail-logo');
        if (logoDiv) {
            if (school.logo_url) {
                logoDiv.innerHTML = `<img src="${school.logo_url}" class="w-full h-full object-cover">`;
            } else {
                XssProtection.setTextContent(logoDiv, (school.name || '').charAt(0) || '-');
            }
        }

        const editBtn = DOM.query('#edit-from-details');
        if (editBtn) {
            editBtn.onclick = () => {
                this.closeDetailsModal();
                // We'll rely on the controller to handle it if needed or just trigger edit
                const event = new CustomEvent('school:edit', { detail: { schoolId: school.school_id } });
                document.dispatchEvent(event);
            };
        }

        DOM.removeClass(this.detailsModal, 'hidden');
    }

    closeDetailsModal() {
        if (!this.detailsModal) return;
        DOM.addClass(this.detailsModal, 'hidden');
    }

    renderStatistics(data) {
        const stats = data.statistics;
        if (!stats) return;

        DOM.hide(this.detailsLoading);
        DOM.show(this.detailsData);

        // Counts
        XssProtection.setTextContent(DOM.query('#stat-albums'), stats.counts.albums_count || 0);
        XssProtection.setTextContent(DOM.query('#stat-cards'), stats.counts.cards_count || 0);
        XssProtection.setTextContent(DOM.query('#stat-libraries'), stats.counts.storage_libraries_count || 0);

        // Storage
        const storage = stats.storage_info;
        const usedGB = (storage.used_storage / (1024 * 1024 * 1024)).toFixed(2);
        const totalGB = (storage.total_storage / (1024 * 1024 * 1024)).toFixed(2);
        
        XssProtection.setTextContent(DOM.query('#storage-percent'), `${storage.usage_percentage}%`);
        const progress = DOM.query('#storage-progress');
        if (progress) progress.style.width = `${storage.usage_percentage}%`;
        
        XssProtection.setTextContent(DOM.query('#storage-used'), `${usedGB} GB`);
        XssProtection.setTextContent(DOM.query('#storage-total'), `من أصل ${totalGB} GB`);

        // Subscription
        const subCard = DOM.query('#subscription-card');
        if (subCard && stats.subscription_info) {
            const info = stats.subscription_info;
            if (info.has_active_subscription && info.subscription_details) {
                const sub = info.subscription_details;
                subCard.className = "p-6 bg-green-50 rounded-2xl border border-green-100 flex flex-col justify-center h-[134px]";
                subCard.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center text-white">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <p class="text-xs text-green-600 font-bold">باقة نشطة</p>
                            <h5 class="text-sm font-bold text-gray-900">${sub.plan?.name || 'خطة غير معروفة'}</h5>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-green-200/50 flex items-center justify-between text-[10px] font-bold text-green-700">
                        <span>تاريخ الانتهاء: ${new Date(sub.ends_at).toLocaleDateString()}</span>
                        <a href="#" class="hover:underline">عرض الفاتورة</a>
                    </div>
                `;
            } else {
                subCard.className = "p-6 bg-red-50 rounded-2xl border border-red-100 flex flex-col justify-center h-[134px]";
                subCard.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center text-white">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <p class="text-xs text-red-600 font-bold">لا يوجد اشتراك</p>
                            <h5 class="text-sm font-bold text-gray-900">الباقة منتهية أو غير موجودة</h5>
                        </div>
                    </div>
                    <div class="mt-4">
                        <x-button variant="primary" class="!py-1.5 !px-4 !text-[10px] w-full">ترقية الآن</x-button>
                    </div>
                `;
            }
        }
    }

    populateForm(school) {
        if (!this.form) return;

        const fields = {
            'school-id': school.school_id,
            'name': school.name,
            'email': school.email || '',
            'phone': school.phone || '',
            'city': school.city || '',
            'address': school.address || '',
            'school_type_id': school.school_type_id || '',
            'school_level_id': school.school_level_id || '',
            'school_status_id': school.school_status_id || ''
        };

        Object.entries(fields).forEach(([fieldId, value]) => {
            const field = document.getElementById(fieldId);
            if (field && value !== undefined && value !== null) {
                field.value = value;
            }
        });

        const credentialsSection = DOM.query('#credentials-section');
        if (credentialsSection) DOM.hide(credentialsSection);
    }

    clearForm() {
        if (!this.form) return;
        this.form.reset();
        const schoolIdInput = document.getElementById('school-id');
        if (schoolIdInput) schoolIdInput.value = '';
        const credentialsSection = DOM.query('#credentials-section');
        if (credentialsSection) DOM.show(credentialsSection);
    }

    disableForm() {
        if (this.form) DOM.disableForm(this.form);
    }

    enableForm() {
        if (this.form) DOM.enableForm(this.form);
    }
}

export default SchoolView;
