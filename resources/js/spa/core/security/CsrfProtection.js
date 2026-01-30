/**
 * CSRF Protection Module
 * Manages CSRF tokens for secure AJAX requests
 */

export class CsrfProtection {
    static TOKEN_META_NAME = 'csrf-token';
    static TOKEN_HEADER_NAME = 'X-CSRF-TOKEN';

    /**
     * Get CSRF token from meta tag
     * @returns {string|null} - CSRF token or null
     */
    static getToken() {
        const metaTag = document.querySelector(`meta[name="${this.TOKEN_META_NAME}"]`);
        
        if (metaTag) {
            return metaTag.getAttribute('content');
        }
        
        console.warn('CSRF token not found in meta tags');
        return null;
    }

    /**
     * Set CSRF token in meta tag
     * @param {string} token - CSRF token to set
     */
    static setToken(token) {
        let metaTag = document.querySelector(`meta[name="${this.TOKEN_META_NAME}"]`);
        
        if (!metaTag) {
            metaTag = document.createElement('meta');
            metaTag.name = this.TOKEN_META_NAME;
            document.head.appendChild(metaTag);
        }
        
        metaTag.content = token;
    }

    /**
     * Refresh CSRF token from server
     * @returns {Promise<string>} - New CSRF token
     */
    static async refreshToken() {
        try {
            const response = await fetch('/csrf-token', {
                method: 'GET',
                credentials: 'same-origin'
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.token) {
                    this.setToken(data.token);
                    return data.token;
                }
            }
        } catch (error) {
            console.error('Failed to refresh CSRF token:', error);
        }
        
        return this.getToken();
    }

    /**
     * Add CSRF token to request headers
     * @param {Object} headers - Headers object
     * @returns {Object} - Headers with CSRF token
     */
    static addTokenToHeaders(headers = {}) {
        const token = this.getToken();
        
        if (token) {
            return {
                ...headers,
                [this.TOKEN_HEADER_NAME]: token
            };
        }
        
        return headers;
    }

    /**
     * Add CSRF token to form data
     * @param {FormData} formData - Form data object
     * @returns {FormData} - Form data with CSRF token
     */
    static addTokenToFormData(formData) {
        const token = this.getToken();
        
        if (token && formData instanceof FormData) {
            formData.append('_token', token);
        }
        
        return formData;
    }

    /**
     * Verify CSRF token is present
     * @returns {boolean} - Whether token exists
     */
    static hasToken() {
        return !!this.getToken();
    }
}

// Make it globally accessible
if (typeof window !== 'undefined') {
    window.CsrfProtection = CsrfProtection;
}
