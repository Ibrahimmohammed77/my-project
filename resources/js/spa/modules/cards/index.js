import { CardController } from './controllers/CardController.js';

// Initialize immediately to ensure globals are available for Blade onclick handlers
const cardController = new CardController();

// Expose to window
window.cardController = cardController;
window.showCreateModal = () => cardController.showCreateModal();
window.showCreateGroupModal = () => cardController.showCreateGroupModal();
window.closeGroupModal = () => cardController.view.closeGroupModal();
window.showCreateCardModal = () => cardController.showCreateModal();
window.closeModal = () => cardController.view.closeModal();
window.editGroup = (id) => cardController.editGroup(id);
window.deleteGroup = (id) => cardController.deleteGroup(id);
window.editCard = (id) => cardController.editCard(id);
window.deleteCard = (id) => cardController.deleteCard(id);

export default cardController;
