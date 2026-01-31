import { DOM } from '../../../core/utils/dom.js';
import { XssProtection } from '../../../core/security/XssProtection.js';

export class RoleView {
    constructor() {
        this.tbody = document.getElementById('roles-tbody');
        this.loadingState = document.getElementById('loading-state');
        this.emptyState = document.getElementById('empty-state');
        this.searchInput = document.getElementById('roles-search');
        this.modal = document.getElementById('role-modal');
        this.form = document.getElementById('role-form');
        this.modalTitle = document.getElementById('modal-title');
    }

    /**
     * Show loading state
     */
    showLoading() {
        if (!this.tbody) return;
        this.tbody.innerHTML = '';
        this.loadingState?.classList.remove('hidden');
        this.emptyState?.classList.add('hidden');
    }

    /**
     * Hide loading state
     */
    hideLoading() {
        this.loadingState?.classList.add('hidden');
    }

    /**
     * Show empty state
     */
    showEmpty() {
        this.emptyState?.classList.remove('hidden');
        if (this.tbody) this.tbody.innerHTML = '';
    }

    /**
     * Hide empty state
     */
    hideEmpty() {
        this.emptyState?.classList.add('hidden');
    }

    /**
     * Render roles list
     * @param {Array<Role>} roles
     */
    renderRoles(roles) {
        if (!this.tbody) return;

        if (roles.length === 0) {
            this.showEmpty();
            return;
        }

        this.hideEmpty();

        // Clear tbody
        this.tbody.innerHTML = '';

        roles.forEach(role => {
            const row = this.createRoleRow(role);
            this.tbody.appendChild(row);
        });

        // Initialize pagination
        if (window['roles_initPagination']) {
            window['roles_initPagination']();
        }
    }

    /**
     * Create role table row
     * @param {Role} role
     * @returns {HTMLTableRowElement}
     */
    createRoleRow(role) {
        const tr = DOM.create('tr', {
            className: 'hover:bg-gray-50/80 transition-colors group'
        });

        // Name column with icon
        const nameCell = DOM.create('td', { className: 'px-6 py-4' });
        const nameDiv = DOM.create('div', { className: 'flex items-center gap-4' });
        
        const iconDiv = DOM.create('div', {
            className: `h-12 w-12 rounded-2xl ${role.is_system ? 'bg-purple-100 text-purple-600' : 'bg-blue-100 text-blue-600'} flex items-center justify-center font-bold text-lg shrink-0 shadow-sm border border-white`
        });
        const icon = DOM.create('i', {
            className: `fas ${role.is_system ? 'fa-shield-halved' : 'fa-user-shield'}`
        });
        iconDiv.appendChild(icon);

        const infoDiv = DOM.create('div');
        const nameLine = DOM.create('div', { className: 'flex items-center gap-2' });
        const nameSpan = DOM.create('span', { className: 'font-bold text-gray-900 text-sm' });
        XssProtection.setTextContent(nameSpan, role.name);
        nameLine.appendChild(nameSpan);

        if (role.is_system) {
            const systemBadge = DOM.create('span', {
                className: 'inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-100'
            });
            const lockIcon = DOM.create('i', { className: 'fas fa-lock text-[9px]' });
            systemBadge.appendChild(lockIcon);
            XssProtection.setTextContent(systemBadge, ' نظامي', true);
            nameLine.appendChild(systemBadge);
        }

        const idDiv = DOM.create('div', { className: 'text-[11px] text-gray-400 mt-0.5 font-mono' });
        XssProtection.setTextContent(idDiv, `ID: ${role.role_id}`);

        infoDiv.appendChild(nameLine);
        infoDiv.appendChild(idDiv);
        nameDiv.appendChild(iconDiv);
        nameDiv.appendChild(infoDiv);
        nameCell.appendChild(nameDiv);

        // Description column
        const descCell = DOM.create('td', { className: 'px-6 py-4' });
        const descContainer = DOM.create('div', { className: 'max-w-md' });
        const descP = DOM.create('p', {
            className: 'text-sm text-gray-600 leading-relaxed line-clamp-2',
            title: role.description
        });
        
        if (role.description) {
            XssProtection.setTextContent(descP, role.description);
        } else {
            const noDesc = DOM.create('span', { className: 'text-gray-400 italic font-light' });
            XssProtection.setTextContent(noDesc, 'لا يوجد وصف متاح لهذا الدور');
            descP.appendChild(noDesc);
        }
        
        descContainer.appendChild(descP);
        descCell.appendChild(descContainer);

        // Permissions column
        const permCell = DOM.create('td', { className: 'px-6 py-4' });
        const permDiv = DOM.create('div', { className: 'flex items-center gap-2' });
        
        const permIconContainer = DOM.create('div', { className: 'flex -space-x-2 space-x-reverse overflow-hidden p-1' });
        const permIconCircle = DOM.create('div', {
            className: 'inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 border-2 border-white text-gray-400 text-xs shadow-sm'
        });
        const keyIcon = DOM.create('i', { className: 'fas fa-key' });
        permIconCircle.appendChild(keyIcon);
        permIconContainer.appendChild(permIconCircle);

        const permBadge = DOM.create('span', {
            className: 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold bg-gray-50 text-gray-700 border border-gray-200 shadow-sm'
        });
        const statusDot = DOM.create('span', {
            className: `w-1.5 h-1.5 rounded-full ${role.permissionCount > 0 ? 'bg-green-500' : 'bg-gray-400'}`
        });
        permBadge.appendChild(statusDot);
        XssProtection.setTextContent(permBadge, ` ${role.permissionCount} صلاحية`, true);

        permDiv.appendChild(permIconContainer);
        permDiv.appendChild(permBadge);
        permCell.appendChild(permDiv);

        // Actions column
        const actionsCell = DOM.create('td', { className: 'px-6 py-4' });
        
        if (role.isEditable) {
            const actionsDiv = DOM.create('div', {
                className: 'flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0'
            });

            const editBtn = DOM.create('button', {
                className: 'h-9 w-9 rounded-xl bg-white border border-gray-200 text-blue-600 hover:bg-blue-50 hover:border-blue-200 flex items-center justify-center transition-all shadow-sm hover:shadow-md',
                title: 'تعديل',
                onclick: () => window.editRole?.(role.role_id)
            });
            const editIcon = DOM.create('i', { className: 'fas fa-pen text-xs' });
            editBtn.appendChild(editIcon);

            const deleteBtn = DOM.create('button', {
                className: 'h-9 w-9 rounded-xl bg-white border border-gray-200 text-red-600 hover:bg-red-50 hover:border-red-200 flex items-center justify-center transition-all shadow-sm hover:shadow-md',
                title: 'حذف',
                onclick: () => window.deleteRole?.(role.role_id)
            });
            const deleteIcon = DOM.create('i', { className: 'fas fa-trash text-xs' });
            deleteBtn.appendChild(deleteIcon);

            actionsDiv.appendChild(editBtn);
            actionsDiv.appendChild(deleteBtn);
            actionsCell.appendChild(actionsDiv);
        } else {
            const lockedDiv = DOM.create('div', { className: 'flex justify-center' });
            const lockedBadge = DOM.create('span', {
                className: 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-medium bg-gray-50 text-gray-400 border border-gray-100 opacity-50 cursor-not-allowed'
            });
            const lockIcon2 = DOM.create('i', { className: 'fas fa-lock text-[10px]' });
            lockedBadge.appendChild(lockIcon2);
            XssProtection.setTextContent(lockedBadge, ' محمي', true);
            lockedDiv.appendChild(lockedBadge);
            actionsCell.appendChild(lockedDiv);
        }

        tr.appendChild(nameCell);
        tr.appendChild(descCell);
        tr.appendChild(permCell);
        tr.appendChild(actionsCell);

        return tr;
    }

    /**
     * Show modal for create/edit
     * @param {string} title
     */
    openModal(title) {
        if (this.modalTitle) {
            // Support both HTML-wrapped titles and plain text
            if (typeof title === 'string' && title.includes('<')) {
                XssProtection.setHtml(this.modalTitle, title, true);
            } else {
                XssProtection.setTextContent(this.modalTitle, title);
            }
        }
        this.modal?.classList.remove('hidden');
    }

    /**
     * Close modal
     */
    closeModal() {
        this.modal?.classList.add('hidden');
        this.form?.reset();
    }

    /**
     * Populate form with role data
     * @param {Role} role
     */
    populateForm(role) {
        if (!this.form) return;

        const fields = {
            'role-id': role.role_id,
            'name': role.name,
            'description': role.description || ''
        };

        Object.entries(fields).forEach(([fieldId, value]) => {
            const field = document.getElementById(fieldId);
            if (field && value !== undefined && value !== null) {
                field.value = value;
            }
        });
    }

    /**
     * Show error in loading state
     * @param {string} message
     */
    showError(message = 'حدث خطأ أثناء تحميل البيانات') {
        if (this.loadingState) {
            this.loadingState.innerHTML = `<p class="text-red-500 text-center py-4">${XssProtection.escape(message)}</p>`;
        }
    }
}

export default RoleView;
