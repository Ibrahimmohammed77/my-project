/**
 * Accounts Module Entry Point v4.0
 * Unified and optimized accounts management system
 */

import '../../../bootstrap.js';
import { AccountController } from './controllers/AccountController.js';

// Global module instance
let accountController = null;

/**
 * Initialize accounts module with all dependencies
 */
function initAccountsModule() {
    console.log('[Accounts Module] Initializing v4.0...');

    try {
        // Create controller instance
        accountController = new AccountController();

        // Setup global functions for Blade template compatibility
        setupGlobalFunctions();

        // Initialize module
        accountController.init();

        console.log('[Accounts Module] Initialized successfully');
    } catch (error) {
        console.error('[Accounts Module] Initialization failed:', error);
    }
}

/**
 * Setup global functions for legacy Blade compatibility
 */
function setupGlobalFunctions() {
    if (!accountController) return;

    // Primary global functions
    window.accountController = accountController;
    window.showCreateAccountModal = () => accountController.showCreateModal();
    window.closeAccountModal = () => accountController.closeModal();

    // Aliases for backward compatibility
    window.showCreateModal = window.showCreateAccountModal;
    window.closeModal = window.closeAccountModal;

    // Action handlers
    window.editAccount = (id) => accountController.editAccount(id);
    window.deleteAccount = (id) => accountController.deleteAccount(id);

    // Form handlers
    window.handleRoleChange = (el) => accountController.handleRoleChange(el);
    window.updateConditionalFields = (role) =>
        accountController.view.updateConditionalFields(role);
}

// Auto-initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAccountsModule);
} else {
    initAccountsModule();
}

export { accountController, AccountController };
