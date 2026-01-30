import { Permission } from '../models/Permission.js';
import { PermissionService } from '../services/PermissionService.js';
import { PermissionView } from '../views/PermissionView.js';
import { Toast } from '../../../core/ui/Toast.js';
import { InputValidator } from '../../../core/security/InputValidator.js';

export class PermissionController {
    constructor() {
        this.view = new PermissionView();
        this.permissions = [];
        this.init();
    }

    init() {
        this.attachEventListeners();
        this.loadPermissions();
    }

    attachEventListeners() {
        if (this.view.searchInput) {
            this.view.searchInput.addEventListener('input', () => this.filterAndRender());
        }

        if (this.view.form) {
            this.view.form.addEventListener('submit', (e) => this.handleFormSubmit(e));
        }
    }

    async loadPermissions() {
        this.view.showLoading();

        try {
            this.permissions = await PermissionService.getAll();
            this.view.hideLoading();
            this.filterAndRender();
        } catch (error) {
            console.error('Failed to load permissions:', error);
            this.view.showError();
        }
    }

    filterAndRender() {
        const searchTerm = this.view.searchInput?.value.toLowerCase() || '';
        
        const filtered = this.permissions.filter(perm =>
            perm.name.toLowerCase().includes(searchTerm) ||
            perm.resource_type.toLowerCase().includes(searchTerm) ||
            perm.action.toLowerCase().includes(searchTerm)
        );

        this.view.renderPermissions(filtered);
    }

    showCreateModal() {
        const title = '<span class="w-2 h-6 bg-accent rounded-full"></span><span>إضافة صلاحية جديدة</span>';
        this.view.openModal(title);
    }

    async editPermission(permId) {
        const perm = this.permissions.find(p => p.permission_id === permId);
        if (!perm) {
            Toast.error('لم يتم العثور على الصلاحية');
            return;
        }

        const title = '<span class="w-2 h-6 bg-orange-500 rounded-full"></span><span>تعديل صلاحية</span>';
        this.view.openModal(title);
        this.view.populateForm(perm);
    }

    async deletePermission(permId) {
        const perm = this.permissions.find(p => p.permission_id === permId);
        
        if (!confirm(`هل أنت متأكد من حذف صلاحية "${perm?.name}"؟`)) {
            return;
        }

        Toast.info('جاري حذف الصلاحية...');

        try {
            await PermissionService.delete(permId);
            
            this.permissions = this.permissions.filter(p => p.permission_id !== permId);
            this.filterAndRender();
            
            Toast.success('تم حذف الصلاحية بنجاح');
        } catch (error) {
            console.error('Failed to delete permission:', error);
            Toast.error('فشل حذف الصلاحية');
        }
    }

    async handleFormSubmit(e) {
        e.preventDefault();

        const formData = this.getFormData();
        
        const validation = this.validateFormData(formData);
        if (!validation.valid) {
            Toast.error(validation.errors[0]);
            return;
        }

        const permData = new Permission({
            name: formData.name,
            resource_type: formData.resource_type,
            action: formData.action,
            description: formData.description
        });

        try {
            let saved;
            
            if (formData.id) {
                saved = await PermissionService.update(formData.id, permData);
                const index = this.permissions.findIndex(p => p.permission_id === formData.id);
                if (index !== -1) {
                    this.permissions[index] = saved;
                }
                Toast.success('تم تحديث الصلاحية بنجاح');
            } else {
                saved = await PermissionService.create(permData);
                this.permissions.push(saved);
                Toast.success('تم إنشاء الصلاحية بنجاح');
            }

            this.view.closeModal();
            this.filterAndRender();
            
        } catch (error) {
            console.error('Failed to save permission:', error);
            const errorMessage = error.response?.data?.message || 'فشل حفظ الصلاحية';
            Toast.error(errorMessage);
        }
    }

    getFormData() {
        return {
            id: document.getElementById('permission-id')?.value || null,
            name: document.getElementById('name')?.value || '',
            resource_type: document.getElementById('resource_type')?.value || '',
            action: document.getElementById('action')?.value || '',
            description: document.getElementById('description')?.value || ''
        };
    }

    validateFormData(data) {
        const errors = [];

        if (!InputValidator.validateRequired(data.name)) {
            errors.push('اسم الصلاحية مطلوب');
        }

        if (!InputValidator.validateRequired(data.resource_type)) {
            errors.push('نوع المورد مطلوب');
        }

        if (!InputValidator.validateRequired(data.action)) {
            errors.push('الإجراء مطلوب');
        }

        return {
            valid: errors.length === 0,
            errors
        };
    }

    closeModal() {
        this.view.closeModal();
    }
}

export default PermissionController;
