/**
 * Subscribers Module Entry Point
 * Same pattern as accounts: Controller + View, global handlers for Blade
 */

import '../../../bootstrap.js';
import SubscriberController from './controllers/SubscriberController.js';

let subscriberController = null;

function initSubscribersModule() {
    subscriberController = new SubscriberController();

    window.subscriberController = subscriberController;
    window.showCreateModal = () => subscriberController.showCreateModal();
    window.closeModal = () => subscriberController.closeModal();
    window.editSubscriber = (id) => subscriberController.editSubscriber(id);
    window.deleteSubscriber = (id) => subscriberController.deleteSubscriber(id);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSubscribersModule);
} else {
    initSubscribersModule();
}

export { subscriberController, SubscriberController };
