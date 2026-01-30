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

        actionsDiv.appendChild(editBtn);
        actionsDiv.appendChild(deleteBtn);
        actionsCell.appendChild(actionsDiv);

        // Append all cells
        tr.appendChild(accountCell);
        tr.appendChild(emailCell);
        tr.appendChild(roleCell);
        tr.appendChild(statusCell);
        tr.appendChild(actionsCell);

        return tr;
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

        this.updateConditionalFields('');
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

        // Hide password field for editing
        const passwordField = document.getElementById('password-field');
        if (passwordField) {
            DOM.hide(passwordField);
        }

        // Update conditional fields
        if (account.roles && account.roles[0]) {
            this.updateConditionalFields(account.roles[0].name);
        }
    }

    /**
     * Clear form
     */
    clearForm() {
        if (!this.form) return;
        
        this.form.reset();
        
        const accountIdInput = document.getElementById('account-id');
        if (accountIdInput) {
            accountIdInput.value = '';
        }

        // Show password field for new account
        const passwordField = document.getElementById('password-field');
        if (passwordField) {
            DOM.show(passwordField);
        }

        this.updateConditionalFields('');
    }

    /**
     * Update conditional fields based on role name
     * @param {string} roleName - Role code or name
     */
    updateConditionalFields(roleName) {
        if (!roleName) return;
        
        const studioFields = DOM.query('#studio-fields');
        const schoolFields = DOM.query('#school-fields');
        const subscriberFields = DOM.query('#subscriber-fields');
        
        // Hide all first
        DOM.hide(studioFields);
        DOM.hide(schoolFields);
        DOM.hide(subscriberFields);
        
        const normalizedRole = roleName.toLowerCase();
        
        // Show based on role name
        if (normalizedRole.includes('studio')) {
            DOM.show(studioFields);
        } else if (normalizedRole.includes('school')) {
            DOM.show(schoolFields);
        } else if (normalizedRole.includes('customer') || normalizedRole.includes('subscriber')) {
            DOM.show(subscriberFields);
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
