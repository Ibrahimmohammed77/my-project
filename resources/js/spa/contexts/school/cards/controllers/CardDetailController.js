import { SchoolCardService } from '../services/SchoolCardService.js';
import { CardDetailView } from '../views/CardDetailView.js';
import { Toast } from '../../../../core/ui/Toast.js';

export class CardDetailController {
    constructor() {
        this.view = new CardDetailView();
        // Assume card-id input exists or get from URL.
        // Studio migration added hidden input. School should too?
        // Let's check logic implies hidden input or extracting from URL.
        // I will use hidden input if present, else URL logic as fallback.
        this.cardId = document.getElementById('card-id')?.value || window.location.pathname.split('/').pop();
        this.init();
    }

    init() {
        this.view.bindSubmit(this.handleSubmit.bind(this));
    }

    async handleSubmit(albumIds) {
        try {
            this.view.setLoading(true);
            const response = await SchoolCardService.linkAlbums(this.cardId, albumIds);
            
            if (response.data.success) {
                Toast.success(response.data.message);
                setTimeout(() => {
                    window.location.href = '/school/cards';
                }, 1500);
            }
        } catch (error) {
            Toast.error(error.response?.data?.message || 'خطأ في الربط');
        } finally {
            this.view.setLoading(false);
        }
    }
}

