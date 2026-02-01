import ApiClient from '../../core/api/ApiClient.js';

export class CardRedemptionService {
    /**
     * Redeem a card by card number
     */
    static async redeemCard(cardNumber) {
        const response = await ApiClient.post('/cards/redeem', {
            card_number: cardNumber
        });
        return response.data?.data || null;
    }

    /**
     * Get user's cards
     */
    static async getMyCards() {
        const response = await ApiClient.get('/my-cards');
        return response.data?.data || [];
    }
}

export default CardRedemptionService;
