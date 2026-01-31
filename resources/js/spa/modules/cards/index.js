import { CardController } from './controllers/CardController.js';

const initCards = () => {
    if (!window.cardController) {
        new CardController();
    }
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCards);
} else {
    initCards();
}
