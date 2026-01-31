/**
 * Studio Controller
 * Same pattern as AccountController: load, filter, CRUD, validation, Toast
 */

import { Studio } from '../../../models/Studio.js';
import { StudioService } from '../../../services/StudioService.js';
import StudioView from '../views/StudioView.js';
import { Toast } from '../../../core/ui/Toast.js';
import { InputValidator } from '../../../core/security/InputValidator.js';
import { Security } from '../../../core/security/Security.js';
import { DOM } from '../../../core/utils/dom.js';
import { debounce } from '../../../core/utils/performance.js';
import { showErrors, clearErrors } from '../../../utils/toast.js';

export class StudioController {
    constructor() {
        this.studios = [];
        this.view = new StudioView();
        this.currentStudio = null;

        this.init();
    }

    init() {
        this.attachEventListeners();
        this.loadStudios();
    }

    attachEventListeners() {
        const searchInput = DOM.query('#search');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(() => {
                this.filterAndRender();
            }, 300));
        }

        const statusFilter = DOM.query('#status-filter');
        if (statusFilter) {
            statusFilter.addEventListener('change', () => {
                this.filterAndRender();
            });
        }

        if (this.view.tbody) {
            DOM.delegate(this.view.tbody, 'click', '[data-action="view"]', (e) => {
                const btn = e.target.closest('button');
                if (btn) {
                    const studioId = parseInt(btn.dataset.studioId, 10);
                    this.viewStudio(studioId);
                }
            });

            DOM.delegate(this.view.tbody, 'click', '[data-action="edit"]', (e) => {
                const btn = e.target.closest('button');
                if (btn) {
                    const studioId = parseInt(btn.dataset.studioId, 10);
                    this.editStudio(studioId);
                }
            });

            DOM.delegate(this.view.tbody, 'click', '[data-action="delete"]', (e) => {
                const btn = e.target.closest('button');
                if (btn) {
                    const studioId = parseInt(btn.dataset.studioId, 10);
                    this.deleteStudio(studioId);
                }
            });
        }

        if (this.view.form) {
            this.view.form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmit();
            });
        }
    }

    async loadStudios() {
        this.view.showLoading();

        try {
            this.studios = await StudioService.getAll();
            this.filterAndRender();
        } catch (error) {
            console.error('Failed to load studios:', error);
            Toast.error('فشل تحميل الاستوديوهات');
        } finally {
            this.view.hideLoading();
        }
    }

    filterAndRender() {
        const searchInput = DOM.query('#search');
        const statusFilter = DOM.query('#status-filter');

        const searchTerm = (searchInput ? searchInput.value : '').toLowerCase();
        const statusValue = statusFilter ? statusFilter.value : '';

        const filtered = this.studios.filter(studio => {
            const matchesSearch = !searchTerm ||
                (studio.name && studio.name.toLowerCase().includes(searchTerm)) ||
                (studio.email && studio.email.toLowerCase().includes(searchTerm));
            const matchesStatus = !statusValue || (studio.status?.code === statusValue);
            return matchesSearch && matchesStatus;
        });

        this.view.render(filtered);
    }

    showCreateModal() {
        this.currentStudio = null;
        this.view.clearForm();
        this.view.openModal('إضافة استوديو جديد');
        clearErrors();
    }

    async editStudio(studioId) {
        const studio = this.studios.find(s => s.studio_id === studioId);

        if (!studio) {
            Toast.error('الاستوديو غير موجود');
            return;
        }

        this.currentStudio = studio;
        this.view.populateForm(studio);
        this.view.openModal('تعديل استوديو');
        clearErrors();
    }

    async viewStudio(studioId) {
        const studio = this.studios.find(s => s.studio_id === studioId);
        if (!studio) {
            Toast.error('الاستوديو غير موجود');
            return;
        }

        this.view.openDetailsModal(studio);

        try {
            const statistics = await StudioService.getStatistics(studioId);
            this.view.renderStatistics(statistics);
        } catch (error) {
            console.error('Failed to load studio statistics:', error);
            Toast.error('حدث خطأ أثناء تحميل إحصائيات الاستوديو');
        }
    }

    async deleteStudio(studioId) {
        const studio = this.studios.find(s => s.studio_id === studioId);

        if (!studio) {
            Toast.error('الاستوديو غير موجود');
            return;
        }

        if (!confirm(`هل أنت متأكد من حذف الاستوديو "${studio.name}"؟`)) {
            return;
        }

        Toast.info('جاري حذف الاستوديو...');

        try {
            await StudioService.delete(studioId);
            this.studios = this.studios.filter(s => s.studio_id !== studioId);
            this.filterAndRender();
            Toast.success('تم حذف الاستوديو بنجاح');
        } catch (error) {
            console.error('Failed to delete studio:', error);
            Toast.error('فشل حذف الاستوديو');
        }
    }

    async handleFormSubmit() {
        clearErrors();

        const formData = this.getFormData();

        const validation = this.validateFormData(formData);
        if (!validation.valid) {
            showErrors(validation.errors);
            return;
        }

        const sanitized = Security.sanitizeInput(formData);

        this.view.disableForm();

        try {
            const studio = this.currentStudio
                ? await StudioService.update(this.currentStudio.studio_id, sanitized)
                : await StudioService.create(sanitized);

            if (this.currentStudio) {
                const index = this.studios.findIndex(s => s.studio_id === this.currentStudio.studio_id);
                if (index !== -1) {
                    this.studios[index] = studio;
                }
            } else {
                this.studios.unshift(studio);
            }

            this.filterAndRender();
            this.view.closeModal();
            Toast.success(this.currentStudio ? 'تم تحديث الاستوديو بنجاح' : 'تم إنشاء الاستوديو بنجاح');
        } catch (error) {
            console.error('Failed to save studio:', error);
            if (error.response && error.response.status === 422) {
                showErrors(error.response.data.errors || {});
            } else {
                Toast.error('فشل حفظ الاستوديو');
            }
        } finally {
            this.view.enableForm();
        }
    }

    getFormData() {
        if (!this.view.form) return {};
        return DOM.getFormData(this.view.form);
    }

    validateFormData(data) {
        const rules = {
            name: ['required', 'min:2'],
            email: ['required', 'email'],
            studio_status_id: ['required']
        };

        const validator = new InputValidator(rules);
        const isValid = validator.validate(data);
        return {
            valid: isValid,
            errors: validator.getErrors()
        };
    }

    closeModal() {
        this.view.closeModal();
        clearErrors();
    }

    closeDetailsModal() {
        this.view.closeDetailsModal();
    }
}

export default StudioController;
