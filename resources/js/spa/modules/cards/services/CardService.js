import ApiClient from '../../../core/api/ApiClient.js';
import { API_ENDPOINTS, getEndpoint } from '../../../core/api/endpoints.js';
import { Card } from '../models/Card.js';

export class CardService {
    static async getAll() {
        const response = await ApiClient.get(API_ENDPOINTS.ADMIN.CARDS.LIST);
        if (response.data?.data?.cards) {
            return response.data.data.cards.map(c => Card.fromJson(c));
        }
        return [];
    }

    static async getAllCards(params = {}) {
        const response = await ApiClient.get(API_ENDPOINTS.ADMIN.CARDS.LIST, { params });
        if (response.data?.data?.cards) {
            return response.data.data.cards.map(c => Card.fromJson(c));
        }
        return [];
    }

    static async getAllGroups() {
        const response = await ApiClient.get(API_ENDPOINTS.ADMIN.CARDS.GROUPS.LIST);
        if (response.data?.data?.groups) {
            return response.data.data.groups;
        }
        return [];
    }

    static async createGroup(data) {
        const response = await ApiClient.post(API_ENDPOINTS.ADMIN.CARDS.GROUPS.CREATE, data);
        return response.data?.data?.group || response.data?.data;
    }

    static async updateGroup(id, data) {
        const response = await ApiClient.put(getEndpoint(API_ENDPOINTS.ADMIN.CARDS.GROUPS.UPDATE, id), data);
        return response.data?.data?.group || response.data?.data;
    }

    static async deleteGroup(id) {
        await ApiClient.delete(getEndpoint(API_ENDPOINTS.ADMIN.CARDS.GROUPS.DELETE, id));
    }

    static async getGroupCards(groupId) {
        const response = await ApiClient.get(getEndpoint(API_ENDPOINTS.ADMIN.CARDS.GROUPS.CARDS, groupId));
        if (response.data?.data?.cards) {
            return response.data.data.cards.map(c => Card.fromJson(c));
        }
        return [];
    }

    static async createCard(groupId, data) {
        const response = await ApiClient.post(getEndpoint(API_ENDPOINTS.ADMIN.CARDS.GROUPS.CARDS, groupId), data);
        return Card.fromJson(response.data?.data?.card || response.data?.data);
    }

    static async updateCard(groupId, cardId, data) {
        const response = await ApiClient.put(getEndpoint(API_ENDPOINTS.ADMIN.CARDS.GROUPS.CARDS, groupId) + `/${cardId}`, data);
        return Card.fromJson(response.data?.data?.card || response.data?.data);
    }

    static async deleteCard(groupId, cardId) {
        await ApiClient.delete(getEndpoint(API_ENDPOINTS.ADMIN.CARDS.GROUPS.CARDS, groupId) + `/${cardId}`);
    }

    static async getById(id) {
        const endpoint = getEndpoint(API_ENDPOINTS.ADMIN.CARDS.SHOW, id);
        if (!endpoint) return null;
        
        const response = await ApiClient.get(endpoint);
        if (response.data?.data?.card) {
            return Card.fromJson(response.data.data.card);
        }
        return null;
    }
}

export default CardService;


