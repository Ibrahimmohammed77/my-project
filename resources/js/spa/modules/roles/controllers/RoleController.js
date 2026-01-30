import { Role } from '../models/Role.js';
import { RoleService } from '../services/RoleService.js';
import { RoleView } from '../views/RoleView.js';
import { Toast } from '../../../core/ui/Toast.js';
import { InputValidator } from '../../../core/security/InputValidator.js';

export class RoleController {
    constructor() {
        this.view = new RoleView();
        this.roles = [];
        this.init();
    }

    /**
     * Initialize controller
     */
    init() {
        this.attachEventListeners();
        this.loadRoles();
    }

    /**
     * Attach event listeners
     */
    attachEventListeners() {
        // Search functionality
        if (this.view.searchInput) {
            this.view.searchInput.addEventListener('input', () => this.filterAndRender());
        }

        // Form submission
        if (this.view.form) {
            this.view.form.addEventListener('submit', (e) => this.handleFormSubmit(e));
        }
    }

    /**
     * Load all roles
     */
    async loadRoles() {
        this.view.showLoading();

        try {
            this.roles = await RoleService.getAll();
            this.view.hideLoading();
            this.filterAndRender();
        } catch (error) {
            console.error('Failed to load roles:', error);
            this.view.showError();
        }
    }

    /**
     * Filter and render roles
     */
    filterAndRender() {
        const searchTerm = this.view.searchInput?.value.toLowerCase() || '';
        
        const filteredRoles = this.roles.filter(role =>
            role.name.toLowerCase().includes(searchTerm) ||
            (role.description && role.description.toLowerCase().includes(searchTerm))
        );

        this.view.renderRoles(filteredRoles);
    }

    /**
     * Show create modal
     */
    showCreateModal() {
        const title = '<span class="w-2 h-6 bg-accent rounded-full"></span><span>إضافة دور جديد</span>';
        this.view.openModal(title);
    }

    /**
     * Edit role
     * @param {number} roleId
     */
    async editRole(roleId) {
        const role = this.roles.find(r => r.role_id === roleId);
        if (!role) {
            Toast.error('لم يتم العثور على الدور');
            return;
        }

        const title = '<span class="w-2 h-6 bg-orange-500 rounded-full"></span><span>تعديل دور</span>';
        this.view.openModal(title);
        this.view.populateForm(role);
    }

    /**
     * Delete role
     * @param {number} roleId
     */
    async deleteRole(roleId) {
        const role = this.roles.find(r => r.role_id === roleId);
        
        if (!confirm(`هل أنت متأكد من حذف دور "${role?.name}"؟`)) {
            return;
        }

        Toast.info('جاري حذف الدور...');

        try {
            await RoleService.delete(roleId);
            
            this.roles = this.roles.filter(r => r.role_id !== roleId);
            this.filterAndRender();
            
            Toast.success('تم حذف الدور بنجاح');
        } catch (error) {
            console.error('Failed to delete role:', error);
            Toast.error('فشل حذف الدور');
        }
    }

    /**
     * Handle form submission
     * @param {Event} e
     */
    async handleFormSubmit(e) {
        e.preventDefault();

        const formData = this.getFormData();
        
        // Validate
        const validation = this.validateFormData(formData);
        if (!validation.valid) {
            Toast.error(validation.errors[0]);
            return;
        }

        const roleData = new Role({
            name: formData.name,
            description: formData.description
        });

        try {
            let savedRole;
            
            if (formData.id) {
                // Update
                savedRole = await RoleService.update(formData.id, roleData);
                
                // Update in local array
                const index = this.roles.findIndex(r => r.role_id === formData.id);
                if (index !== -1) {
                    this.roles[index] = savedRole;
                }
                
                Toast.success('تم تحديث الدور بنجاح');
            } else {
                // Create
                savedRole = await RoleService.create(roleData);
                this.roles.push(savedRole);
                
                Toast.success('تم إنشاء الدور بنجاح');
            }

            this.view.closeModal();
            this.filterAndRender();
            
        } catch (error) {
            console.error('Failed to save role:', error);
            const errorMessage = error.response?.data?.message || 'فشل حفظ الدور';
            Toast.error(errorMessage);
        }
    }

    /**
     * Get form data
     * @returns {Object}
     */
    getFormData() {
        return {
            id: document.getElementById('role-id')?.value || null,
            name: document.getElementById('name')?.value || '',
            description: document.getElementById('description')?.value || ''
        };
    }

    /**
     * Validate form data
     * @param {Object} data
     * @returns {Object}
     */
    validateFormData(data) {
        const errors = [];

        if (!InputValidator.validateRequired(data.name)) {
            errors.push('اسم الدور مطلوب');
        }

        if (data.name && !InputValidator.validateLength(data.name, 2, 100)) {
            errors.push('اسم الدور يجب أن يكون بين 2 و 100 حرف');
        }

        if (data.description && !InputValidator.validateLength(data.description, 0, 500)) {
            errors.push('الوصف يجب ألا يتجاوز 500 حرف');
        }

        return {
            valid: errors.length === 0,
            errors
        };
    }

    /**
     * Close modal
     */
    closeModal() {
        this.view.closeModal();
    }
}

export default RoleController;
