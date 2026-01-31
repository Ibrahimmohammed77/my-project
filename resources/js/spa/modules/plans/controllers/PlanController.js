import { Plan } from '../models/Plan.js';
import { PlanService } from '../services/PlanService.js';
import { PlanView } from '../views/PlanView.js';
import { Toast } from '../../../core/ui/Toast.js';
import { InputValidator } from '../../../core/security/InputValidator.js';

export class PlanController {
    constructor() {
        this.view = new PlanView();
        this.plans = [];
        this.init();
    }

    init() {
        this.attachEventListeners();
        this.loadPlans();
    }

    attachEventListeners() {
        if (this.view.form) {
            this.view.form.addEventListener('submit', (e) => this.handleFormSubmit(e));
        }
    }

    async loadPlans() {
        this.view.showLoading();
        try {
            this.plans = await PlanService.getAll();
            this.view.hideLoading();
            this.view.renderPlans(this.plans);
        } catch (error) {
            console.error('Failed to load plans:', error);
            this.view.hideLoading();
            if (this.view.loadingState) {
                this.view.loadingState.innerHTML = '<p class="text-red-500 text-center py-4">حدث خطأ أثناء تحميل البيانات</p>';
            }
        }
    }

    showCreateModal() {
        this.view.openModal('إضافة خطة جديدة');
        if (this.view.idInput) this.view.idInput.value = '';
        if (this.view.activeToggle) this.view.activeToggle.checked = true;
    }

    editPlan(id) {
        const plan = this.plans.find(p => p.id === id);
        if (!plan) return;
        this.view.openModal('تعديل الخطة');
        this.view.populateForm(plan);
    }

    viewPlan(id) {
        const plan = this.plans.find(p => p.id === id);
        if (!plan) return;
        this.view.openDetailsModal(plan);
    }

    async deletePlan(id) {
        if (!confirm('هل أنت متأكد من حذف هذه الخطة؟')) return;
        Toast.info('جاري الحذف...');
        try {
            await PlanService.delete(id);
            this.plans = this.plans.filter(p => p.id !== id);
            this.view.renderPlans(this.plans);
            Toast.success('تم الحذف بنجاح');
        } catch (error) {
            Toast.error('حدث خطأ أثناء الحذف');
        }
    }

    async handleFormSubmit(e) {
        e.preventDefault();
        const formData = this.getFormData();
        
        if (!this.validate(formData)) return;

        const planData = new Plan(formData);

        try {
            if (formData.id) {
                const updated = await PlanService.update(formData.id, planData);
                const idx = this.plans.findIndex(p => p.id === formData.id);
                if (idx !== -1) this.plans[idx] = updated;
                Toast.success('تم التحديث بنجاح');
            } else {
                const created = await PlanService.create(planData);
                this.plans.push(created);
                Toast.success('تم الإضافة بنجاح');
            }
            this.view.closeModal();
            this.view.renderPlans(this.plans);
        } catch (error) {
            Toast.error(error.response?.data?.message || 'حدث خطأ أثناء حفظ البيانات');
        }
    }

    getFormData() {
        return {
            id: document.getElementById('plan-id')?.value || null,
            name: document.getElementById('name')?.value || '',
            description: document.getElementById('description')?.value || '',
            price_monthly: document.getElementById('price_monthly')?.value || 0,
            price_yearly: document.getElementById('price_yearly')?.value || 0,
            storage_limit: (parseInt(document.getElementById('storage_limit')?.value) || 0) * 1024 * 1024 * 1024,
            features: document.getElementById('features')?.value.split('\n').filter(f => f.trim() !== '') || [],
            is_active: document.getElementById('is_active')?.checked ?? true
        };
    }

    validate(data) {
        if (!InputValidator.validateRequired(data.name)) {
            Toast.error('اسم الخطة مطلوب');
            return false;
        }
        if (data.storage_limit <= 0) {
            Toast.error('مساحة التخزين يجب أن تكون أكبر من 0');
            return false;
        }
        if (!data.features || data.features.length === 0) {
            Toast.error('يرجى إضافة ميزة واحدة على الأقل');
            return false;
        }
        return true;
    }

    closeModal() {
        this.view.closeModal();
    }

    closeDetailsModal() {
        this.view.closeDetailsModal();
    }
}

export default PlanController;
