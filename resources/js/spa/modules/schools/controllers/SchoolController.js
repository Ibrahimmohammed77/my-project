/**
 * School Controller
 * Same pattern as AccountController: load, filter, CRUD, validation, Toast
 */

import { School } from '../../../shared/models/School.js';
import { SchoolService } from '../../../shared/services/SchoolService.js';
import SchoolView from '../views/SchoolView.js';
import { Toast } from '../../../core/ui/Toast.js';
import { InputValidator } from '../../../core/security/InputValidator.js';
import { Security } from '../../../core/security/Security.js';
import { DOM } from '../../../core/utils/dom.js';
import { debounce } from '../../../core/utils/performance.js';
import { showErrors, clearErrors } from '../../../utils/toast.js';

export class SchoolController {
    constructor() {
        this.schools = [];
        this.view = new SchoolView();
        this.currentSchool = null;

        this.init();
    }

    init() {
        this.attachEventListeners();
        this.loadSchools();
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
                    const schoolId = parseInt(btn.dataset.schoolId, 10);
                    this.viewSchool(schoolId);
                }
            });

            DOM.delegate(this.view.tbody, 'click', '[data-action="edit"]', (e) => {
                const btn = e.target.closest('button');
                if (btn) {
                    const schoolId = parseInt(btn.dataset.schoolId, 10);
                    this.editSchool(schoolId);
                }
            });

            DOM.delegate(this.view.tbody, 'click', '[data-action="delete"]', (e) => {
                const btn = e.target.closest('button');
                if (btn) {
                    const schoolId = parseInt(btn.dataset.schoolId, 10);
                    this.deleteSchool(schoolId);
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

    async loadSchools() {
        this.view.showLoading();

        try {
            this.schools = await SchoolService.getAll();
            this.filterAndRender();
        } catch (error) {
            console.error('Failed to load schools:', error);
            Toast.error('فشل تحميل المدارس');
        } finally {
            this.view.hideLoading();
        }
    }

    filterAndRender() {
        const searchInput = DOM.query('#search');
        const statusFilter = DOM.query('#status-filter');

        const searchTerm = (searchInput ? searchInput.value : '').toLowerCase();
        const statusValue = statusFilter ? statusFilter.value : '';

        const filtered = this.schools.filter(school => {
            const matchesSearch = !searchTerm ||
                (school.name && school.name.toLowerCase().includes(searchTerm)) ||
                (school.email && school.email.toLowerCase().includes(searchTerm));
            const matchesStatus = !statusValue || (school.status?.lookup_value_id == statusValue);
            return matchesSearch && matchesStatus;
        });

        this.view.render(filtered);
    }

    showCreateModal() {
        this.currentSchool = null;
        this.view.clearForm();
        this.view.openModal('إضافة مدرسة جديدة');
        clearErrors();
    }

    async editSchool(schoolId) {
        const school = this.schools.find(s => s.school_id === schoolId);

        if (!school) {
            Toast.error('المدرسة غير موجودة');
            return;
        }

        this.currentSchool = school;
        this.view.populateForm(school);
        this.view.openModal('تعديل مدرسة');
        clearErrors();
    }

    async viewSchool(schoolId) {
        const school = this.schools.find(s => s.school_id === schoolId);
        if (!school) {
            Toast.error('المدرسة غير موجودة');
            return;
        }

        this.view.openDetailsModal(school);

        try {
            const statistics = await SchoolService.getStatistics(schoolId);
            this.view.renderStatistics(statistics);
        } catch (error) {
            console.error('Failed to load school statistics:', error);
            Toast.error('حدث خطأ أثناء تحميل إحصائيات المدرسة');
        }
    }

    async deleteSchool(schoolId) {
        const school = this.schools.find(s => s.school_id === schoolId);

        if (!school) {
            Toast.error('المدرسة غير موجودة');
            return;
        }

        if (!confirm(`هل أنت متأكد من حذف المدرسة "${school.name}"؟`)) {
            return;
        }

        Toast.info('جاري حذف المدرسة...');

        try {
            await SchoolService.delete(schoolId);
            this.schools = this.schools.filter(s => s.school_id !== schoolId);
            this.filterAndRender();
            Toast.success('تم حذف المدرسة بنجاح');
        } catch (error) {
            console.error('Failed to delete school:', error);
            Toast.error('فشل حذف المدرسة');
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
            const school = this.currentSchool
                ? await SchoolService.update(this.currentSchool.school_id, sanitized)
                : await SchoolService.create(sanitized);

            if (this.currentSchool) {
                const index = this.schools.findIndex(s => s.school_id === this.currentSchool.school_id);
                if (index !== -1) {
                    this.schools[index] = school;
                }
            } else {
                this.schools.unshift(school);
            }

            this.filterAndRender();
            this.view.closeModal();
            Toast.success(this.currentSchool ? 'تم تحديث المدرسة بنجاح' : 'تم إنشاء المدرسة بنجاح');
        } catch (error) {
            console.error('Failed to save school:', error);
            if (error.response && error.response.status === 422) {
                showErrors(error.response.data.errors || {});
            } else {
                Toast.error('فشل حفظ المدرسة');
            }
        } finally {
            this.view.enableForm();
        }
    }

    getFormData() {
        if (!this.view.form) return {};
        const data = DOM.getFormData(this.view.form);
        const hiddenId = document.getElementById('school-id');
        if (hiddenId && hiddenId.value) {
            data.school_id = hiddenId.value;
        }
        return data;
    }

    validateFormData(data) {
        const rules = {
            name: ['required', 'min:2'],
            email: ['required', 'email'],
            school_type_id: ['required'],
            school_level_id: ['required'],
            school_status_id: ['required']
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

export default SchoolController;
