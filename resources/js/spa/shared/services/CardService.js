import ApiClient from '../../core/api/ApiClient.js';
import { CardGroup } from '../models/CardGroup.js';
import { Card } from '../models/Card.js';

export class CardService {
    static async getAllGroups() {
        try {
            const response = await ApiClient.get('/admin/cards');
            const groupsData = response.data.data?.groups?.data || response.data.data?.groups || [];
            return groupsData.map(groupData => CardGroup.fromJson(groupData));
        } catch (error) {
            console.error('Detailed error fetching card groups:', error.response || error);
            throw error;
        }
    }

    static async createGroup(group) {
        try {
            const data = group instanceof CardGroup ? group.toJson() : group;
            const response = await ApiClient.post('/admin/cards/groups', data);
            return response.data;
        } catch (error) {
            console.error('Error creating card group:', error);
            throw error;
        }
    }

    static async updateGroup(id, group) {
        try {
            const data = group instanceof CardGroup ? group.toJson() : group;
            const response = await ApiClient.put(`/admin/cards/groups/${id}`, data);
            return response.data;
        } catch (error) {
            console.error('Error updating card group:', error);
            throw error;
        }
    }

    static async deleteGroup(id) {
        try {
            await ApiClient.delete(`/admin/cards/groups/${id}`);
            return true;
        } catch (error) {
            console.error('Error deleting card group:', error);
            throw error;
        }
    }

    static async getGroupCards(groupId) {
        try {
            const response = await ApiClient.get(`/admin/cards/groups/${groupId}/cards`);
            const cardsData = response.data.data?.cards?.data || response.data.data?.cards || [];
            return cardsData.map(cardData => Card.fromJson(cardData));
        } catch (error) {
            console.error('Error fetching cards for group:', error);
            throw error;
        }
    }
}

export default CardService;
