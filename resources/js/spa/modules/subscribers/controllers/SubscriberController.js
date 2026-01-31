/**
 * Subscriber Controller
 * Same pattern as AccountController: load, filter, CRUD, Toast
 */

import { Subscriber } from '../../../shared/models/Subscriber.js';
import { SubscriberService } from '../../../shared/services/SubscriberService.js';
import SubscriberView from '../views/SubscriberView.js';
import { Toast } from '../../../core/ui/Toast.js';
import { InputValidator } from '../../../core/security/InputValidator.js';
import { Security } from '../../../core/security/Security.js';
import { DOM } from '../../../core/utils/dom.js';
import { debounce } from '../../../core/utils/performance.js';
import { showErrors, clearErrors } from '../../../utils/toast.js';

export class SubscriberController {
    constructor() {
        this.subscribers = [];
        this.view = new SubscriberView();
        this.currentSubscriber = null;

        this.init();
    }

    init() {
        this.attachEventListeners();
        this.loadSubscribers();
    }

    attachEventListeners() {
        const searchInput = DOM.query('#search');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(() => {
                this.filterAndRender();
            }, 300));
        }

        if (this.view.tbody) {
            DOM.delegate(this.view.tbody, 'click', '[data-action="edit"]', (e) => {
                const btn = e.target.closest('button');
                if (btn) {
                    const subscriberId = parseInt(btn.dataset.subscriberId, 10);
                    this.editSubscriber(subscriberId);
                }
            });

            DOM.delegate(this.view.tbody, 'click', '[data-action="delete"]', (e) => {
                const btn = e.target.closest('button');
                if (btn) {
                    const subscriberId = parseInt(btn.dataset.subscriberId, 10);
                    this.deleteSubscriber(subscriberId);
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

    async loadSubscribers() {
        this.view.showLoading();

        try {
            this.subscribers = await SubscriberService.getAll();
            this.filterAndRender();
        } catch (error) {
            console.error('Failed to load subscribers:', error);
            Toast.error('فشل تحميل المشتركين');
        } finally {
            this.view.hideLoading();
        }
    }

    filterAndRender() {
        const searchInput = DOM.query('#search');
        const searchTerm = (searchInput ? searchInput.value : '').toLowerCase();

        const filtered = this.subscribers.filter(subscriber => {
            if (!searchTerm) return true;
            const accountName = subscriber.account?.full_name?.toLowerCase() || '';
            const username = subscriber.account?.username?.toLowerCase() || '';
            return accountName.includes(searchTerm) || username.includes(searchTerm);
        });

        this.view.render(filtered);
    }

    showCreateModal() {
        this.currentSubscriber = null;
        this.view.clearForm();
        this.view.openModal('إضافة مشترك جديد');
        clearErrors();
    }

    async editSubscriber(subscriberId) {
        const subscriber = this.subscribers.find(s => s.subscriber_id === subscriberId);

        if (!subscriber) {
            Toast.error('المشترك غير موجود');
            return;
        }

        this.currentSubscriber = subscriber;
        this.view.populateForm(subscriber);
        this.view.openModal('تعديل مشترك');
        clearErrors();
    }

    async deleteSubscriber(subscriberId) {
        const subscriber = this.subscribers.find(s => s.subscriber_id === subscriberId);

        if (!subscriber) {
            Toast.error('المشترك غير موجود');
            return;
        }

        const name = subscriber.account?.full_name || subscriber.subscriber_id;
        if (!confirm(`هل أنت متأكد من حذف المشترك "${name}"؟`)) {
            return;
        }

        Toast.info('جاري حذف المشترك...');

        try {
            await SubscriberService.delete(subscriberId);
            this.subscribers = this.subscribers.filter(s => s.subscriber_id !== subscriberId);
            this.filterAndRender();
            Toast.success('تم حذف المشترك بنجاح');
        } catch (error) {
            console.error('Failed to delete subscriber:', error);
            Toast.error('فشل حذف المشترك');
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
            if (this.currentSubscriber) {
                await SubscriberService.update(this.currentSubscriber.subscriber_id, sanitized);
            } else {
                await SubscriberService.create(sanitized);
            }

            await this.loadSubscribers();
            this.view.closeModal();
            Toast.success(this.currentSubscriber ? 'تم تحديث المشترك بنجاح' : 'تم إنشاء المشترك بنجاح');
        } catch (error) {
            console.error('Failed to save subscriber:', error);
            if (error.response && error.response.status === 422) {
                showErrors(error.response.data.errors || {});
            } else {
                Toast.error('فشل حفظ المشترك');
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
            account_id: ['required'],
            subscriber_status_id: ['required']
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

export default SubscriberController;
