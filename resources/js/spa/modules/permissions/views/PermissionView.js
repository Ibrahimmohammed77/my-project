import { DOM } from '../../../core/utils/dom.js';
import { XssProtection } from '../../../core/security/XssProtection.js';

export class PermissionView {
    constructor() {
        this.tbody = document.getElementById('permissions-tbody');
        this.loadingState = document.getElementById('loading-state');
        this.emptyState = document.getElementById('empty-state');
        this.searchInput = document.getElementById('permissions-search');
        this.modal = document.getElementById('permission-modal');
        this.form = document.getElementById('permission-form');
        this.modalTitle = document.getElementById('modal-title');
    }

    showLoading() {
        if (!this.tbody) return;
        this.tbody.innerHTML = '';
        this.loadingState?.classList.remove('hidden');
        this.emptyState?.classList.add('hidden');
    }

    hideLoading() {
        this.loadingState?.classList.add('hidden');
    }

    showEmpty() {
        this.emptyState?.classList.remove('hidden');
        if (this.tbody) this.tbody.innerHTML = '';
    }

    hideEmpty() {
        this.emptyState?.classList.add('hidden');
    }

    renderPermissions(permissions) {
        if (!this.tbody) return;

        if (permissions.length === 0) {
            this.showEmpty();
            return;
        }

        this.hideEmpty();
        this.tbody.innerHTML = '';

        permissions.forEach(perm => {
            const row = this.createPermissionRow(perm);
            this.tbody.appendChild(row);
        });

        if (window['permissions_initPagination']) {
            window['permissions_initPagination']();
        }
    }

    createPermissionRow(perm) {
        const tr = DOM.create('tr', { className: 'hover:bg-gray-50/80 transition-colors group' });

        // Name & Resource column
        const nameCell = DOM.create('td', { className: 'px-6 py-4' });
        const nameDiv = DOM.create('div', { className: 'flex items-center gap-4' });
        
        const iconDiv = DOM.create('div', {
            className: `h-12 w-12 rounded-2xl ${this.getResourceColor(perm.resource_type)} flex items-center justify-center font-bold text-lg shrink-0 shadow-sm border border-white`
        });
        const icon = DOM.create('i', { className: `fas ${this.getResourceIcon(perm.resource_type)}` });
        iconDiv.appendChild(icon);

        const infoDiv = DOM.create('div');
        const nameSpan = DOM.create('div', { className: 'font-bold text-gray-900 text-sm mb-1' });
        XssProtection.setTextContent(nameSpan, perm.name);
        
        const metaDiv = DOM.create('div', { className: 'flex items-center gap-1.5' });
        const idSpan = DOM.create('span', { className: 'text-[10px] text-gray-400 font-mono bg-gray-50 px-1.5 rounded border border-gray-100' });
        XssProtection.setTextContent(idSpan, `ID: ${perm.permission_id}`);
        const resourceSpan = DOM.create('span', { className: 'text-[11px] text-gray-400' });
        XssProtection.setTextContent(resourceSpan, perm.resource_type);
        
        metaDiv.appendChild(idSpan);
        metaDiv.appendChild(resourceSpan);
        infoDiv.appendChild(nameSpan);
        infoDiv.appendChild(metaDiv);
        nameDiv.appendChild(iconDiv);
        nameDiv.appendChild(infoDiv);
        nameCell.appendChild(nameDiv);

        // Action column
        const actionCell = DOM.create('td', { className: 'px-6 py-4' });
        const actionBadge = DOM.create('span', {
            className: `inline-flex items-center gap-2 px-3 py-1.5 rounded-xl text-xs font-bold ${this.getActionClass(perm.action)} border border-current/10 shadow-sm`
        });
        const actionIcon = DOM.create('i', { className: `fas ${this.getActionIcon(perm.action)} text-xs` });
        actionBadge.appendChild(actionIcon);
        const actionText = DOM.create('span', { className: 'uppercase tracking-wider' });
        XssProtection.setTextContent(actionText, perm.action);
        actionBadge.appendChild(actionText);
        actionCell.appendChild(actionBadge);

        // Description column
        const descCell = DOM.create('td', { className: 'px-6 py-4' });
        const descContainer = DOM.create('div', { className: 'max-w-xs' });
        const descP = DOM.create('p', {
            className: 'text-sm text-gray-600 leading-relaxed line-clamp-2',
            title: perm.description
        });
        if (perm.description) {
            XssProtection.setTextContent(descP, perm.description);
        } else {
            const noDesc = DOM.create('span', { className: 'text-gray-400 italic font-light' });
            XssProtection.setTextContent(noDesc, 'لا يوجد وصف متاح لهذه الصلاحية');
            descP.appendChild(noDesc);
        }
        descContainer.appendChild(descP);
        descCell.appendChild(descContainer);

        // Actions column
        const actionsCell = DOM.create('td', { className: 'px-6 py-4' });
        const actionsDiv = DOM.create('div', {
            className: 'flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-all duration-300 transform translate-y-2 group-hover:translate-y-0'
        });

        const editBtn = DOM.create('button', {
            className: 'h-9 w-9 rounded-xl bg-white border border-gray-200 text-blue-600 hover:bg-blue-50 hover:border-blue-200 flex items-center justify-center transition-all shadow-sm hover:shadow-md',
            title: 'تعديل',
            onclick: () => window.editPermission?.(perm.permission_id)
        });
        const editIcon = DOM.create('i', { className: 'fas fa-pen text-xs' });
        editBtn.appendChild(editIcon);

        const deleteBtn = DOM.create('button', {
            className: 'h-9 w-9 rounded-xl bg-white border border-gray-200 text-red-600 hover:bg-red-50 hover:border-red-200 flex items-center justify-center transition-all shadow-sm hover:shadow-md',
            title: 'حذف',
            onclick: () => window.deletePermission?.(perm.permission_id)
        });
        const deleteIcon = DOM.create('i', { className: 'fas fa-trash text-xs' });
        deleteBtn.appendChild(deleteIcon);

        actionsDiv.appendChild(editBtn);
        actionsDiv.appendChild(deleteBtn);
        actionsCell.appendChild(actionsDiv);

        tr.appendChild(nameCell);
        tr.appendChild(actionCell);
        tr.appendChild(descCell);
        tr.appendChild(actionsCell);

        return tr;
    }

    getActionClass(action) {
        const map = {
            'create': 'bg-green-50 text-green-700',
            'read': 'bg-blue-50 text-blue-700',
            'update': 'bg-orange-50 text-orange-700',
            'delete': 'bg-red-50 text-red-700',
            'manage': 'bg-purple-50 text-purple-700'
        };
        return map[action.toLowerCase()] || 'bg-gray-100 text-gray-700';
    }

    getActionIcon(action) {
        const map = {
            'create': 'fa-plus',
            'read': 'fa-eye',
            'update': 'fa-pen-to-square',
            'delete': 'fa-trash-can',
            'manage': 'fa-sliders'
        };
        return map[action.toLowerCase()] || 'fa-circle';
    }

    getResourceIcon(resource) {
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

    getResourceColor(resource) {
        const map = {
            'accounts': 'bg-blue-100 text-blue-600',
            'roles': 'bg-purple-100 text-purple-600',
            'permissions': 'bg-amber-100 text-amber-600',
            'logs': 'bg-gray-100 text-gray-600',
            'settings': 'bg-slate-100 text-slate-600'
        };
        return map[resource.toLowerCase()] || 'bg-indigo-50 text-indigo-600';
    }

    openModal(title) {
        if (this.modalTitle) {
            this.modalTitle.innerHTML = title;
        }
        this.modal?.classList.remove('hidden');
    }

    closeModal() {
        this.modal?.classList.add('hidden');
        this.form?.reset();
    }

    populateForm(perm) {
        if (!this.form) return;

        const fields = {
            'permission-id': perm.permission_id,
            'name': perm.name,
            'resource_type': perm.resource_type,
            'action': perm.action,
            'description': perm.description || ''
        };

        Object.entries(fields).forEach(([fieldId, value]) => {
            const field = document.getElementById(fieldId);
            if (field && value !== undefined && value !== null) {
                field.value = value;
            }
        });
    }

    showError(message = 'حدث خطأ أثناء تحميل البيانات') {
        if (this.loadingState) {
            this.loadingState.innerHTML = `<p class="text-red-500 text-center py-4">${XssProtection.escape(message)}</p>`;
        }
    }
}

export default PermissionView;
