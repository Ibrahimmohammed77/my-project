import axios from 'axios';
import { LookupMaster } from '../models/LookupMaster';

export class LookupService {
    static async getAll() {
        try {
            const response = await axios.get('/lookups');
            return response.data.data.masters.map(masterData => LookupMaster.fromJson(masterData));
        } catch (error) {
            console.error('Error fetching lookups:', error);
            throw error;
        }
    }

    static async createValue(data) {
        try {
            const response = await axios.post('/admin/lookups/values', data);
            return response.data;
        } catch (error) {
            console.error('Error creating lookup value:', error);
            throw error;
        }
    }

    static async updateValue(id, data) {
        try {
            const response = await axios.put(`/admin/lookups/values/${id}`, data);
            return response.data;
        } catch (error) {
            console.error('Error updating lookup value:', error);
            throw error;
        }
    }

    static async deleteValue(id) {
        try {
            await axios.delete(`/admin/lookups/values/${id}`);
        } catch (error) {
            console.error('Error deleting lookup value:', error);
            throw error;
        }
    }
}
