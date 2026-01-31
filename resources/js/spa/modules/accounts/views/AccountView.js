/**
 * Account View
 * Handles DOM rendering and UI updates for accounts
 */

import { XssProtection } from '../../../core/security/XssProtection.js';
import { DOM } from '../../../core/utils/dom.js';
import { Formatters } from '../../../core/utils/formatters.js';

export class AccountView {
    constructor(options = {}) {
        this.tbody = options.tbody || DOM.query('#accounts-tbody');
        this.loadingState = options.loadingState || DOM.query('#loading-state');
        this.emptyState = options.emptyState || DOM.query('#empty-state');
        this.modal = options.modal || DOM.query('#account-modal');
        this.modalTitle = options.modalTitle || DOM.query('#account-modal-title');
        this.form = options.form || DOM.query('#account-form');
        this.detailsModal = options.detailsModal || DOM.query('#account-details-modal');
    }

    /**
     * Show loading state
     */
    showLoading() {
        DOM.show(this.loadingState);
        if (this.tbody) {
            DOM.empty(this.tbody);
        }
    }

    /**
     * Hide loading state
     */
    hideLoading() {
        DOM.hide(this.loadingState);
    }

    /**
     * Show empty state
     */
    showEmpty() {
        DOM.show(this.emptyState);
        DOM.hide(this.tbody?.parentElement);
    }

    /**
     * Hide empty state
     */
    hideEmpty() {
        DOM.hide(this.emptyState);
        DOM.show(this.tbody?.parentElement);
    }

    /**
     * Render accounts table (XSS-safe)
     * @param {Array<Account>} accounts - Accounts to render
     */
    render(accounts) {
        if (!this.tbody) return;

        DOM.empty(this.tbody);

        if (!accounts || accounts.length === 0) {
            this.showEmpty();
            return;
        }

        this.hideEmpty();

        accounts.forEach(account => {
            const row = this.createAccountRow(account);
            this.tbody.appendChild(row);
        });
    }

    /**
     * Create account table row (XSS-safe)
     * @param {Account} account - Account data
     * @returns {HTMLElement} - Table row element
     */
    createAccountRow(account) {
        const tr = DOM.create('tr', {
            className: 'hover:bg-gray-50/50 transition-colors duration-200 border-b border-gray-100 last:border-0'
        });

        // Account column
        const accountCell = DOM.create('td', {
            className: 'px-6 py-4'
        });

        const accountDiv = DOM.create('div', {
            className: 'flex items-center gap-3'
        });

        const avatar = DOM.create('div', {
            className: 'w-10 h-10 rounded-full bg-accent/10 flex items-center justify-center text-accent font-bold text-sm'
        });
        const initial = XssProtection.escape(account.full_name?.substring(0, 1)?.toUpperCase() || 'U');
        XssProtection.setTextContent(avatar, initial);

        const infoDiv = DOM.create('div');
        
        const nameDiv = DOM.create('div', {
            className: 'text-sm font-bold text-gray-900'
        });
        XssProtection.setTextContent(nameDiv, account.full_name || 'N/A');

        const usernameDiv = DOM.create('div', {
            className: 'text-xs text-gray-500'
        });
        XssProtection.setTextContent(usernameDiv, `@${account.username || ''}`);

        infoDiv.appendChild(nameDiv);
        infoDiv.appendChild(usernameDiv);
        accountDiv.appendChild(avatar);
        accountDiv.appendChild(infoDiv);
        accountCell.appendChild(accountDiv);

        // Email column
        const emailCell = DOM.create('td', {
            className: 'px-6 py-4 text-sm text-gray-600'
        });
        XssProtection.setTextContent(emailCell, account.email || 'N/A');

        // Role column
        const roleCell = DOM.create('td', {
            className: 'px-6 py-4'
        });
        const roleBadge = DOM.create('span', {
            className: 'px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-600'
        });
        XssProtection.setTextContent(roleBadge, (account.roles && account.roles[0]) ? account.roles[0].name : 'N/A');
        roleCell.appendChild(roleBadge);

        // Status column
        const statusCell = DOM.create('td', {
            className: 'px-6 py-4'
        });
        const statusData = account.status || account.account_status;
        const statusCode = statusData?.code || '';
        const statusName = statusData?.name || 'N/A';

        const statusBadge = DOM.create('span', {
            className: `px-3 py-1 rounded-full text-xs font-bold ${
                statusCode === 'ACTIVE' ? 
                'bg-green-50 text-green-600' : 
                'bg-red-50 text-red-600'
            }`
        });
        XssProtection.setTextContent(statusBadge, statusName);
        statusCell.appendChild(statusBadge);

        // Actions column
        const actionsCell = DOM.create('td', {
            className: 'px-6 py-4'
        });
        
        const actionsDiv = DOM.create('div', {
            className: 'flex items-center justify-center gap-2'
        });

        const viewBtn = DOM.create('button', {
            type: 'button',
            className: 'w-8 h-8 rounded-lg bg-green-50 text-green-500 hover:bg-green-500 hover:text-white transition-all duration-200 flex items-center justify-center shadow-sm',
            title: 'عرض التفاصيل',
            dataset: {
                action: 'view',
                accountId: account.id
            }
        });
        viewBtn.innerHTML = '<i class="fas fa-eye text-xs"></i>';

        const editBtn = DOM.create('button', {
            type: 'button',
            className: 'w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white transition-all duration-200 flex items-center justify-center shadow-sm',
            title: 'تعديل',
            dataset: {
                action: 'edit',
                accountId: account.id
            }
        });
        editBtn.innerHTML = '<i class="fas fa-pen text-xs"></i>';

        const deleteBtn = DOM.create('button', {
            type: 'button',
            className: 'w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-200 flex items-center justify-center shadow-sm',
            title: 'حذف',
            dataset: {
                action: 'delete',
                accountId: account.id
            }
        });
        deleteBtn.innerHTML = '<i class="fas fa-trash text-xs"></i>';

        actionsDiv.appendChild(viewBtn);
        actionsDiv.appendChild(editBtn);
        actionsDiv.appendChild(deleteBtn);
        actionsCell.appendChild(actionsDiv);

        // Append cells (Matching 4 headers in Blade)
        tr.appendChild(accountCell);
        tr.appendChild(emailCell);
        tr.appendChild(statusCell);
        tr.appendChild(actionsCell);

        return tr;
    }

    /**
     * Render pagination UI
     * @param {Object} meta - Pagination metadata
     */
    renderPagination(meta) {
        const paginationContainer = DOM.query('#pagination-container');
        if (!paginationContainer) return;

        if (!meta || meta.last_page <= 1) {
            DOM.empty(paginationContainer);
            return;
        }

        // Standard Laravel-style pagination rendering
        // For simplicity, we can render basic Prev/Next or a full list
        let html = `
            <div class="flex items-center justify-between border-t border-gray-100 px-4 py-3 sm:px-6">
                <div class="flex flex-1 justify-between sm:hidden">
                    ${meta.current_page > 1 ? `<button onclick="accountController.loadPage(${meta.current_page - 1})" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">السابق</button>` : ''}
                    ${meta.current_page < meta.last_page ? `<button onclick="accountController.loadPage(${meta.current_page + 1})" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">التالي</button>` : ''}
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            عرض من <span class="font-medium">${meta.from}</span> إلى <span class="font-medium">${meta.to}</span> من أصل <span class="font-medium">${meta.total}</span> نتيجة
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                            <button onclick="accountController.loadPage(${meta.current_page - 1})" ${meta.current_page === 1 ? 'disabled' : ''} class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            ${this.generatePageNumbers(meta)}
                            <button onclick="accountController.loadPage(${meta.current_page + 1})" ${meta.current_page === meta.last_page ? 'disabled' : ''} class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        `;
        paginationContainer.innerHTML = html;
    }

    generatePageNumbers(meta) {
        let pages = '';
        const current = meta.current_page;
        const last = meta.last_page;
        
        for (let i = 1; i <= last; i++) {
            if (i === 1 || i === last || (i >= current - 2 && i <= current + 2)) {
                pages += `
                    <button onclick="accountController.loadPage(${i})" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold ${i === current ? 'z-10 bg-accent text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent' : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0'}">
                        ${i}
                    </button>
                `;
            } else if (i === current - 3 || i === current + 3) {
                pages += `<span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0">...</span>`;
            }
        }
        return pages;
    }

    /**
     * Open modal for creating/editing
     * @param {string} title - Modal title
     */
    openModal(title = 'إضافة حساب جديد') {
        if (!this.modal) return;

        if (this.modalTitle) {
            XssProtection.setTextContent(this.modalTitle, title);
        }

        DOM.removeClass(this.modal, 'hidden');

        // Already pre-restricted in Blade but safe-guarding
        this.restrictRoleSelection();
    }

    restrictRoleSelection() {
        const roleSelect = DOM.query('#role_id');
        if (!roleSelect) return;

        Array.from(roleSelect.options).forEach(option => {
            const roleName = option.getAttribute('data-role-name');
            if (roleName && roleName !== 'customer') {
                option.disabled = true;
                option.hidden = true;
            }
            if (roleName === 'customer') {
                option.selected = true;
            }
        });
    }

    /**
     * Close modal
     */
    closeModal() {
        if (!this.modal) return;
        
        DOM.addClass(this.modal, 'hidden');
        
        if (this.form) {
            this.form.reset();
        }
    }

    openDetailsModal(account) {
        if (!this.detailsModal) return;

        // Basic Info
        XssProtection.setTextContent(DOM.query('#detail-full_name'), account.full_name || 'N/A');
        XssProtection.setTextContent(DOM.query('#detail-username'), `@${account.username || ''}`);
        XssProtection.setTextContent(DOM.query('#detail-email'), account.email || 'غير متوفر');
        XssProtection.setTextContent(DOM.query('#detail-phone'), account.phone || 'غير متوفر');

        const statusData = account.status || account.account_status;
        const statusBadge = DOM.query('#detail-status');
        if (statusBadge) {
            const statusCode = statusData?.code || '';
            statusBadge.className = `px-3 py-1 rounded-full text-xs font-bold border ${
                statusCode === 'ACTIVE' ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100'
            }`;
            XssProtection.setTextContent(statusBadge, statusData?.name || 'غير محدد');
        }

        const avatarDiv = DOM.query('#detail-avatar');
        if (avatarDiv) {
            const initial = XssProtection.escape(account.full_name?.substring(0, 1)?.toUpperCase() || 'U');
            XssProtection.setTextContent(avatarDiv, initial);
        }

        // Roles
        const rolesDiv = DOM.query('#detail-roles');
        if (rolesDiv) {
            DOM.empty(rolesDiv);
            if (account.roles && account.roles.length > 0) {
                account.roles.forEach(role => {
                    const badge = DOM.create('span', {
                        className: 'px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-600 border border-purple-100'
                    });
                    XssProtection.setTextContent(badge, role.name);
                    rolesDiv.appendChild(badge);
                });
            } else {
                XssProtection.setTextContent(rolesDiv, 'لا توجد أدوار');
            }
        }

        // Associated Entity (Hidden as not applicable for pure customers)
        const entitySection = DOM.query('#associated-entity-section');
        if (entitySection) {
            DOM.hide(entitySection);
        }

        const editBtn = DOM.query('#edit-from-details');
        if (editBtn) {
            editBtn.onclick = () => {
                this.closeDetailsModal();
                const event = new CustomEvent('account:edit', { detail: { accountId: account.id } });
                document.dispatchEvent(event);
            };
        }

        DOM.removeClass(this.detailsModal, 'hidden');
    }

    closeDetailsModal() {
        if (!this.detailsModal) return;
        DOM.addClass(this.detailsModal, 'hidden');
    }

    /**
     * Populate form with account data
     * @param {Account} account - Account data
     */
    populateForm(account) {
        if (!this.form) return;

        const fields = {
            'account-id': account.id,
            'username': account.username,
            'full_name': account.full_name,
            'email': account.email,
            'phone': account.phone,
            'role_id': account.role_id,
            'user_status_id': account.account_status_id,
            'account_type_id': account.account_type_id,
            'city': account.profile?.city || account.city,
            'address': account.profile?.address || account.address
        };

        Object.entries(fields).forEach(([fieldId, value]) => {
            const field = document.getElementById(fieldId);
            if (field && value !== undefined && value !== null) {
                field.value = value;
            }
        });
    }

    /**
     * Clear form fields
     */
    clearForm() {
        if (this.form) {
            this.form.reset();
            // Clear hidden ID field
            const accountId = document.getElementById('account-id');
            if (accountId) accountId.value = '';
        }
    }

    /**
     * Disable form
     */
    disableForm() {
        if (this.form) {
            DOM.disableForm(this.form);
        }
    }

    /**
     * Enable form
     */
    enableForm() {
        if (this.form) {
            DOM.enableForm(this.form);
        }
    }
}

export default AccountView;
