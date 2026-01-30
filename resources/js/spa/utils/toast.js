/**
 * Legacy Toast Utils
 * Bridge to new core Toast system for backward compatibility
 */

import { Toast } from '../core/ui/Toast.js';
import { DOM } from '../core/utils/dom.js';

/**
 * Show toast notification (legacy compatibility)
 * @param {string} message - Toast message
 * @param {string} type - Toast type
 */
export function showToast(message, type = 'success') {
    Toast.show(message, type);
}

/**
 * Clear all field errors
 */
export function clearErrors() {
    // Clear inline error messages
    DOM.queryAll('.field-error').forEach(el => {
        el.textContent = '';
        DOM.hide(el);
    });
    
    // Remove error styles from inputs
    DOM.queryAll('input, select, textarea').forEach(el => {
        DOM.removeClass(el, ['border-red-500', 'focus:border-red-500', 'focus:ring-red-500/20']);
    });
}

/**
 * Show validation errors
 * @param {Object} errors - Errors object from Laravel
 */
export function showErrors(errors) {
    clearErrors();
    
    if (!errors || typeof errors !== 'object') {
        return;
    }

    // Display errors: { field_name: ['Error message 1'] }
    for (const [field, messages] of Object.entries(errors)) {
        if (!Array.isArray(messages) || messages.length === 0) continue;

        // Find and show error element
        const errorEl = document.getElementById(`${field}-error`);
        if (errorEl) {
            errorEl.textContent = messages[0];
            DOM.show(errorEl);
        }

        // Highlight input field
        const inputEl = document.getElementById(field);
        if (inputEl) {
            DOM.addClass(inputEl, ['border-red-500', 'focus:border-red-500', 'focus:ring-red-500/20']);
            
            // Clear error on input
            const clearError = () => {
                DOM.removeClass(inputEl, ['border-red-500', 'focus:border-red-500', 'focus:ring-red-500/20']);
                if (errorEl) {
                    DOM.hide(errorEl);
                    errorEl.textContent = '';
                }
                inputEl.removeEventListener('input', clearError);
            };
            
            inputEl.addEventListener('input', clearError);
        }

        // Also show as toast for important errors
        if (messages.length > 0) {
            Toast.error(messages[0], { duration: 7000 });
        }
    }
}

// Make globally available for legacy code
if (typeof window !== 'undefined') {
    window.showToast = showToast;
    window.showErrors = showErrors;
    window.clearErrors = clearErrors;
}

