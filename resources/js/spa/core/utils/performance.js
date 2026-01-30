/**
 * Performance Utilities
 * Debounce, throttle, and other performance helpers
 */

/**
 * Debounce function calls
 * @param {Function} func - Function to debounce
 * @param {number} wait - Wait time in ms
 * @param {boolean} immediate - Execute immediately on first call
 * @returns {Function} - Debounced function
 */
export function debounce(func, wait = 300, immediate = false) {
    let timeout;

    return function executedFunction(...args) {
        const context = this;

        const later = () => {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };

        const callNow = immediate && !timeout;
        
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        
        if (callNow) func.apply(context, args);
    };
}

/**
 * Throttle function calls
 * @param {Function} func - Function to throttle
 * @param {number} limit - Time limit in ms
 * @returns {Function} - Throttled function
 */
export function throttle(func, limit = 300) {
    let inThrottle;

    return function(...args) {
        const context = this;
        
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

/**
 * Execute callback once
 * @param {Function} func - Function to execute once
 * @returns {Function} - Once-wrapped function
 */
export function once(func) {
    let called = false;
    let result;

    return function(...args) {
        if (!called) {
            called = true;
            result = func.apply(this, args);
        }
        return result;
    };
}

/**
 * Async retry with exponential backoff
 * @param {Function} func - Async function to retry
 * @param {number} maxRetries - Maximum retry attempts
 * @param {number} delay - Initial delay in ms
 * @returns {Promise} - Result or error
 */
export async function retry(func, maxRetries = 3, delay = 1000) {
    for (let i = 0; i < maxRetries; i++) {
        try {
            return await func();
        } catch (error) {
            if (i === maxRetries - 1) throw error;
            
            const waitTime = delay * Math.pow(2, i);
            await new Promise(resolve => setTimeout(resolve, waitTime));
        }
    }
}

/**
 * Request animation frame wrapper
 * @param {Function} callback - Callback to execute
 * @returns {number} - Request ID
 */
export function raf(callback) {
    return requestAnimationFrame(callback);
}

/**
 * Cancel animation frame
 * @param {number} id - Request ID
 */
export function cancelRaf(id) {
    cancelAnimationFrame(id);
}

/**
 * Batch DOM updates
 * @param {Function} callback - Callback with DOM updates
 */
export function batchUpdate(callback) {
    requestAnimationFrame(() => {
        callback();
    });
}

export default {
    debounce,
    throttle,
    once,
    retry,
    raf,
    cancelRaf,
    batchUpdate
};
