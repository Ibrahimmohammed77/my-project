/**
 * Schools Module Entry Point
 * Same pattern as accounts: Controller + View, global handlers for Blade
 */

import '../../../bootstrap.js';
import SchoolController from './controllers/SchoolController.js';

let schoolController = null;

function initSchoolsModule() {
    schoolController = new SchoolController();

    window.schoolController = schoolController;
    window.showCreateModal = () => schoolController.showCreateModal();
    window.closeModal = () => schoolController.closeModal();
    window.editSchool = (id) => schoolController.editSchool(id);
    window.deleteSchool = (id) => schoolController.deleteSchool(id);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSchoolsModule);
} else {
    initSchoolsModule();
}

export { schoolController, SchoolController };
