import axios from 'axios';

export class StudioCustomerService {
    static async getAll() {
        try {
            const response = await axios.get('/studio/customers');
            return response.data.data.customers;
        } catch (error) {
            console.error('Error fetching studio customers:', error);
            throw error;
        }
    }

    // CRUD methods if needed
}
