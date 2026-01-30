/**
 * Search Helper
 * Simple utility to handle search input with debounce
 */

import { debounce } from '../core/utils/performance.js';

/**
 * Setup search for an input field
 * @param {string|HTMLElement} selector - Input selector or element
 * @param {Function} callback - Callback for search query
 * @param {number} wait - Debounce wait time
 */
export function setupSearch(selector, callback, wait = 300) {
    const input = typeof selector === 'string' ? document.querySelector(selector) : selector;
    
    if (!input) {
        console.warn(`Search input not found: ${selector}`);
        return;
    }

    const debouncedCallback = debounce((e) => {
        callback(e.target.value);
    }, wait);

    input.addEventListener('input', debouncedCallback);
}

export default setupSearch;
