import { PermissionController } from './controllers/PermissionController.js';

const controller = new PermissionController();

window.editPermission = (id) => controller.editPermission(id);
window.deletePermission = (id) => controller.deletePermission(id);
window.showCreateModal = () => controller.showCreateModal();
window.closeModal = () => controller.closeModal();

export { PermissionController };
export default controller;
