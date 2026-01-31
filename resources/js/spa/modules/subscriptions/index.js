// resources/js/spa/modules/subscriptions/index.js
import '../../../bootstrap.js';
import SubscriptionController from './controllers/SubscriptionController.js';

let subscriptionController = null;

function initSubscriptionsModule() {
    subscriptionController = new SubscriptionController();

    // Expose to global scope for Blade event handlers
    window.subscriptionController = subscriptionController;
    window.showCreateModal = () => subscriptionController.showCreateModal();
    window.closeModal = () => subscriptionController.closeModal();
    window.editSubscription = (id) => subscriptionController.editSubscription(id);
    window.deleteSubscription = (id) => subscriptionController.deleteSubscription(id);
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSubscriptionsModule);
} else {
    initSubscriptionsModule();
}

export { subscriptionController, SubscriptionController };
