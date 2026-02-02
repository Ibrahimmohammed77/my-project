import { CardRedemptionService } from '../../../shared/services/CardRedemptionService.js';
import { CardRedemptionView } from '../../../shared/views/CardRedemptionView.js';
import { Toast } from '../../../core/ui/Toast.js';

export class CardRedemptionController {
    constructor() {
        this.view = new CardRedemptionView();
        this.formContainer = null;
        this.resultContainer = null;
        this.myCardsContainer = null;
    }

    /**
     * Initialize redemption form
     */
    async initRedemptionForm() {
        this.formContainer = document.querySelector('#redemption-container');
        if (!this.formContainer) return;

        this.view.renderRedemptionForm(this.formContainer);
        this.resultContainer = document.querySelector('#redemption-result');

        const form = document.querySelector('#redemption-form');
        if (form) {
            form.addEventListener('submit', (e) => this.handleRedemption(e));
        }
    }

    /**
     * Initialize my cards page
     */
    async initMyCards() {
        this.myCardsContainer = document.querySelector('#my-cards-container');
        if (!this.myCardsContainer) return;

        await this.loadMyCards();

        // Attach event listener for viewing albums
        this.myCardsContainer.addEventListener('click', (e) => {
            const viewBtn = e.target.closest('.view-album');
            if (viewBtn) {
                const albumId = viewBtn.dataset.albumId;
                window.location.href = `/albums/${albumId}`;
            }
        });
    }

    /**
     * Handle card redemption
     */
    async handleRedemption(e) {
        e.preventDefault();
        
        const form = e.target;
        const formData = new FormData(form);
        const cardNumber = formData.get('card_number');

        if (!cardNumber) {
            Toast.error('الرجاء إدخال رقم الكرت');
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التحقق...';

        try {
            const data = await CardRedemptionService.redeemCard(cardNumber);
            this.view.showSuccess(data, this.resultContainer);
            form.reset();
            Toast.success('تم استخدام الكرت بنجاح!');
        } catch (error) {
            console.error('Redemption error:', error);
            const message = error.response?.data?.message || 'فشل استخدام الكرت';
            Toast.error(message);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check"></i> استخدام الكرت';
        }
    }

    /**
     * Load user's cards
     */
    async loadMyCards() {
        try {
            const cards = await CardRedemptionService.getMyCards();
            this.view.renderMyCards(cards, this.myCardsContainer);
        } catch (error) {
            console.error('Error loading cards:', error);
            Toast.error('فشل تحميل الكروت');
        }
    }
}

export default CardRedemptionController;
