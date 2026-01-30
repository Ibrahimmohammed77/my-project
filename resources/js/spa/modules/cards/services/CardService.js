import ApiClient from '../../../core/api/ApiClient.js';
import { API_ENDPOINTS, getEndpoint } from '../../../core/api/endpoints.js';
import { Card } from '../models/Card.js';

export class CardService {
    static async getAll() {
        const response = await ApiClient.get(API_ENDPOINTS.ADMIN.CARDS.LIST);
        if (response.data && response.data.data && response.data.data.cards) {
            return response.data.data.cards.map(c => Card.fromJson(c));
        }
        return [];
    }

    static async getAllGroups() {
        const response = await ApiClient.get(API_ENDPOINTS.ADMIN.CARDS.GROUPS.LIST);
        if (response.data && response.data.data) {
            return response.data.data;
        }
        return [];
    }

    static async getById(id) {
        const response = await ApiClient.get(getEndpoint(API_ENDPOINTS.ADMIN.CARDS.SHOW, id));
        if (response.data && response.data.data && response.data.data.card) {
            return Card.fromJson(response.data.data.card);
        }
        return null;
    }

    static async getGroupCards(groupId) {
        const response = await ApiClient.get(getEndpoint(API_ENDPOINTS.ADMIN.CARDS.GROUPS.CARDS, groupId));
        if (response.data && response.data.data && response.data.data.cards) {
            return response.data.data.cards.map(c => Card.fromJson(c));
        }
        return [];
    }
}

export default CardService;
