import axios from 'axios';

export class StudioStorageService {
    static async getAll() {
        try {
            const response = await axios.get('/studio/storage/libraries');
            return response.data.data;
        } catch (error) {
            console.error('Error fetching storage libraries:', error);
            throw error;
        }
    }

    static async create(data) {
        try {
            const response = await axios.post('/studio/storage/libraries', data);
            return response.data;
        } catch (error) {
            console.error('Error creating storage library:', error);
            throw error;
        }
    }

    static async update(id, data) {
        try {
            const response = await axios.put(`/studio/storage/libraries/${id}`, data);
            return response.data;
        } catch (error) {
            console.error('Error updating storage library:', error);
            throw error;
        }
    }

    static async delete(id) {
        try {
            const response = await axios.delete(`/studio/storage/libraries/${id}`);
            return response.data;
        } catch (error) {
            console.error('Error deleting storage library:', error);
            throw error;
        }
    }

    static async getCustomers() {
        try {
            const response = await axios.get('/studio/customers');
            return response.data.data.customers;
        } catch (error) {
            console.error('Error fetching customers:', error);
            throw error;
        }
    }
}
