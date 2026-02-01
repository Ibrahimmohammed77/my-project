/**
 * API Client
 * Centralized Axios instance with interceptors and error handling
 */

import axios from 'axios';
import { CsrfProtection } from '../security/CsrfProtection.js';

export class ApiClient {
    constructor(config = {}) {
        this.client = axios.create({
            baseURL: config.baseURL || '',
            timeout: config.timeout || 30000,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            ...config
        });

        this.setupInterceptors();
    }

    /**
     * Setup request and response interceptors
     */
    setupInterceptors() {
        // Request interceptor
        this.client.interceptors.request.use(
            (config) => {
                // Add CSRF token
                const csrfToken = CsrfProtection.getToken();
                if (csrfToken) {
                    config.headers['X-CSRF-TOKEN'] = csrfToken;
                }

                // Add cache-busting timestamp for GET requests
                if (config.method === 'get') {
                    config.params = {
                        ...config.params,
                        _t: Date.now()
                    };
                }

                // Log request in development
                if (process.env.NODE_ENV === 'development') {
                    console.log(`[API] ${config.method.toUpperCase()} ${config.url}`, config.data);
                }

                return config;
            },
            (error) => {
                console.error('[API] Request error:', error);
                return Promise.reject(error);
            }
        );

        // Response interceptor
        this.client.interceptors.response.use(
            (response) => {
                // Log response in development
                if (process.env.NODE_ENV === 'development') {
                    console.log('[API] Response:', response.data);
                }

                return response;
            },
            async (error) => {
                const originalRequest = error.config;

                // Handle CSRF token mismatch (419)
                if (error.response?.status === 419 && !originalRequest._retry) {
                    originalRequest._retry = true;
                    
                    try {
                        await CsrfProtection.refreshToken();
                        return this.client(originalRequest);
                    } catch (refreshError) {
                        console.error('[API] Failed to refresh CSRF token');
                        return Promise.reject(refreshError);
                    }
                }

                // Handle unauthorized (401)
                if (error.response?.status === 401) {
                    console.warn('[API] Unauthorized - redirecting to login');
                    window.location.href = '/login';
                    return Promise.reject(error);
                }

                // Handle forbidden (403)
                if (error.response?.status === 403) {
                    console.error('[API] Forbidden - insufficient permissions');
                }

                // Handle not found (404)
                if (error.response?.status === 404) {
                    console.error('[API] Resource not found');
                }

                // Handle validation errors (422)
                if (error.response?.status === 422) {
                    console.warn('[API] Validation errors:', error.response.data.errors);
                }

                // Handle server errors (500+)
                if (error.response?.status >= 500) {
                    console.error('[API] Server error:', error.response?.data?.message);
                }

                console.error('[API] Error:', error);
                return Promise.reject(error);
            }
        );
    }

    /**
     * Make a GET request
     * @param {string} url - Request URL
     * @param {Object} config - Axios config
     * @returns {Promise} - Response promise
     */
    async get(url, config = {}) {
        return this.client.get(url, config);
    }

    /**
     * Make a POST request
     * @param {string} url - Request URL
     * @param {Object} data - Request data
     * @param {Object} config - Axios config
     * @returns {Promise} - Response promise
     */
    async post(url, data = {}, config = {}) {
        return this.client.post(url, data, config);
    }

    /**
     * Make a PUT request
     * @param {string} url - Request URL
     * @param {Object} data - Request data
     * @param {Object} config - Axios config
     * @returns {Promise} - Response promise
     */
    async put(url, data = {}, config = {}) {
        return this.client.put(url, data, config);
    }

    /**
     * Make a PATCH request
     * @param {string} url - Request URL
     * @param {Object} data - Request data
     * @param {Object} config - Axios config
     * @returns {Promise} - Response promise
     */
    async patch(url, data = {}, config = {}) {
        return this.client.patch(url, data, config);
    }

    /**
     * Make a DELETE request
     * @param {string} url - Request URL
     * @param {Object} config - Axios config
     * @returns {Promise} - Response promise
     */
    async delete(url, config = {}) {
        return this.client.delete(url, config);
    }

    /**
     * Upload file
     * @param {string} url - Upload URL
     * @param {FormData} formData - Form data with file
     * @param {Function} onProgress - Progress callback
     * @returns {Promise} - Response promise
     */
    async upload(url, formData, onProgress = null) {
        const config = {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        };

        if (onProgress) {
            config.onUploadProgress = (progressEvent) => {
                const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                onProgress(percentCompleted);
            };
        }

        return this.client.post(url, formData, config);
    }

    /**
     * Cancel all pending requests
     */
    cancelAllRequests() {
        // Implementation for request cancellation
        console.log('[API] Cancelling all pending requests');
    }
}

// Create default instance
const apiClient = new ApiClient();

// Make it globally accessible
if (typeof window !== 'undefined') {
    window.ApiClient = apiClient;
}

export default apiClient;
