import StudioCardService from '../../../../shared/services/StudioCardService.js';
import { CardListView } from '../views/CardListView.js';
import { Toast } from '../../../../core/ui/Toast.js';

export class CardListController {
    constructor() {
        this.view = new CardListView();
        this.cards = [];
        this.init();
    }

    async init() {
        this.view.bindSearch(this.handleSearch.bind(this));
        await this.loadCards();
    }

    async loadCards() {
        try {
            this.cards = await StudioCardService.getAll();
            this.view.renderTable(this.cards);
        } catch (error) {
            Toast.error('خطأ في تحميل الكروت');
        }
    }

    handleSearch(query) {
        const lower = query.toLowerCase();
        const filtered = this.cards.filter(c => 
            c.card_number.toLowerCase().includes(lower) || 
            (c.title && c.title.toLowerCase().includes(lower))
        );
        this.view.renderTable(filtered);
    }
}

