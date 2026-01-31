import { SchoolCardService } from '../services/SchoolCardService.js';
import { CardListView } from '../views/CardListView.js';
import { Toast } from '../../../../core/ui/Toast.js';

export class CardListController {
    constructor() {
        this.view = new CardListView();
        this.filters = { search: '', status: '', type: '' };
        this.init();
    }

    async init() {
        this.view.bindSearch(this.handleFilter.bind(this));
        await this.loadCards();
    }

    async loadCards() {
        try {
            const params = {};
            if (this.filters.search) params.search = this.filters.search;
            if (this.filters.status) params.status_id = this.filters.status;
            if (this.filters.type) params.type_id = this.filters.type;
            
            const response = await SchoolCardService.getAll(params);
            this.view.renderTable(response.data.data.cards);
        } catch (error) {
            Toast.error('خطأ في تحميل الكروت');
        }
    }

    handleFilter(updates) {
        this.filters = { ...this.filters, ...updates };
        // Debounce if search
        if (updates.search !== undefined) {
             if (this.searchTimeout) clearTimeout(this.searchTimeout);
             this.searchTimeout = setTimeout(() => this.loadCards(), 500);
        } else {
            this.loadCards();
        }
    }
}

