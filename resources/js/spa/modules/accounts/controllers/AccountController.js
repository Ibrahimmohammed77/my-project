/**
 * Account Controller
 * Orchestrates account management logic
 */

import { Account } from '../../../models/Account.js';
import { AccountService } from '../../../services/AccountService.js';
import AccountView from '../views/AccountView.js';
import { Toast } from '../../../core/ui/Toast.js';
import { InputValidator } from '../../../core/security/InputValidator.js';
import { Security } from '../../../core/security/Security.js';
import { DOM } from '../../../core/utils/dom.js';
import { debounce } from '../../../core/utils/performance.js';
import { showErrors, clearErrors } from '../../../utils/toast.js';

export class AccountController {
    constructor() {
        this.accounts = [];
        this.view = new AccountView();
        this.currentAccount = null;
        
        this.init();
    }

    /**
     * Initialize controller
     */
    init() {
        this.attachEventListeners();
        this.loadAccounts();
    }

    /**
     * Attach event listeners
     */
    attachEventListeners() {
        // Search input
        const searchInput = DOM.query('#search');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(() => {
                this.filterAndRender();
            }, 300));
        }

        // Status filter
        const statusFilter = DOM.query('#user_status_id');
        if (statusFilter) {
            statusFilter.addEventListener('change', () => {
                this.filterAndRender();
            });
        }

        // Table actions (using event delegation)
        if (this.view.tbody) {
            DOM.delegate(this.view.tbody, 'click', '[data-action="view"]', (e) => {
                const accountId = parseInt(e.target.closest('button').dataset.accountId);
                this.viewAccount(accountId);
            });

            DOM.delegate(this.view.tbody, 'click', '[data-action="edit"]', (e) => {
                const accountId = parseInt(e.target.closest('button').dataset.accountId);
                this.editAccount(accountId);
            });

            DOM.delegate(this.view.tbody, 'click', '[data-action="delete"]', (e) => {
                const accountId = parseInt(e.target.closest('button').dataset.accountId);
                this.deleteAccount(accountId);
            });
        }

        // Form submit
        if (this.view.form) {
            this.view.form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmit();
            });
        }

    }

    /**
     * Load accounts from server
     */
    async loadAccounts(page = 1) {
        this.view.showLoading();

        try {
            const response = await AccountService.getAll({ page });
            this.accounts = response.items;
            this.metadata = response.meta;
            this.filterAndRender();
        } catch (error) {
            console.error('Failed to load accounts:', error);
            Toast.error('فشل تحميل الحسابات');
        } finally {
            this.view.hideLoading();
        }
    }

    /**
     * Load specific page
     * @param {number} page - Page number
     */
    async loadPage(page) {
        if (page < 1 || (this.metadata && page > this.metadata.last_page)) return;
        await this.loadAccounts(page);
    }

    /**
     * Filter and render accounts
     */
    filterAndRender() {
        const searchInput = DOM.query('#search');
        const statusFilter = DOM.query('#status-filter'); // Fixed ID from #user_status_id

        const searchTerm = searchInput ? searchInput.value.toLowerCase() : '';
        const status = statusFilter ? statusFilter.value : '';

        const filtered = this.accounts.filter(account => {
            const matchesSearch = !searchTerm || 
                (account.username && account.username.toLowerCase().includes(searchTerm)) ||
                (account.full_name && account.full_name.toLowerCase().includes(searchTerm)) ||
                (account.email && account.email.toLowerCase().includes(searchTerm));

            const matchesStatus = !status || (account.account_status_id || account.user_status_id) === parseInt(status);

            return matchesSearch && matchesStatus;
        });

        this.view.render(filtered);
        
        // Update pagination UI if metadata exists
        if (this.metadata) {
            this.view.renderPagination(this.metadata);
        }
    }

    /**
     * Show create modal
     */
    showCreateModal() {
        this.currentAccount = null;
        this.view.clearForm();
        this.view.openModal('إضافة حساب جديد');
        clearErrors();
    }

    /**
     * Edit account
     * @param {number} accountId - Account ID
     */
    async editAccount(accountId) {
        const account = this.accounts.find(a => a.id === accountId);
        
        if (!account) {
            Toast.error('الحساب غير موجود');
            return;
        }

        this.currentAccount = account;
        this.view.populateForm(account);
        

        this.view.openModal('تعديل الحساب');
        clearErrors();
    }

    /**
     * View account details
     * @param {number} accountId - Account ID
     */
    async viewAccount(accountId) {
        const account = this.accounts.find(a => a.id === accountId);
        
        if (!account) {
            Toast.error('الحساب غير موجود');
            return;
        }

        this.view.openDetailsModal(account);
    }

    /**
     * Delete account
     * @param {number} accountId - Account ID
     */
    async deleteAccount(accountId) {
        const account = this.accounts.find(a => a.id === accountId);
        
        if (!account) {
            Toast.error('الحساب غير موجود');
            return;
        }

        if (!confirm(`هل أنت متأكد من حذف حساب "${account.full_name}"؟`)) {
            return;
        }

        Toast.info('جاري حذف الحساب...');

        try {
            await AccountService.delete(accountId);
            
            this.accounts = this.accounts.filter(a => a.id !== accountId);
            this.filterAndRender();
            
            Toast.success('تم حذف الحساب بنجاح');
        } catch (error) {
            console.error('Failed to delete account:', error);
            Toast.error('فشل حذف الحساب');
        }
    }

    /**
     * Handle form submission
     */
    async handleFormSubmit() {
        clearErrors();

        const formData = this.getFormData();
        
        // Validate
        const validation = this.validateFormData(formData);
        if (!validation.valid) {
            showErrors(validation.errors);
            return;
        }

        // Sanitize
        const sanitized = Security.sanitizeInput(formData);

        // Save
        this.view.disableForm();

        try {
            const account = this.currentAccount 
                ? await AccountService.update(this.currentAccount.id, sanitized)
                : await AccountService.create(sanitized);

            if (this.currentAccount) {
                // Update existing
                const index = this.accounts.findIndex(a => a.id === this.currentAccount.id);
                if (index !== -1) {
                    this.accounts[index] = account;
                }
            } else {
                // Add new
                this.accounts.unshift(account);
            }

            this.filterAndRender();
            this.view.closeModal();
            
            Toast.success(this.currentAccount ? 'تم تحديث الحساب بنجاح' : 'تم إنشاء الحساب بنجاح');
            
        } catch (error) {
            console.error('Failed to save account:', error);
            
            if (error.response && error.response.status === 422) {
                showErrors(error.response.data.errors);
            } else {
                Toast.error('فشل حفظ الحساب');
            }
        } finally {
            this.view.enableForm();
        }
    }

    /**
     * Get form data
     * @returns {Object} - Form data
     */
    getFormData() {
        if (!this.view.form) return {};

        return DOM.getFormData(this.view.form);
    }

    /**
     * Validate form data
     * @param {Object} data - Form data
     * @returns {Object} - Validation result
     */
    validateFormData(data) {
        const rules = {
            username: ['required', 'alphanumeric', 'min:3'],
            full_name: ['required', 'min:3'],
            email: ['required', 'email'],
            role_id: ['required'],
            user_status_id: ['required']
        };

        // Add password validation for new accounts
        if (!this.currentAccount) {
            rules.password = ['required', 'min:8'];
        }

        const validator = new InputValidator(rules);
        const isValid = validator.validate(data);

        return {
            valid: isValid,
            errors: validator.getErrors()
        };
    }


    /**
     * Close modal
     */
    closeModal() {
        this.view.closeModal();
        clearErrors();
    }

    closeDetailsModal() {
        this.view.closeDetailsModal();
    }
}

// Make window functions for legacy compatibility
if (typeof window !== 'undefined') {
    window.showCreateAccountModal = function() {
        if (window.accountController) {
            window.accountController.showCreateModal();
        }
    };

    window.closeAccountModal = function() {
        if (window.accountController) {
            window.accountController.closeModal();
        }
    };

    window.closeDetailsModal = function() {
        if (window.accountController) {
            window.accountController.closeDetailsModal();
        }
    };
}

export default AccountController;
