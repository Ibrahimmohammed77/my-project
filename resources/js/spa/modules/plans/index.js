import { PlanController } from './controllers/PlanController.js';

const controller = new PlanController();

window.showCreateModal = () => controller.showCreateModal();
window.editPlan = (id) => controller.editPlan(id);
window.deletePlan = (id) => controller.deletePlan(id);
window.closeModal = () => controller.closeModal();

export { PlanController };
export default controller;
