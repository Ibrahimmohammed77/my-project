import axios from 'axios';
import { CardGroup } from '../models/CardGroup';

export class CardService {
    static async getAllGroups() {
        try {
            const response = await axios.get('/cards');
            return response.data.data.groups.map(groupData => CardGroup.fromJson(groupData));
        } catch (error) {
            console.error('Error fetching card groups:', error);
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
}
