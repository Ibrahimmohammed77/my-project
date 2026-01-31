import ApiClient from '../core/api/ApiClient.js';

export class StudioCardService {
    static async getAll() {
        try {
            const response = await ApiClient.get('/studio/cards');
            return response.data.data.cards;
        } catch (error) {
            console.error('Error fetching studio cards:', error);
            throw error;
        }
    }

    static async getDetail(id) {
        try {
            const response = await ApiClient.get(`/studio/cards/${id}`);
            return response.data.data;
        } catch (error) {
            console.error('Error fetching card detail:', error);
            throw error;
        }
    }

    static async linkAlbums(cardId, albumIds) {
        try {
            const response = await ApiClient.post(`/studio/cards/${cardId}/link-albums`, {
                album_ids: albumIds
            });
            return response.data;
        } catch (error) {
            console.error('Error linking albums to card:', error);
            throw error;
        }
    }
}

export default StudioCardService;
