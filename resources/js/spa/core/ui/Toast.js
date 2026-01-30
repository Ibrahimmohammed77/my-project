/**
 * Toast Notification System
 * Enhanced toast with queue management and customization
 */

import { XssProtection } from '../security/XssProtection.js';

export class Toast {
    static container = null;
    static queue = [];
    static activeToasts = new Set();
    static maxToasts = 3;

    /**
     * Initialize toast container
     */
    static init() {
        if (this.container) return;

        this.container = document.createElement('div');
        this.container.id = 'toast-container';
        this.container.className = 'fixed top-4 left-4 z-[9999] flex flex-col gap-3 max-w-md';
        document.body.appendChild(this.container);
    }

    /**
     * Show a toast notification
     * @param {string} message - Toast message
     * @param {string} type - Toast type (success|error|warning|info)
     * @param {Object} options - Toast options
     */
    static show(message, type = 'info', options = {}) {
        this.init();

        const config = {
            duration: options.duration || 5000,
            dismissible: options.dismissible !== false,
            position: options.position || 'top-left',
            onClose: options.onClose || null,
            ...options
        };

        if (this.activeToasts.size >= this.maxToasts) {
            this.queue.push({ message, type, config });
            return;
        }

        this.createToast(message, type, config);
    }

    /**
     * Create and display a toast
     * @param {string} message - Toast message
     * @param {string} type - Toast type
     * @param {Object} config - Toast configuration
     */
    static createToast(message, type, config) {
        const toast = document.createElement('div');
        const toastId = `toast-${Date.now()}-${Math.random()}`;
        toast.id = toastId;
        toast.className = this.getToastClasses(type);
        
        const icon = this.getIcon(type);
        const safeMessage = XssProtection.escape(message);

        toast.innerHTML = `
            <div class="flex items-start gap-3">
                <div class="flex-shrink-0 mt-0.5">
                    ${icon}
                </div>
                <div class="flex-1 text-sm font-medium">${safeMessage}</div>
                ${config.dismissible ? `
                    <button type="button" class="flex-shrink-0 ml-2 text-current opacity-70 hover:opacity-100 transition-opacity" data-dismiss="toast">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                ` : ''}
            </div>
        `;

        // Add to container with animation
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(-20px)';
        this.container.appendChild(toast);

        // Trigger animation
        requestAnimationFrame(() => {
            toast.style.transition = 'all 0.3s ease-out';
            toast.style.opacity = '1';
            toast.style.transform = 'translateX(0)';
        });

        this.activeToasts.add(toastId);

        // Dismiss button handler
        if (config.dismissible) {
            const dismissBtn = toast.querySelector('[data-dismiss="toast"]');
            dismissBtn?.addEventListener('click', () => this.remove(toastId, config.onClose));
        }

        // Auto-dismiss
        if (config.duration > 0) {
            setTimeout(() => this.remove(toastId, config.onClose), config.duration);
        }
    }

    /**
     * Remove a toast
     * @param {string} toastId - Toast ID
     * @param {Function} onClose - Close callback
     */
    static remove(toastId, onClose = null) {
        const toast = document.getElementById(toastId);
        if (!toast) return;

        toast.style.opacity = '0';
        toast.style.transform = 'translateX(-20px)';

        setTimeout(() => {
            toast.remove();
            this.activeToasts.delete(toastId);

            if (onClose) onClose();

            // Show next toast from queue
            if (this.queue.length > 0) {
                const { message, type, config } = this.queue.shift();
                this.createToast(message, type, config);
            }
        }, 300);
    }

    /**
     * Get toast CSS classes
     * @param {string} type - Toast type
     * @returns {string} - CSS classes
     */
    static getToastClasses(type) {
        const baseClasses = 'relative px-4 py-3 rounded-xl shadow-lg backdrop-blur-sm border transform transition-all duration-300';
        
        const typeClasses = {
            success: 'bg-green-50 text-green-800 border-green-200',
            error: 'bg-red-50 text-red-800 border-red-200',
            warning: 'bg-orange-50 text-orange-800 border-orange-200',
            info: 'bg-blue-50 text-blue-800 border-blue-200'
        };

        return `${baseClasses} ${typeClasses[type] || typeClasses.info}`;
    }

    /**
     * Get icon for toast type
     * @param {string} type - Toast type
     * @returns {string} - Icon HTML
     */
    static getIcon(type) {
        const icons = {
            success: '<i class="fas fa-circle-check text-green-600 text-lg"></i>',
            error: '<i class="fas fa-circle-xmark text-red-600 text-lg"></i>',
            warning: '<i class="fas fa-triangle-exclamation text-orange-600 text-lg"></i>',
            info: '<i class="fas fa-circle-info text-blue-600 text-lg"></i>'
        };

        return icons[type] || icons.info;
    }

    /**
     * Shorthand methods
     */
    static success(message, options = {}) {
        return this.show(message, 'success', options);
    }

    static error(message, options = {}) {
        return this.show(message, 'error', options);
    }

    static warning(message, options = {}) {
        return this.show(message, 'warning', options);
    }

    static info(message, options = {}) {
        return this.show(message, 'info', options);
    }

    /**
     * Clear all toasts
     */
    static clearAll() {
        this.activeToasts.forEach(toastId => {
            const toast = document.getElementById(toastId);
            toast?.remove();
        });
        this.activeToasts.clear();
        this.queue = [];
    }
}

// Legacy compatibility
export function showToast(message, type = 'info') {
    Toast.show(message, type);
}

export function showErrors(errors) {
    if (typeof errors === 'object') {
        Object.values(errors).flat().forEach(error => {
            Toast.error(error);
        });
    } else {
        Toast.error(errors);
    }
}

export function clearErrors() {
    // Legacy - can be removed
}

// Make it globally accessible
if (typeof window !== 'undefined') {
    window.Toast = Toast;
    window.showToast = showToast;
    window.showErrors = showErrors;
    window.clearErrors = clearErrors;
}
