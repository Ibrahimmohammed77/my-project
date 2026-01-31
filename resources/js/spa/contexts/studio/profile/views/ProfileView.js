import { DOM } from '../../../../core/utils/dom.js';
import { showErrors, clearErrors } from '../../../../utils/toast.js';

export class ProfileView {
    constructor() {
        this.form = document.getElementById('profile-form');
        this.submitBtn = this.form?.querySelector('button[type="submit"]');
        this.originalBtnContent = this.submitBtn?.innerHTML;
    }

    bindUpdate(handler) {
        if (!this.form) return;
        
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(this.form);
            const data = Object.fromEntries(formData.entries());
            
            // Clean empty strings to null/undefined if needed, but backend handles nullable
            handler(data);
        });
    }

    setLoading(loading) {
        if (!this.submitBtn) return;
        
        if (loading) {
            this.submitBtn.disabled = true;
            this.submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin ms-2"></i> جاري الحفظ...';
            this.submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        } else {
            this.submitBtn.disabled = false;
            this.submitBtn.innerHTML = this.originalBtnContent;
            this.submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
        }
    }

    /**
     * Display validation errors using shared utility
     * @param {Object} errors 
     */
    displayErrors(errors) {
        showErrors(errors);
    }

    clearErrors() {
        clearErrors();
    }
}

