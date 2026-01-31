import ApiClient from '../core/api/ApiClient.js';

export class StudioCustomerService {
    static async getAll() {
        try {
            const response = await ApiClient.get('/studio/customers');
            return response.data.data.customers;
        } catch (error) {
            console.error('Error fetching studio customers:', error);
            throw error;
        }
    }
}

export default StudioCustomerService;
