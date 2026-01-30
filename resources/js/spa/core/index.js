/**
 * Core Framework Entry Point
 * Exports all core modules for easy importing
 */

// Security
export { XssProtection } from './security/XssProtection.js';
export { CsrfProtection } from './security/CsrfProtection.js';
export { InputValidator } from './security/InputValidator.js';
export { Security } from './security/Security.js';

// API
export { default as ApiClient } from './api/ApiClient.js';
export { API_ENDPOINTS, buildUrl, getEndpoint } from './api/endpoints.js';

// UI Components
export { Toast, showToast, showErrors, clearErrors } from './ui/Toast.js';
export { Modal } from './ui/Modal.js';

// Utils
export { DOM } from './utils/dom.js';
export { Formatters } from './utils/formatters.js';
export { debounce, throttle, once, retry } from './utils/performance.js';

/**
 * Initialize core framework
 */
export function initCore() {
    // Initialize security features
    Security.init();
    
    // Initialize toast container
    Toast.init();
    
    console.log('[Core] Framework initialized');
}

// Auto-init on DOM ready
if (typeof window !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCore);
    } else {
        initCore();
    }
}
