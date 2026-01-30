/**
 * Accounts Module Entry Point
 * Initializes the accounts management system
 */

import '../../../bootstrap.js';
import AccountController from './controllers/AccountController.js';

// Initialize when DOM is ready
let accountController = null;

function initAccountsModule() {
    console.log('[Accounts Module] Initializing...');
    
    accountController = new AccountController();
    
    // Make globally accessible for legacy Blade onclick handlers
    window.accountController = accountController;
    window.showCreateAccountModal = () => accountController.showCreateModal();
    window.closeAccountModal = () => accountController.closeModal();
    
    // Alises for backward compatibility with Blade index
    window.showCreateModal = window.showCreateAccountModal;
    window.closeModal = window.closeAccountModal;
    window.editAccount = (id) => accountController.editAccount(id);
    window.deleteAccount = (id) => accountController.deleteAccount(id);
    window.handleRoleChange = (el) => accountController.handleRoleChange(el);
    window.updateConditionalFields = (role) => accountController.view.updateConditionalFields(role);
    
    console.log('[Accounts Module] Initialized successfully');
}

// Auto-init on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAccountsModule);
} else {
    initAccountsModule();
}

export { accountController, AccountController };
