/**
 * XSS Protection Module
 * Provides utilities to sanitize user input and prevent XSS attacks
 */

export class XssProtection {
    /**
     * Escape HTML special characters
     * @param {string} str - The string to escape
     * @returns {string} - Escaped string
     */
    static escape(str) {
        if (str === null || str === undefined) return '';
        
        const entityMap = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
            '/': '&#x2F;',
            '`': '&#x60;',
            '=': '&#x3D;'
        };
        
        return String(str).replace(/[&<>"'`=\/]/g, char => entityMap[char]);
    }

    /**
     * Sanitize HTML content (allows safe tags)
     * @param {string} html - HTML content to sanitize
     * @returns {string} - Sanitized HTML
     */
    static sanitizeHtml(html) {
        if (typeof html !== 'string') return '';
        
        // Create a temporary div
        const temp = document.createElement('div');
        temp.textContent = html;
        
        return temp.innerHTML;
    }

    /**
     * Create a safe text node
     * @param {string} text - Text content
     * @returns {Text} - DOM text node
     */
    static createTextNode(text) {
        return document.createTextNode(String(text || ''));
    }

    /**
     * Set text content safely
     * @param {HTMLElement} element - Target element
     * @param {string} text - Text to set
     */
    static setTextContent(element, text) {
        if (element && element.nodeType === 1) {
            element.textContent = String(text || '');
        }
    }

    /**
     * Set HTML content safely (ONLY for trusted content)
     * @param {HTMLElement} element - Target element
     * @param {string} html - HTML to set
     * @param {boolean} trusted - Whether the content is trusted
     */
    static setHtml(element, html, trusted = false) {
        if (!element || element.nodeType !== 1) return;
        
        if (trusted) {
            element.innerHTML = html;
        } else {
            element.textContent = html;
        }
    }

    /**
     * Sanitize URL to prevent javascript: and data: schemes
     * @param {string} url - URL to sanitize
     * @returns {string} - Safe URL or empty string
     */
    static sanitizeUrl(url) {
        if (typeof url !== 'string') return '';
        
        const trimmed = url.trim().toLowerCase();
        
        // Block dangerous protocols
        const dangerousProtocols = ['javascript:', 'data:', 'vbscript:', 'file:'];
        
        for (const protocol of dangerousProtocols) {
            if (trimmed.startsWith(protocol)) {
                console.warn(`Blocked potentially dangerous URL: ${url}`);
                return '';
            }
        }
        
        return url;
    }

    /**
     * Validate and sanitize object properties
     * @param {Object} obj - Object to sanitize
     * @param {Array<string>} keys - Keys to sanitize
     * @returns {Object} - Sanitized object
     */
    static sanitizeObject(obj, keys = []) {
        if (!obj || typeof obj !== 'object') return {};
        
        const sanitized = { ...obj };
        
        const keysToSanitize = keys.length > 0 ? keys : Object.keys(obj);
        
        keysToSanitize.forEach(key => {
            if (typeof sanitized[key] === 'string') {
                sanitized[key] = this.escape(sanitized[key]);
            }
        });
        
        return sanitized;
    }
}

// Make it globally accessible for legacy code
if (typeof window !== 'undefined') {
    window.XssProtection = XssProtection;
}
