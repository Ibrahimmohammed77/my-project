import ApiClient from '../../core/api/ApiClient.js';
import { LookupMaster } from '../models/LookupMaster.js';

export class LookupService {
    static async getAll() {
        try {
            const response = await ApiClient.get('/admin/lookups'); // Updated to /admin/lookups if that's the correct path
            return response.data?.data?.masters?.map(masterData => LookupMaster.fromJson(masterData)) || [];
        } catch (error) {
            console.error('Error fetching lookups:', error);
            throw error;
        }
    }

    static async createValue(data) {
        try {
            const response = await ApiClient.post('/admin/lookups/values', data);
            return response.data;
        } catch (error) {
            console.error('Error creating lookup value:', error);
            throw error;
        }
    }

    static async updateValue(id, data) {
        try {
            const response = await ApiClient.put(`/admin/lookups/values/${id}`, data);
            return response.data;
        } catch (error) {
            console.error('Error updating lookup value:', error);
            throw error;
        }
    }

    static async deleteValue(id) {
        try {
            await ApiClient.delete(`/admin/lookups/values/${id}`);
            return true;
        } catch (error) {
            console.error('Error deleting lookup value:', error);
            throw error;
        }
    }
}

export default LookupService;

