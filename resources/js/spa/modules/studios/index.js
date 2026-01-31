/**
 * Studios Module Entry Point
 * Same pattern as accounts: Controller + View, global handlers for Blade
 */

import '../../../bootstrap.js';
import StudioController from './controllers/StudioController.js';

let studioController = null;

function initStudiosModule() {
    studioController = new StudioController();

    window.studioController = studioController;
    window.showCreateModal = () => studioController.showCreateModal();
    window.closeModal = () => studioController.closeModal();
    window.editStudio = (id) => studioController.editStudio(id);
    window.deleteStudio = (id) => studioController.deleteStudio(id);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initStudiosModule);
} else {
    initStudiosModule();
}

export { studioController, StudioController };
