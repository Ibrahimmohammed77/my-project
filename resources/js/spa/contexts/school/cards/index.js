import { CardListController } from './controllers/CardListController.js';
import { CardDetailController } from './controllers/CardDetailController.js';

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('cards-table')) {
        new CardListController();
    } else if (document.getElementById('link-albums-form')) {
        new CardDetailController();
    }
});
