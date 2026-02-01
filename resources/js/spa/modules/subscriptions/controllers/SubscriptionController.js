// resources/js/spa/modules/subscriptions/controllers/SubscriptionController.js
import { Subscription } from '../../../shared/models/Subscription.js';
import SubscriptionService from '../../../shared/services/SubscriptionService.js';
import SubscriptionView from '../views/SubscriptionView.js';
import { Toast } from '../../../core/ui/Toast.js';
import { InputValidator } from '../../../core/security/InputValidator.js';
import { Security } from '../../../core/security/Security.js';
import { DOM } from '../../../core/utils/dom.js';
import { debounce } from '../../../core/utils/performance.js';
import { showErrors, clearErrors } from '../../../utils/toast.js';

export class SubscriptionController {
    constructor() {
        console.log('[SubscriptionController] Initializing v1.1 - Force Refresh');
        this.subscriptions = [];
        this.view = new SubscriptionView();
        this.currentSubscription = null;
        this.plans = [];
        this.usersCache = new Map();

        // Force global instance update
        window.subscriptionController = this;

        this.init();
    }

    async init() {
        try {
            await this.loadPlans();
            this.attachEventListeners();
            await this.loadSubscriptions();
        } catch (error) {
            console.error('Failed to initialize SubscriptionController:', error);
            Toast.error('فشل تهيئة وحدة الاشتراكات');
        }
    }

    attachEventListeners() {
        // Expose method for manual triggering from Blade
        window.subscriptionController = this;

        // Plan filter input
        const planFilterInput = DOM.query('#plan-filter-input');
        if (planFilterInput) {
            planFilterInput.addEventListener('input', debounce((e) => {
                this.searchPlans(e.target.value, 'filter');
            }, 300));
        }

        // Status Filter
        const statusFilter = DOM.query('#status-filter');
        if (statusFilter) {
            statusFilter.addEventListener('change', () => {
                this.triggerFilter(); // Trigger server-side filter
            });
        }

        // Table actions
        if (this.view.tbody) {
            DOM.delegate(this.view.tbody, 'click', '[data-action="edit"]', (e) => {
                const btn = e.target.closest('button');
                if (btn) {
                    const subscriptionId = parseInt(btn.dataset.subscriptionId, 10);
                    this.editSubscription(subscriptionId);
                }
            });

            DOM.delegate(this.view.tbody, 'click', '[data-action="delete"]', (e) => {
                const btn = e.target.closest('button');
                if (btn) {
                    const subscriptionId = parseInt(btn.dataset.subscriptionId, 10);
                    this.deleteSubscription(subscriptionId);
                }
            });
        }

        // Form submission
        if (this.view.form) {
            this.view.form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmit();
            });
        }

        // User filter search input
        const userFilterInput = document.getElementById('user-filter-input');
        if (userFilterInput) {
            userFilterInput.addEventListener('input', debounce((e) => {
                this.searchUsers(e.target.value, this.filterRole, 'filter');
            }, 300));
        }

        // Modal user search input
        const modalUserInput = document.getElementById('modal-user-input');
        if (modalUserInput) {
            modalUserInput.addEventListener('input', debounce((e) => {
                this.searchUsers(e.target.value, this.modalRole, 'modal');
            }, 300));
        }

        // Modal plan search input
        const modalPlanInput = document.getElementById('modal-plan-input');
        if (modalPlanInput) {
            modalPlanInput.addEventListener('input', debounce((e) => {
                this.searchPlans(e.target.value, 'modal');
            }, 300));
        }

        // View event handlers
        this.view.setOnUserSelect((user) => {
            this.selectUser(user);
        });

        this.view.setOnPlanChange(() => {
            this.view.updatePrice();
            this.view.updateEndDate();
        });

        this.view.setOnBillingCycleChange(() => {
            this.view.updatePrice();
            this.view.updateEndDate();
        });
    }

    setFilterRole(role) {
        this.filterRole = role;
        this.selectedFilterUser = null; // Reset specific user filter
        this.triggerFilter(); // Re-render table with new role filter
    }

    setModalRole(role) {
        this.modalRole = role;
    }

    async loadPlans() {
        try {
            this.plans = await SubscriptionService.getPlans();
            this.view.populatePlanDropdown(this.plans, 'filter'); // Populate filter
            this.view.populatePlanDropdown(this.plans, 'modal'); // Populate modal
        } catch (error) {
            console.error('Failed to load plans:', error);
            Toast.error('فشل تحميل الخطط');
        }
    }

    
    async loadPage(page) {
        if (page < 1 || (this.metadata && page > this.metadata.last_page)) return;
        await this.loadSubscriptions(page);
    }

    async loadSubscriptions(page = 1) {
        this.view.showLoading();

        try {
            const filters = this.getFilters();
            filters.page = page;
            const response = await SubscriptionService.getAll(filters);
            this.subscriptions = response.items;
            this.metadata = response.meta;
            this.filterAndRender();
        } catch (error) {
            console.error('Failed to load subscriptions:', error);
            Toast.error('فشل تحميل الاشتراكات');
        } finally {
            this.view.hideLoading();
        }
    }

    getFilters() {
        const filters = {};

        // If specific user selected
        if (this.selectedFilterUser) {
            filters.user_id = this.selectedFilterUser.id;
        } else if (this.filterRole) {
            // Or just role
            filters.roles = [this.filterRole];
        }

        // Plan Filter
        const planFilterLabel = document.getElementById('plan-filter-label');
        if (planFilterLabel && planFilterLabel.dataset.planId) {
            filters.plan_id = planFilterLabel.dataset.planId;
        }

        // Status Filter
        const statusFilter = DOM.query('#status-filter');
        if (statusFilter?.value) filters.status_id = statusFilter.value;

        return filters;
    }

    filterAndRender() {
        // Backend filtering is preferred, but for client-side search/filter updates:
        const statusFilter = DOM.query('#status-filter');
        const statusValue = statusFilter?.value || '';

        // If we are relying on backend pagination, we should reload.
        // But for consistency with previous implementation, we will filter loaded items too.

        let filtered = this.subscriptions;

        if (statusValue) {
            filtered = filtered.filter(s => s.status?.lookup_value_id && s.status.lookup_value_id.toString() === statusValue);
        }

        // If role/user filters changed, we optimally should reload from server.
        // For now, let's trigger a server reload if this method is called from a filter change event.
        // Since getFilters() reads the current state, loadSubscriptions() is the right call.
        // But to avoid infinite loops, we need to be careful.

        // Actually, let's just render what we have if it matches, to feel "instant",
        // AND trigger a background fetch? Or just rely on loadSubscriptions being called explicitly?
        // Let's call loadSubscriptions for filter changes.

        // Wait, filterAndRender is bound to status change.
        // Let's modify logic:

        this.view.render(filtered);

        if (this.metadata && typeof this.view.renderPagination === 'function') {
            this.view.renderPagination(this.metadata);
        }
    }

    // Override filterAndRender to actually fetch data for real filtering
    async triggerFilter() {
        await this.loadSubscriptions(1);
    }

    async searchUsers(query, role, context) {
        console.log(`[SubscriptionController] searchUsers called with query: '${query}', role: '${role}', context: '${context}'`);
        const spinnerId = context === 'filter' ? 'user-filter-spinner' : 'modal-user-spinner';
        
        // Only require 2 chars if NO role is selected
        if (!role && query.length < 2) {
            console.log('[SubscriptionController] Query too short and no role selected, clearing results.');
            this.view.renderUserResults([], context); // Clear results
            return;
        }

        this.view.toggleSpinner(spinnerId, true);

        try {
            // Determine roles to search
            const roles = role ? role : null;
            
            console.log(`[SubscriptionController] Calling SubscriptionService.searchUsers with query: '${query}', roles:`, roles);
            const users = await SubscriptionService.searchUsers(query, roles);
            console.log(`[SubscriptionController] Received ${users.length} users from service.`);
            
            if (users.length === 0) {
                 // Optional: Toast.info('لم يتم العثور على مستخدمين');
                 console.warn('[SubscriptionController] No users found for criteria.');
            }

            this.view.renderUserResults(users, context);
        } catch (error) {
            console.error('[SubscriptionController] Error searching users:', error);
            Toast.error('حدث خطأ أثناء البحث عن المستخدمين');
            
            // Critical: Clear "Loading..." state from container
            const containerId = context === 'filter' ? 'user-filter-results' : 'modal-user-results';
            const container = document.getElementById(containerId);
            if(container) {
                container.innerHTML = `
                    <div class="p-4 text-center text-red-500 text-xs">
                        <i class="fas fa-exclamation-circle mb-1"></i>
                        <br>
                        فشل جلب البيانات. يرجى المحاولة مرة أخرى.
                    </div>
                `;
            }
        } finally {
            this.view.toggleSpinner(spinnerId, false);
        }
    }

    async searchPlans(query, context) {
        const spinnerId = context === 'filter' ? 'plan-filter-spinner' : 'modal-plan-spinner'; // Assuming modal plan search might exist
        this.view.toggleSpinner(spinnerId, true);

        try {
            // Filter plans already loaded
            const filteredPlans = this.plans.filter(plan =>
                plan.name.toLowerCase().includes(query.toLowerCase())
            );
            this.view.renderPlanResults(filteredPlans, context);
        } catch (error) {
            console.error('Error searching plans:', error);
        } finally {
            this.view.toggleSpinner(spinnerId, false);
        }
    }

    selectUser(user) {
        this.view.setSelectedUser(user);
        this.view.updateEndDate();
    }

    showCreateModal() {
        this.currentSubscription = null;
        this.view.clearForm();
        this.view.openModal('منح اشتراك جديد');
        clearErrors();
    }

    async editSubscription(subscriptionId) {
        const subscription = this.subscriptions.find(s => s.subscription_id === subscriptionId);

        if (!subscription) {
            Toast.error('الاشتراك غير موجود');
            return;
        }

        this.currentSubscription = subscription;
        this.view.populateForm(subscription);
        this.view.openModal('تعديل اشتراك');
        clearErrors();
    }

    async deleteSubscription(subscriptionId) {
        const subscription = this.subscriptions.find(s => s.subscription_id === subscriptionId);

        if (!subscription) {
            Toast.error('الاشتراك غير موجود');
            return;
        }

        if (!confirm(`هل أنت متأكد من حذف اشتراك "${subscription.user?.name}"؟`)) {
            return;
        }

        try {
            await SubscriptionService.delete(subscriptionId);
            this.subscriptions = this.subscriptions.filter(s => s.subscription_id !== subscriptionId);
            this.filterAndRender();
            Toast.success('تم حذف الاشتراك بنجاح');
        } catch (error) {
            console.error('Failed to delete subscription:', error);
            Toast.error('فشل حذف الاشتراك');
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

        this.view.disableForm();

        try {
            const subscription = this.currentSubscription
                ? await SubscriptionService.update(this.currentSubscription.subscription_id, formData)
                : await SubscriptionService.create(formData);

            if (this.currentSubscription) {
                const index = this.subscriptions.findIndex(s => s.subscription_id === this.currentSubscription.subscription_id);
                if (index !== -1) {
                    this.subscriptions[index] = subscription;
                }
            } else {
                this.subscriptions.unshift(subscription);
            }

            this.filterAndRender();
            this.view.closeModal();
            Toast.success(this.currentSubscription ? 'تم تحديث الاشتراك بنجاح' : 'تم إنشاء الاشتراك بنجاح');
        } catch (error) {
            console.error('Failed to save subscription:', error);
            if (error.response && error.response.status === 422) {
                showErrors(error.response.data.errors || {});
            } else {
                Toast.error('فشل حفظ الاشتراك');
            }
        } finally {
            this.view.enableForm();
        }
    }

    getFormData() {
        if (!this.view.form) return {};
        const formData = DOM.getFormData(this.view.form);
        
        // Explicitly convert checkbox value to boolean
        formData.auto_renew = !!formData.auto_renew;
        
        return formData;
    }

    validateFormData(data) {
        const rules = {
            user_id: ['required'],
            plan_id: ['required'],
            billing_cycle: ['required', 'in:monthly,yearly']
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
}

export default SubscriptionController;
