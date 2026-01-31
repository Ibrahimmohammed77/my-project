import { CardListController } from './controllers/CardListController.js';
import { CardDetailController } from './controllers/CardDetailController.js';

document.addEventListener('DOMContentLoaded', () => {
    const listTable = document.getElementById('cards-table');
    const linkForm = document.getElementById('link-albums-form');

    if (listTable) {
        new CardListController();
    }
    
    if (linkForm) {
        new CardDetailController();
    }
});
