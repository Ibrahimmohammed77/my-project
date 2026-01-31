/**
 * Toast Helper Functions
 * Wrapper utilities for inline error handling
 * Uses the canonical Toast from core/ui/Toast.js
 */

import { Toast } from '../core/ui/Toast.js';

/**
 * Display multiple validation errors
 * @param {Object} errors - Errors object from Laravel validation
 */
export function showErrors(errors) {
    if (!errors || typeof errors !== 'object') {
        return;
    }

    // Clear any existing inline errors first
    clearErrors();

    // Display each error as a Toast
    Object.entries(errors).forEach(([field, messages]) => {
        if (Array.isArray(messages)) {
            messages.forEach(message => {
                Toast.error(message);
                
                // Also set inline error if field exists
                const input = document.querySelector(`[name="${field}"]`);
                if (input) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'text-red-500 text-xs mt-1';
                    errorDiv.textContent = message;
                    errorDiv.dataset.error = field;
                    
                    input.classList.add('border-red-500');
                    input.parentNode.appendChild(errorDiv);
                }
            });
        }
    });
}

/**
 * Clear all inline validation errors
 */
export function clearErrors() {
    // Remove all error divs
    document.querySelectorAll('[data-error]').forEach(el => el.remove());
    
    // Remove error styling from inputs
    document.querySelectorAll('.border-red-500').forEach(input => {
        input.classList.remove('border-red-500');
    });
}

/**
 * Display a toast message (legacy wrapper)
 * @param {string} message 
 * @param {string} type 'success' | 'error' | 'info' | 'warning'
 */
export function showToast(message, type = 'success') {
    if (typeof Toast[type] === 'function') {
        Toast[type](message);
    } else {
        Toast.success(message);
    }
}
