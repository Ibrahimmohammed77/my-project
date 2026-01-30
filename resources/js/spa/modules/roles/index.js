import { RoleController } from './controllers/RoleController.js';

// Initialize controller
const controller = new RoleController();

// Global function aliases for backward compatibility with Blade views
window.editRole = (id) => controller.editRole(id);
window.deleteRole = (id) => controller.deleteRole(id);
window.showCreateModal = () => controller.showCreateModal();
window.closeModal = () => controller.closeModal();

export { RoleController };
export default controller;
