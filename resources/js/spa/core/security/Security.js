/**
 * Security Utilities
 * General security helpers
 */

import { XssProtection } from './XssProtection.js';
import { CsrfProtection } from './CsrfProtection.js';
import { InputValidator } from './InputValidator.js';

export class Security {
    /**
     * Initialize security features
     */
    static init() {
        this.preventClickjacking();
        this.setupCsrfRefresh();
        this.sanitizeExternalLinks();
    }

    /**
     * Prevent clickjacking attacks
     */
    static preventClickjacking() {
        if (window.top !== window.self) {
            console.warn('Potential clickjacking detected');
            window.top.location = window.self.location;
        }
    }

    /**
     * Auto-refresh CSRF token periodically
     */
    static setupCsrfRefresh() {
        // Refresh token every 30 minutes
        setInterval(() => {
            CsrfProtection.refreshToken().catch(err => {
                console.error('Failed to refresh CSRF token:', err);
            });
        }, 30 * 60 * 1000);
    }

    /**
     * Sanitize external links (add rel="noopener noreferrer")
     */
    static sanitizeExternalLinks() {
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            
            if (link && link.hostname !== window.location.hostname) {
                link.setAttribute('rel', 'noopener noreferrer');
                link.setAttribute('target', '_blank');
            }
        });
    }

    /**
     * Sanitize user input before sending to server
     * @param {Object} data - Data object to sanitize
     * @returns {Object} - Sanitized data
     */
    static sanitizeInput(data) {
        if (!data || typeof data !== 'object') return data;

        const sanitized = {};

        for (const [key, value] of Object.entries(data)) {
            if (typeof value === 'string') {
                // Trim whitespace
                sanitized[key] = value.trim();
            } else if (Array.isArray(value)) {
                sanitized[key] = value.map(item => 
                    typeof item === 'string' ? item.trim() : item
                );
            } else {
                sanitized[key] = value;
            }
        }

        return sanitized;
    }

    /**
     * Validate and sanitize form data
     * @param {FormData|Object} formData - Form data
     * @param {Object} rules - Validation rules
     * @returns {Object} - Result with valid flag and data/errors
     */
    static validateAndSanitize(formData, rules = {}) {
        // Convert FormData to object if needed
        let data = formData;
        
        if (formData instanceof FormData) {
            data = {};
            for (const [key, value] of formData.entries()) {
                data[key] = value;
            }
        }

        // Sanitize input
        const sanitized = this.sanitizeInput(data);

        // Validate if rules provided
        if (Object.keys(rules).length > 0) {
            const validator = new InputValidator(rules);
            const isValid = validator.validate(sanitized);

            return {
                valid: isValid,
                data: sanitized,
                errors: validator.getErrors()
            };
        }

        return {
            valid: true,
            data: sanitized,
            errors: {}
        };
    }

    /**
     * Check if content is safe to render as HTML
     * @param {string} content - Content to check
     * @returns {boolean} - Whether content is safe
     */
    static isSafeHtml(content) {
        if (typeof content !== 'string') return false;

        // Check for dangerous patterns
        const dangerousPatterns = [
            /<script/i,
            /javascript:/i,
            /on\w+\s*=/i,  // onclick, onerror, etc.
            /<iframe/i,
            /<object/i,
            /<embed/i
        ];

        return !dangerousPatterns.some(pattern => pattern.test(content));
    }

    /**
     * Generate a random token
     * @param {number} length - Token length
     * @returns {string} - Random token
     */
    static generateToken(length = 32) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let token = '';
        
        const array = new Uint8Array(length);
        crypto.getRandomValues(array);
        
        for (let i = 0; i < length; i++) {
            token += chars[array[i] % chars.length];
        }
        
        return token;
    }
}

// Export all security modules
export { XssProtection, CsrfProtection, InputValidator };

// Make Security globally accessible
if (typeof window !== 'undefined') {
    window.Security = Security;
}
