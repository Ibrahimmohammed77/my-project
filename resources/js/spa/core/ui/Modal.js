/**
 * Modal Component
 * Reusable, accessible modal with keyboard navigation
 */

import { XssProtection } from '../security/XssProtection.js';

export class Modal {
    constructor(options = {}) {
        this.id = options.id || `modal-${Date.now()}`;
        this.title = options.title || '';
        this.size = options.size || 'md'; // sm, md, lg, xl
        this.closeOnOverlay = options.closeOnOverlay !== false;
        this.closeOnEscape = options.closeOnEscape !== false;
        this.onOpen = options.onOpen || null;
        this.onClose = options.onClose || null;
        
        this.element = null;
        this.isOpen = false;
        
        this.create();
        this.attachEvents();
    }

    /**
     * Create modal element
     */
    create() {
        const modal = document.createElement('div');
        modal.id = this.id;
        modal.className = 'hidden fixed inset-0 z-50 overflow-y-auto';
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('aria-labelledby', `${this.id}-title`);

        const sizeClasses = {
            sm: 'sm:max-w-md',
            md: 'sm:max-w-lg',
            lg: 'sm:max-w-2xl',
            xl: 'sm:max-w-4xl',
            full: 'sm:max-w-full sm:mx-4'
        };

        modal.innerHTML = `
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-md transition-opacity" data-modal-overlay></div>
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-[2.5rem] bg-white text-right shadow-2xl transition-all sm:my-8 sm:w-full ${sizeClasses[this.size]} border border-gray-100" data-modal-content>
                    <!-- Header -->
                    <div class="px-10 pt-10 pb-6 flex justify-between items-center" data-modal-header>
                        <h3 class="text-2xl font-black text-gray-900 flex items-center gap-3" id="${this.id}-title">
                            <span class="w-2 h-8 bg-accent rounded-full"></span>
                            <span data-modal-title>${XssProtection.escape(this.title)}</span>
                        </h3>
                        <button type="button" data-modal-close class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:text-gray-900 hover:bg-gray-100 transition-all active:scale-95">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="px-10 pb-10" data-modal-body></div>

                    <!-- Footer (optional) -->
                    <div class="hidden px-10 py-8 bg-gray-50/50 border-t border-gray-100" data-modal-footer></div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        this.element = modal;
    }

    /**
     * Attach event listeners
     */
    attachEvents() {
        // Close button
        const closeBtn = this.element.querySelector('[data-modal-close]');
        closeBtn?.addEventListener('click', () => this.close());

        // Overlay click
        if (this.closeOnOverlay) {
            const overlay = this.element.querySelector('[data-modal-overlay]');
            overlay?.addEventListener('click', () => this.close());
        }

        // Escape key
        if (this.closeOnEscape) {
            this.escapeHandler = (e) => {
                if (e.key === 'Escape' && this.isOpen) {
                    this.close();
                }
            };
            document.addEventListener('keydown', this.escapeHandler);
        }

        // Focus trap
        this.element.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                this.trapFocus(e);
            }
        });
    }

    /**
     * Trap focus within modal
     * @param {KeyboardEvent} e - Keyboard event
     */
    trapFocus(e) {
        const focusableElements = this.element.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        if (e.shiftKey) {
            if (document.activeElement === firstElement) {
                lastElement.focus();
                e.preventDefault();
            }
        } else {
            if (document.activeElement === lastElement) {
                firstElement.focus();
                e.preventDefault();
            }
        }
    }

    /**
     * Set modal title
     * @param {string} title - New title
     */
    setTitle(title) {
        this.title = title;
        const titleElement = this.element.querySelector('[data-modal-title]');
        if (titleElement) {
            XssProtection.setTextContent(titleElement, title);
        }
    }

    /**
     * Set modal body content
     * @param {string|HTMLElement} content - Body content
     */
    setBody(content) {
        const body = this.element.querySelector('[data-modal-body]');
        if (!body) return;

        if (typeof content === 'string') {
            body.innerHTML = content;
        } else if (content instanceof HTMLElement) {
            body.innerHTML = '';
            body.appendChild(content);
        }
    }

    /**
     * Set modal footer content
     * @param {string|HTMLElement} content - Footer content
     */
    setFooter(content) {
        const footer = this.element.querySelector('[data-modal-footer]');
        if (!footer) return;

        footer.classList.remove('hidden');

        if (typeof content === 'string') {
            footer.innerHTML = content;
        } else if (content instanceof HTMLElement) {
            footer.innerHTML = '';
            footer.appendChild(content);
        }
    }

    /**
     * Open modal
     */
    open() {
        if (this.isOpen) return;

        this.element.classList.remove('hidden');
        this.isOpen = true;

        // Prevent body scroll
        document.body.style.overflow = 'hidden';

        // Focus first focusable element
        setTimeout(() => {
            const firstFocusable = this.element.querySelector('button, [href], input, select, textarea');
            firstFocusable?.focus();
        }, 100);

        if (this.onOpen) {
            this.onOpen();
        }
    }

    /**
     * Close modal
     */
    close() {
        if (!this.isOpen) return;

        this.element.classList.add('hidden');
        this.isOpen = false;

        // Restore body scroll
        document.body.style.overflow = '';

        if (this.onClose) {
            this.onClose();
        }
    }

    /**
     * Toggle modal
     */
    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }

    /**
     * Destroy modal
     */
    destroy() {
        if (this.escapeHandler) {
            document.removeEventListener('keydown', this.escapeHandler);
        }

        this.element?.remove();
        this.element = null;
        this.isOpen = false;
    }

    /**
     * Static method to create and show modal quickly
     * @param {Object} options - Modal options
     * @returns {Modal} - Modal instance
     */
    static show(options = {}) {
        const modal = new Modal(options);
        modal.open();
        return modal;
    }
}

// Make it globally accessible
if (typeof window !== 'undefined') {
    window.Modal = Modal;
}

export default Modal;
