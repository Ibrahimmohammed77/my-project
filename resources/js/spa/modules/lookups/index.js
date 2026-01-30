import { LookupController } from './controllers/LookupController.js';

const controller = new LookupController();

window.editMaster = (id) => controller.editMaster(id);
window.editValue = (valId) => controller.editValue(valId);
window.deleteValue = (valId) => controller.deleteValue(valId);
window.closeModal = () => controller.closeModal();
window.resetValueForm = () => controller.view.resetValueForm();

export { LookupController };
export default controller;
