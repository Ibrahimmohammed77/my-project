// resources/js/spa/modules/subscriptions/views/SubscriptionView.js
import { XssProtection } from '../../../core/security/XssProtection.js';
import { DOM } from '../../../core/utils/dom.js';

function getStatusClass(code) {
    const classes = {
        'ACTIVE': 'bg-green-100 text-green-800 border-green-200',
        'EXPIRED': 'bg-red-100 text-red-800 border-red-200',
        'CANCELLED': 'bg-gray-100 text-gray-800 border-gray-200',
        'SUSPENDED': 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'PENDING': 'bg-blue-100 text-blue-800 border-blue-200'
    };
    return classes[code] || 'bg-gray-50 text-gray-600 border-gray-100';
}

export class SubscriptionView {
    constructor(options = {}) {
        this.tbody = options.tbody || DOM.query('#subscriptions-tbody');
        this.loadingState = options.loadingState || DOM.query('#loading-state');
        this.emptyState = options.emptyState || DOM.query('#empty-state');
        this.modal = options.modal || DOM.query('#subscription-modal');
        this.modalTitle = options.modalTitle || DOM.query('#subscription-modal-title');
        this.form = options.form || DOM.query('#subscription-form');
        this.userResults = options.userResults || DOM.query('#user-results');
        this.selectedUser = options.selectedUser || DOM.query('#selected-user');

        this.paginationContainer = options.paginationContainer || DOM.query('#pagination-container');

        this.selectedUserData = null;
    }

    renderPagination(meta) {
        if (!this.paginationContainer) return;

        if (!meta || meta.last_page <= 1) {
            DOM.empty(this.paginationContainer);
            return;
        }

        let html = `
            <div class="flex items-center justify-between border-t border-gray-100 px-4 py-3 sm:px-6">
                <div class="flex flex-1 justify-between sm:hidden">
                    ${meta.current_page > 1 ? `<button onclick="subscriptionController.loadPage(${meta.current_page - 1})" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">السابق</button>` : ''}
                    ${meta.current_page < meta.last_page ? `<button onclick="subscriptionController.loadPage(${meta.current_page + 1})" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">التالي</button>` : ''}
                </div>
                <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            عرض من <span class="font-medium">${meta.from}</span> إلى <span class="font-medium">${meta.to}</span> من أصل <span class="font-medium">${meta.total}</span> نتيجة
                        </p>
                    </div>
                    <div>
                        <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                            <button onclick="subscriptionController.loadPage(${meta.current_page - 1})" ${meta.current_page === 1 ? 'disabled' : ''} class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            ${this.generatePageNumbers(meta)}
                            <button onclick="subscriptionController.loadPage(${meta.current_page + 1})" ${meta.current_page === meta.last_page ? 'disabled' : ''} class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                        </nav>
                    </div>
                </div>
            </div>
        `;
        this.paginationContainer.innerHTML = html;
    }

    generatePageNumbers(meta) {
        let pages = '';
        const current = meta.current_page;
        const last = meta.last_page;
        
        for (let i = 1; i <= last; i++) {
            if (i === 1 || i === last || (i >= current - 2 && i <= current + 2)) {
                pages += `
                    <button onclick="subscriptionController.loadPage(${i})" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold ${i === current ? 'z-10 bg-accent text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent' : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0'}">
                        ${i}
                    </button>
                `;
            } else if (i === current - 3 || i === current + 3) {
                pages += `<span class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-700 ring-1 ring-inset ring-gray-300 focus:outline-offset-0">...</span>`;
            }
        }
        return pages;
    }

    showLoading() {
        DOM.show(this.loadingState);
        if (this.tbody) DOM.empty(this.tbody);
    }

    hideLoading() {
        DOM.hide(this.loadingState);
    }

    showEmpty() {
        DOM.show(this.emptyState);
        if (this.tbody?.parentElement) DOM.hide(this.tbody.parentElement);
    }

    hideEmpty() {
        DOM.hide(this.emptyState);
        if (this.tbody?.parentElement) DOM.show(this.tbody.parentElement);
    }

    render(subscriptions) {
        if (!this.tbody) return;
        DOM.empty(this.tbody);

        if (!subscriptions || subscriptions.length === 0) {
            this.showEmpty();
            return;
        }
        this.hideEmpty();

        subscriptions.forEach(subscription => {
            this.tbody.appendChild(this.createSubscriptionRow(subscription));
        });
    }

    createSubscriptionRow(subscription) {
        const tr = DOM.create('tr', {
            className: 'hover:bg-gray-50/50 transition-colors duration-200 border-b border-gray-100 last:border-0'
        });

        // User Column
        const userCell = DOM.create('td', { className: 'px-6 py-4' });
        const userDiv = DOM.create('div', { className: 'flex items-center gap-3' });

        const avatar = DOM.create('div', {
            className: 'w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center'
        });
        avatar.innerHTML = '<i class="fas fa-user text-gray-500"></i>';

        const userInfo = DOM.create('div', { className: 'flex-1' });
        const userName = DOM.create('div', { className: 'text-sm font-bold text-gray-900' });
        XssProtection.setTextContent(userName, subscription.user?.name || 'غير محدد');

        const userEmail = DOM.create('div', { className: 'text-xs text-gray-500' });
        XssProtection.setTextContent(userEmail, subscription.user?.email || '');

        userInfo.appendChild(userName);
        userInfo.appendChild(userEmail);
        userDiv.appendChild(avatar);
        userDiv.appendChild(userInfo);
        userCell.appendChild(userDiv);

        // Plan Column
        const planCell = DOM.create('td', { className: 'px-6 py-4' });
        const planDiv = DOM.create('div', { className: 'flex items-center gap-2' });
        planDiv.innerHTML = '<i class="fas fa-cube text-accent"></i>';

        const planInfo = DOM.create('div');
        const planName = DOM.create('div', { className: 'text-sm font-medium text-gray-900' });
        XssProtection.setTextContent(planName, subscription.plan?.name || 'غير محدد');

        const planStorage = DOM.create('div', { className: 'text-xs text-gray-500' });
        XssProtection.setTextContent(planStorage, `${subscription.plan?.storage_limit || 0} MB تخزين`);

        planInfo.appendChild(planName);
        planInfo.appendChild(planStorage);
        planDiv.appendChild(planInfo);
        planCell.appendChild(planDiv);

        // Status Column
        const statusCell = DOM.create('td', { className: 'px-6 py-4' });
        const statusCode = subscription.status?.code || '';
        const statusBadge = DOM.create('span', {
            className: `inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold ${getStatusClass(statusCode)} border`
        });
        statusBadge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-current"></span>';
        const statusText = DOM.create('span');
        XssProtection.setTextContent(statusText, subscription.status?.name || 'غير محدد');
        statusBadge.appendChild(statusText);
        statusCell.appendChild(statusBadge);

        // End Date Column
        const dateCell = DOM.create('td', { className: 'px-6 py-4' });
        const dateDiv = DOM.create('div', { className: 'flex flex-col gap-1' });

        const endDate = DOM.create('div', { className: 'text-sm text-gray-900' });
        XssProtection.setTextContent(endDate, subscription.getFormattedEndDate());

        const daysRemaining = DOM.create('div', { className: 'text-xs text-gray-500' });
        const days = subscription.getDaysRemaining();
        XssProtection.setTextContent(daysRemaining, `${days} أيام متبقية`);

        dateDiv.appendChild(endDate);
        dateDiv.appendChild(daysRemaining);
        dateCell.appendChild(dateDiv);

        // Auto Renew Column
        const renewCell = DOM.create('td', { className: 'px-6 py-4' });
        const renewBadge = DOM.create('span', {
            className: `inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold ${subscription.auto_renew ? 'bg-blue-100 text-blue-800 border-blue-200' : 'bg-gray-100 text-gray-800 border-gray-200'} border`
        });
        renewBadge.innerHTML = subscription.auto_renew
            ? '<i class="fas fa-sync text-xs"></i> مفعل'
            : '<i class="fas fa-ban text-xs"></i> غير مفعل';
        renewCell.appendChild(renewBadge);

        // Actions Column
        const actionsCell = DOM.create('td', { className: 'px-6 py-4 text-center' });
        const actionsDiv = DOM.create('div', { className: 'flex items-center justify-center gap-2' });

        const editBtn = DOM.create('button', {
            type: 'button',
            className: 'w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white transition-all duration-200 flex items-center justify-center shadow-sm',
            title: 'تعديل',
            dataset: { action: 'edit', subscriptionId: subscription.subscription_id }
        });
        editBtn.innerHTML = '<i class="fas fa-pen text-xs"></i>';

        const deleteBtn = DOM.create('button', {
            type: 'button',
            className: 'w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition-all duration-200 flex items-center justify-center shadow-sm',
            title: 'حذف',
            dataset: { action: 'delete', subscriptionId: subscription.subscription_id }
        });
        deleteBtn.innerHTML = '<i class="fas fa-trash text-xs"></i>';

        actionsDiv.appendChild(editBtn);
        actionsDiv.appendChild(deleteBtn);
        actionsCell.appendChild(actionsDiv);

        // Append all cells
        tr.appendChild(userCell);
        tr.appendChild(planCell);
        tr.appendChild(statusCell);
        tr.appendChild(dateCell);
        tr.appendChild(renewCell);
        tr.appendChild(actionsCell);

        return tr;
    }

    openModal(title = 'منح اشتراك جديد') {
        if (!this.modal) return;
        if (this.modalTitle) {
            const safeTitle = XssProtection.escape(title || '');
            XssProtection.setHtml(this.modalTitle, `<span class="w-2 h-6 bg-accent rounded-full"></span><span>${safeTitle}</span>`, true);
        }
        DOM.removeClass(this.modal, 'hidden');
    }

    closeModal() {
        if (!this.modal) return;
        DOM.addClass(this.modal, 'hidden');
        if (this.form) this.form.reset();
        this.clearUserSelection();
        this.hideUserResults();
        this.updatePriceDisplay(false);
    }

    populateForm(subscription) {
        if (!this.form) return;

        const fields = {
            'subscription-id': subscription.subscription_id,
            'user_id': subscription.user_id,
            'plan_id': subscription.plan_id,
            'status_id': subscription.subscription_status_id,
            'billing_cycle': subscription.billing_cycle || 'monthly',
            'auto_renew': subscription.auto_renew
        };

        Object.entries(fields).forEach(([fieldId, value]) => {
            const field = document.getElementById(fieldId);
            if (field) {
                if (field.type === 'checkbox') {
                    field.checked = !!value;
                } else {
                    field.value = value || '';
                }
            }
        });

        // Show status field in edit mode
        const statusContainer = document.getElementById('status-field-container');
        if (statusContainer) {
            statusContainer.classList.remove('hidden');
        }

        // Set selected user display
        if (subscription.user) {
            this.setSelectedUser(subscription.user);
        }

        // Update price and dates
        this.updatePrice();
        this.updateEndDate();
    }

    clearForm() {
        if (!this.form) return;
        this.form.reset();
        const subscriptionIdInput = document.getElementById('subscription-id');
        if (subscriptionIdInput) subscriptionIdInput.value = '';
        
        // Hide status field in create mode
        const statusContainer = document.getElementById('status-field-container');
        if (statusContainer) {
            statusContainer.classList.add('hidden');
        }

        this.clearUserSelection();
        this.updatePriceDisplay(false);
    }

    clearUserSelection() {
        this.selectedUserData = null;
        DOM.hide(this.selectedUser);
        const userIdInput = document.getElementById('user_id');
        if (userIdInput) userIdInput.value = '';
        const userSearchInput = document.getElementById('user-search');
        if (userSearchInput) userSearchInput.value = '';
    }

    setSelectedUser(user) {
        this.selectedUserData = user;

        const userIdInput = document.getElementById('user_id');
        if (userIdInput) userIdInput.value = user.id;

        const userNameElement = document.getElementById('selected-user-name');
        if (userNameElement) XssProtection.setTextContent(userNameElement, user.name);

        const userEmailElement = document.getElementById('selected-user-email');
        if (userEmailElement) XssProtection.setTextContent(userEmailElement, user.email);

        DOM.show(this.selectedUser);
        this.hideUserResults();
    }

    showUserResults(users) {
        if (!this.userResults) return;
        DOM.empty(this.userResults);

        if (users.length === 0) {
            const noResults = DOM.create('div', {
                className: 'p-4 text-center text-gray-500 text-sm'
            });
            XssProtection.setTextContent(noResults, 'لم يتم العثور على مستخدمين');
            this.userResults.appendChild(noResults);
        } else {
            users.forEach(user => {
                const userElement = this.createUserResultElement(user);
                this.userResults.appendChild(userElement);
            });
        }

        DOM.show(this.userResults);
    }

    hideUserResults() {
        if (this.userResults) DOM.hide(this.userResults);
    }

    renderUserResults(users, context) {
        console.log(`[SubscriptionView] Rendering ${users.length} users for context: ${context}`);
        
        const containerId = context === 'filter' ? 'user-filter-results' : 'modal-user-results';
        const container = document.getElementById(containerId);
        
        if (!container) {
            console.error(`[SubscriptionView] Container #${containerId} not found!`);
            return;
        }

        // Force visibility
        container.style.display = 'block';
        
        DOM.empty(container);

        if (!users || users.length === 0) {
            console.log('[SubscriptionView] No users to display.');
            container.innerHTML = '<div class="p-4 text-center text-gray-400 text-xs">لا توجد نتائج</div>';
            return;
        }

        users.forEach(user => {
            try {
                const el = this.createUserResultItem(user, context);
                container.appendChild(el);
            } catch (err) {
                console.error('[SubscriptionView] Error rendering user item:', user, err);
            }
        });
        
        console.log(`[SubscriptionView] Successfully appended ${container.children.length} items to container.`);
    }

    createUserResultItem(user, context) {
        const div = DOM.create('div', {
            className: 'flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors group border-b border-gray-50 last:border-0',
        });
        
        // Click handler
        div.onclick = (e) => {
            e.preventDefault();
            e.stopPropagation(); // Prevent bubbling issues
            
            if (context === 'filter') {
                // Set filter logic
                const label = document.getElementById('user-filter-label');
                if (label) label.textContent = `المستخدم: ${user.name}`;
                
                const dropdown = document.getElementById('user-filter-dropdown');
                if (dropdown) dropdown.classList.add('hidden');
                
                if (window.subscriptionController) {
                    window.subscriptionController.selectedFilterUser = user;
                    window.subscriptionController.triggerFilter();
                }
            } else {
                // Modal logic
                this.setSelectedUser(user);
                
                const dropdown = document.getElementById('modal-user-dropdown');
                if (dropdown) dropdown.classList.add('hidden');
            }
        };

        const userName = user.name || 'مستخدم غير معروف';
        const userInitial = userName.charAt(0).toUpperCase();

        const avatar = DOM.create('div', {
            className: 'w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 text-xs font-bold shrink-0'
        });
        avatar.textContent = userInitial;

        const info = DOM.create('div', { className: 'flex-1 min-w-0' });
        const name = DOM.create('div', { className: 'text-sm font-medium text-gray-900 truncate' });
        name.textContent = userName;
        
        const roleAndEmail = DOM.create('div', { className: 'text-xs text-gray-500 truncate' });
        const roleName = this.getRoleName(user);
        roleAndEmail.textContent = `${roleName} • ${user.email || ''}`;

        info.appendChild(name);
        info.appendChild(roleAndEmail);
        div.appendChild(avatar);
        div.appendChild(info);

        return div;
    }
    
    getRoleName(user) {
         const roleMap = {
            'customer': 'عميل',
            'school_owner': 'مدرسة',
            'studio_owner': 'استوديو'
        };
        return user.roles && user.roles.length > 0 ? (roleMap[user.roles[0].name] || user.roles[0].name) : '';
    }

    renderPlanResults(plans, context) {
        console.log(`[SubscriptionView] Rendering ${plans.length} plans for context: ${context}`);
        
        const containerId = context === 'filter' ? 'plan-filter-results' : 'modal-plan-results';
        const container = document.getElementById(containerId);
        
        if (!container) return;
        
        // Force visibility
        if(context === 'modal') {
             const dropdown = document.getElementById('modal-plan-dropdown');
             if(dropdown && !dropdown.classList.contains('hidden')) {
                  // ensure results container is visible
                  container.style.display = 'block';
             }
        }

        DOM.empty(container);

        if (plans.length === 0) {
            container.innerHTML = '<div class="p-4 text-center text-gray-400 text-xs">لا توجد خطط</div>';
            return;
        }

        const createPlanItem = (plan) => {
            const btn = DOM.create('button', {
                className: 'w-full text-right px-3 py-2 rounded-lg hover:bg-gray-50 text-sm font-medium text-gray-700 transition-colors flex justify-between items-center group border-b border-gray-50 last:border-0',
                type: 'button'
            });
            
            const nameSpan = DOM.create('span', { className: 'font-bold text-gray-800' });
            nameSpan.textContent = plan.name;
            
            const priceSpan = DOM.create('span', { className: 'text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-md group-hover:bg-white transition-colors' });
            priceSpan.textContent = `${plan.price_monthly} / شهر`;

            btn.appendChild(nameSpan);
            btn.appendChild(priceSpan);
            
            btn.onclick = () => {
                if(context === 'filter') {
                     this.selectPlanFilter(plan.plan_id, plan.name);
                } else {
                     this.selectPlanModal(plan);
                }
            };
            return btn;
        };

        // "All" option for filter only
        if (context === 'filter') {
            const allBtn = DOM.create('button', {
                className: 'w-full text-right px-3 py-2 rounded-lg hover:bg-gray-50 text-sm font-medium text-gray-700 transition-colors mb-1',
                textContent: 'الكل'
            });
            allBtn.onclick = () => this.selectPlanFilter(null, 'الكل');
            container.appendChild(allBtn);
        }

        plans.forEach(plan => {
            container.appendChild(createPlanItem(plan));
        });
    }
    
    selectPlanFilter(planId, planName) {
        const label = document.getElementById('plan-filter-label');
        if(label) {
            label.textContent = `الخطة: ${planName}`;
            label.dataset.planId = planId || '';
        }
        
        const dropdown = document.getElementById('plan-filter-dropdown');
        if(dropdown) dropdown.classList.add('hidden');
        
        if(window.subscriptionController) {
            window.subscriptionController.triggerFilter();
        }
    }

    selectPlanModal(plan) {
        // Update Label
        const label = document.getElementById('modal-plan-label');
        if(label) label.textContent = plan.name;
        
        // Update Hidden Input
        const input = document.getElementById('plan_id');
        if(input) {
             input.value = plan.plan_id;
             // Store prices for dynamic calculation
             input.dataset.priceMonthly = plan.price_monthly;
             input.dataset.priceYearly = plan.price_yearly;
        }

        // Close Dropdown
        const dropdown = document.getElementById('modal-plan-dropdown');
        if(dropdown) dropdown.classList.add('hidden');
        
        // Trigger generic change event for price update if needed
        // Since we are not using a select change event anymore, we must call update directly
        if(window.subscriptionController && window.subscriptionController.view) {
             window.subscriptionController.view.updatePrice();
             window.subscriptionController.view.updateEndDate();
        }
    }

    populatePlanDropdown(plans, context) {
        this.renderPlanResults(plans, context);
    }
    
    // Legacy method mostly, kept for safety but functionality moved to renderPlanResults
    populatePlanOptions(plans) {
        // Maybe populate modal data for initial search?
        // We can just call renderPlanResults with modal context
        this.renderPlanResults(plans, 'modal');
    }

    toggleSpinner(id, show) {
        const spinner = document.getElementById(id);
        if (spinner) {
            if (show) DOM.removeClass(spinner, 'hidden');
            else DOM.addClass(spinner, 'hidden');
        }
    }
    
    setSelectedUser(user) {
        this.selectedUserData = user;

        const userIdInput = document.getElementById('user_id');
        if (userIdInput) userIdInput.value = user.id;

        // Custom UI update
        const display = document.getElementById('modal-selected-user-display');
        const trigger = document.getElementById('modal-user-trigger');
        const nameEl = document.getElementById('modal-selected-user-name');
        const emailEl = document.getElementById('modal-selected-user-email');
        
        if(nameEl) nameEl.textContent = user.name;
        if(emailEl) emailEl.textContent = user.email;
        
        if(display) {
            display.classList.remove('hidden');
            display.classList.add('flex');
        }
        if(trigger) trigger.classList.add('hidden');

        // Logic for old UI components just in case
        const oldSelectedUser = document.getElementById('selected-user');
         if(oldSelectedUser) DOM.show(oldSelectedUser);
    }
    
    clearUserSelection() {
        this.selectedUserData = null;
        const userIdInput = document.getElementById('user_id');
        if (userIdInput) userIdInput.value = '';
        
        // Reset Custom UI
        const display = document.getElementById('modal-selected-user-display');
        const trigger = document.getElementById('modal-user-trigger');
        
        if(display) {
            display.classList.add('hidden');
            display.classList.remove('flex');
        }
        if(trigger) trigger.classList.remove('hidden');
        
        // Reset Dropdown state
        // We might want to reset the "Role Selection" view too?
        // Let's rely on the toggle logic resetting it
    }

    updatePrice() {
        const planSelect = document.getElementById('plan_id');
        const selectedOption = planSelect?.options[planSelect.selectedIndex];
        const billingCycle = document.getElementById('billing_cycle')?.value;

        if (selectedOption?.value && billingCycle) {
            const price = billingCycle === 'monthly'
                ? selectedOption.getAttribute('data-price-monthly')
                : selectedOption.getAttribute('data-price-yearly');

            if (price) {
                this.updatePriceDisplay(true, price, billingCycle);
            } else {
                this.updatePriceDisplay(false);
            }
        } else {
            this.updatePriceDisplay(false);
        }
    }

    updatePriceDisplay(show = false, price = '0.00', period = 'monthly') {
        const displayElement = document.getElementById('price-display');
        const priceElement = document.getElementById('selected-price');
        const periodElement = document.getElementById('price-period');

        if (show) {
            if (priceElement) XssProtection.setTextContent(priceElement, parseFloat(price).toFixed(2));
            if (periodElement) XssProtection.setTextContent(periodElement, period === 'monthly' ? '/شهر' : '/سنة');
            if (displayElement) DOM.show(displayElement);
        } else {
            if (displayElement) DOM.hide(displayElement);
        }
    }

    updateEndDate() {
        if (!this.selectedUserData) return;

        const billingCycle = document.getElementById('billing_cycle')?.value;
        if (!billingCycle) return;

        const today = new Date();
        const endDate = new Date(today);

        if (billingCycle === 'yearly') {
            endDate.setFullYear(endDate.getFullYear() + 1);
        } else {
            endDate.setMonth(endDate.getMonth() + 1);
        }

        const endDateElement = document.getElementById('end-date');
        if (endDateElement) {
            XssProtection.setTextContent(endDateElement, endDate.toLocaleDateString('ar-EG', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            }));
        }
    }

    disableForm() {
        if (this.form) DOM.disableForm(this.form);
    }

    enableForm() {
        if (this.form) DOM.enableForm(this.form);
    }

    // Event handler setters
    setOnUserSelect(callback) {
        this.onUserSelect = callback;
    }

    setOnPlanChange(callback) {
        const planSelect = document.getElementById('plan_id');
        if (planSelect) {
            planSelect.addEventListener('change', callback);
        }
    }

    setOnBillingCycleChange(callback) {
        const billingCycleSelect = document.getElementById('billing_cycle');
        if (billingCycleSelect) {
            billingCycleSelect.addEventListener('change', callback);
        }
    }

    showSearchSpinner() {
        const spinner = document.getElementById('user-search-spinner');
        if (spinner) DOM.removeClass(spinner, 'hidden');
    }

    hideSearchSpinner() {
        const spinner = document.getElementById('user-search-spinner');
        if (spinner) DOM.addClass(spinner, 'hidden');
    }
}

export default SubscriptionView;
