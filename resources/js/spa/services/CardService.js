import axios from 'axios';
import { CardGroup } from '../models/CardGroup';
import { Card } from '../models/Card';

export class CardService {
    static async getAllGroups() {
        try {
            const response = await axios.get('/admin/cards');
            const groupsData = response.data.data?.groups?.data || response.data.data?.groups || [];
            return groupsData.map(groupData => CardGroup.fromJson(groupData));
        } catch (error) {
            console.error('Detailed error fetching card groups:', error.response || error);
            throw error;
        }
    }

    static async createGroup(group) {
        try {
            const response = await axios.post('/admin/cards/groups', group.toJson());
            return response.data;
        } catch (error) {
            console.error('Error creating card group:', error);
            throw error;
        }
    }

    static async updateGroup(id, group) {
        try {
            const response = await axios.put(`/admin/cards/groups/${id}`, group.toJson());
            return response.data;
        } catch (error) {
            console.error('Error updating card group:', error);
            throw error;
        }
    }

    static async deleteGroup(id) {
        try {
            await axios.delete(`/admin/cards/groups/${id}`);
        } catch (error) {
            console.error('Error deleting card group:', error);
            throw error;
        }
    }

    static async getGroupCards(groupId) {
        try {
            const response = await axios.get(`/admin/cards/groups/${groupId}/cards`);
            // The controller returns { success: true, data: { cards: { data: [...] } } }
            const cardsData = response.data.data?.cards?.data || response.data.data?.cards || [];
            return cardsData.map(cardData => Card.fromJson(cardData));
        } catch (error) {
            console.error('Error fetching cards for group:', error);
            throw error;
        }
    }
}
