import ApiClient from '../../../core/api/ApiClient.js';
import { API_ENDPOINTS, getEndpoint } from '../../../core/api/endpoints.js';
import { Customer } from '../models/Customer.js';

export class CustomerService {
    static async getAll() {
        const response = await ApiClient.get(API_ENDPOINTS.ADMIN.CUSTOMERS.LIST);
        if (response.data && response.data.data && response.data.data.customers) {
            return response.data.data.customers.map(c => Customer.fromJson(c));
        }
        return [];
    }

    static async getById(id) {
        const response = await ApiClient.get(getEndpoint(API_ENDPOINTS.ADMIN.CUSTOMERS.SHOW, id));
        if (response.data && response.data.data && response.data.data.customer) {
            return Customer.fromJson(response.data.data.customer);
        }
        return null;
    }

    static async delete(id) {
        await ApiClient.delete(getEndpoint(API_ENDPOINTS.ADMIN.CUSTOMERS.DELETE, id));
    }
}

export default CustomerService;
