/**
 * DOM Utilities
 * Helper functions for safe DOM manipulation
 */

import { XssProtection } from '../security/XssProtection.js';

export const DOM = {
    /**
     * Create element safely
     * @param {string} tag - HTML tag
     * @param {Object} attributes - Element attributes
     * @param {string|HTMLElement} content - Element content
     * @returns {HTMLElement} - Created element
     */
    create(tag, attributes = {}, content = null) {
        const element = document.createElement(tag);

        // Set attributes
        Object.entries(attributes).forEach(([key, value]) => {
            if (key === 'className') {
                element.className = value;
            } else if (key === 'dataset') {
                Object.entries(value).forEach(([dataKey, dataValue]) => {
                    element.dataset[dataKey] = dataValue;
                });
            } else if (key.startsWith('on') && typeof value === 'function') {
                element.addEventListener(key.slice(2).toLowerCase(), value);
            } else {
                element.setAttribute(key, value);
            }
        });

        // Set content
        if (content !== null) {
            if (typeof content === 'string') {
                XssProtection.setTextContent(element, content);
            } else if (content instanceof HTMLElement) {
                element.appendChild(content);
            } else if (Array.isArray(content)) {
                content.forEach(child => {
                    if (child instanceof HTMLElement) {
                        element.appendChild(child);
                    }
                });
            }
        }

        return element;
    },

    /**
     * Query element safely
     * @param {string} selector - CSS selector
     * @param {HTMLElement} context - Search context
     * @returns {HTMLElement|null} - Found element
     */
    query(selector, context = document) {
        try {
            return context.querySelector(selector);
        } catch (error) {
            console.error(`Invalid selector: ${selector}`, error);
            return null;
        }
    },

    /**
     * Query all elements safely
     * @param {string} selector - CSS selector
     * @param {HTMLElement} context - Search context
     * @returns {Array<HTMLElement>} - Found elements
     */
    queryAll(selector, context = document) {
        try {
            return Array.from(context.querySelectorAll(selector));
        } catch (error) {
            console.error(`Invalid selector: ${selector}`, error);
            return [];
        }
    },

    /**
     * Add event listener with cleanup
     * @param {HTMLElement} element - Target element
     * @param {string} event - Event name
     * @param {Function} handler - Event handler
     * @param {Object} options - Event options
     * @returns {Function} - Cleanup function
     */
    on(element, event, handler, options = {}) {
        if (!element || !event || typeof handler !== 'function') {
            console.warn('Invalid event listener parameters');
            return () => {};
        }

        element.addEventListener(event, handler, options);

        return () => element.removeEventListener(event, handler, options);
    },

    /**
     * Add delegated event listener
     * @param {HTMLElement} parent - Parent element
     * @param {string} event - Event name
     * @param {string} selector - Target selector
     * @param {Function} handler - Event handler
     * @returns {Function} - Cleanup function
     */
    delegate(parent, event, selector, handler) {
        const delegatedHandler = (e) => {
            const target = e.target.closest(selector);
            if (target && parent.contains(target)) {
                handler.call(target, e);
            }
        };

        return this.on(parent, event, delegatedHandler);
    },

    /**
     * Remove all child nodes
     * @param {HTMLElement} element - Target element
     */
    empty(element) {
        if (!element) return;
        
        while (element.firstChild) {
            element.removeChild(element.firstChild);
        }
    },

    /**
     * Show element
     * @param {HTMLElement} element - Target element
     */
    show(element) {
        if (!element) return;
        element.classList.remove('hidden');
    },

    /**
     * Hide element
     * @param {HTMLElement} element - Target element
     */
    hide(element) {
        if (!element) return;
        element.classList.add('hidden');
    },

    /**
     * Toggle element visibility
     * @param {HTMLElement} element - Target element
     */
    toggle(element) {
        if (!element) return;
        element.classList.toggle('hidden');
    },

    /**
     * Add class(es) safely
     * @param {HTMLElement} element - Target element
     * @param {string|Array} classes - Classes to add
     */
    addClass(element, classes) {
        if (!element) return;
        
        const classList = Array.isArray(classes) ? classes : [classes];
        element.classList.add(...classList);
    },

    /**
     * Remove class(es) safely
     * @param {HTMLElement} element - Target element
     * @param {string|Array} classes - Classes to remove
     */
    removeClass(element, classes) {
        if (!element) return;
        
        const classList = Array.isArray(classes) ? classes : [classes];
        element.classList.remove(...classList);
    },

    /**
     * Get form data as object
     * @param {HTMLFormElement} form - Form element
     * @returns {Object} - Form data as object
     */
    getFormData(form) {
        if (!(form instanceof HTMLFormElement)) {
            console.error('Invalid form element');
            return {};
        }

        const formData = new FormData(form);
        const data = {};

        for (const [key, value] of formData.entries()) {
            // Handle multiple values (checkboxes)
            if (data[key]) {
                if (!Array.isArray(data[key])) {
                    data[key] = [data[key]];
                }
                data[key].push(value);
            } else {
                data[key] = value;
            }
        }

        return data;
    },

    /**
     * Set form data from object
     * @param {HTMLFormElement} form - Form element
     * @param {Object} data - Data to set
     */
    setFormData(form, data) {
        if (!(form instanceof HTMLFormElement)) {
            console.error('Invalid form element');
            return;
        }

        Object.entries(data).forEach(([key, value]) => {
            const field = form.elements[key];
            
            if (field) {
                if (field.type === 'checkbox' || field.type === 'radio') {
                    field.checked = !!value;
                } else {
                    field.value = value || '';
                }
            }
        });
    },

    /**
     * Disable form
     * @param {HTMLFormElement} form - Form element
     */
    disableForm(form) {
        if (!(form instanceof HTMLFormElement)) return;

        const fields = form.querySelectorAll('input, select, textarea, button');
        fields.forEach(field => field.disabled = true);
    },

    /**
     * Enable form
     * @param {HTMLFormElement} form - Form element
     */
    enableForm(form) {
        if (!(form instanceof HTMLFormElement)) return;

        const fields = form.querySelectorAll('input, select, textarea, button');
        fields.forEach(field => field.disabled = false);
    }
};

export default DOM;
