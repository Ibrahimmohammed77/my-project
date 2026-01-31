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
        this.subscriptions = [];
        this.view = new SubscriptionView();
        this.currentSubscription = null;
        this.plans = [];
        this.usersCache = new Map();

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
        // Search input
        const searchInput = DOM.query('#search');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(() => {
                this.filterAndRender();
            }, 300));
        }

        // Filters
        const planFilter = DOM.query('#plan-filter');
        if (planFilter) {
            planFilter.addEventListener('change', () => {
                this.filterAndRender();
            });
        }

        const statusFilter = DOM.query('#status-filter');
        if (statusFilter) {
            statusFilter.addEventListener('change', () => {
                this.filterAndRender();
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

        // User search
        const userSearchInput = document.getElementById('user-search');
        if (userSearchInput) {
            userSearchInput.addEventListener('input', debounce((e) => {
                this.searchUsers(e.target.value);
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

    async loadPlans() {
        try {
            this.plans = await SubscriptionService.getPlans();
            this.populatePlanOptions();
        } catch (error) {
            console.error('Failed to load plans:', error);
            Toast.error('فشل تحميل الخطط');
        }
    }

    populatePlanOptions() {
        const planSelect = document.getElementById('plan_id');
        const planFilter = document.getElementById('plan-filter');

        // Clear existing options except first
        if (planSelect) {
            while (planSelect.options.length > 1) {
                planSelect.remove(1);
            }
            this.plans.forEach(plan => {
                const option = document.createElement('option');
                option.value = plan.plan_id;
                option.textContent = plan.name;
                option.setAttribute('data-price-monthly', plan.price_monthly || 0);
                option.setAttribute('data-price-yearly', plan.price_yearly || 0);
                planSelect.appendChild(option);
            });
        }

        if (planFilter) {
            while (planFilter.options.length > 1) {
                planFilter.remove(1);
            }
            this.plans.forEach(plan => {
                const option = document.createElement('option');
                option.value = plan.plan_id;
                option.textContent = plan.name;
                planFilter.appendChild(option);
            });
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

        const searchInput = DOM.query('#search');
        if (searchInput?.value) filters.search = searchInput.value;

        const planFilter = DOM.query('#plan-filter');
        if (planFilter?.value) filters.plan_id = planFilter.value;

        const statusFilter = DOM.query('#status-filter');
        if (statusFilter?.value) filters.status_id = statusFilter.value;

        return filters;
    }

    filterAndRender() {
        const searchInput = DOM.query('#search');
        const planFilter = DOM.query('#plan-filter');
        const statusFilter = DOM.query('#status-filter');

        const searchTerm = (searchInput?.value || '').toLowerCase();
        const planValue = planFilter?.value || '';
        const statusValue = statusFilter?.value || '';

        const filtered = this.subscriptions.filter(subscription => {
            const matchesSearch = !searchTerm ||
                (subscription.user?.name && subscription.user.name.toLowerCase().includes(searchTerm)) ||
                (subscription.user?.email && subscription.user.email.toLowerCase().includes(searchTerm));

            const matchesPlan = !planValue ||
                (subscription.plan_id && subscription.plan_id.toString() === planValue);

            const matchesStatus = !statusValue ||
                (subscription.status?.lookup_value_id && subscription.status.lookup_value_id.toString() === statusValue);

            return matchesSearch && matchesPlan && matchesStatus;
        });

        this.view.render(filtered);

        // Update pagination UI if it exists in view
        if (this.metadata && typeof this.view.renderPagination === 'function') {
            this.view.renderPagination(this.metadata);
        }
    }

    async searchUsers(query) {
        if (query.length < 2) {
            this.view.hideUserResults();
            return;
        }

        // Check cache first
        if (this.usersCache.has(query)) {
            this.view.showUserResults(this.usersCache.get(query));
            return;
        }

        try {
            const users = await SubscriptionService.searchUsers(query);
            this.usersCache.set(query, users);
            this.view.showUserResults(users);
        } catch (error) {
            console.error('Error searching users:', error);
            this.view.hideUserResults();
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
