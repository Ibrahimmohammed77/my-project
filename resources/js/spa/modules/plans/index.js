import { PlanController } from './controllers/PlanController.js';

const controller = new PlanController();

window.showCreateModal = () => controller.showCreateModal();
window.viewPlan = (id) => controller.viewPlan(id);
window.editPlan = (id) => controller.editPlan(id);
window.deletePlan = (id) => controller.deletePlan(id);
window.closeModal = () => controller.closeModal();
window.closeDetailsModal = () => controller.closeDetailsModal();

export { PlanController };
export default controller;
