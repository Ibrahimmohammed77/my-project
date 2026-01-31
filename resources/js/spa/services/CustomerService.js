import ApiClient from '../core/api/ApiClient.js';
import { Customer } from '../models/Customer.js';

export class CustomerService {
    static async getAll() {
        try {
            const response = await ApiClient.get('/admin/customers');
            return response.data.data.customers.map(data => Customer.fromJson(data));
        } catch (error) {
            console.error('Error fetching customers:', error);
            throw error;
        }
    }

    static async create(customer) {
        try {
            const data = customer instanceof Customer ? customer.toJson() : customer;
            const response = await ApiClient.post('/admin/customers', data);
            return response.data;
        } catch (error) {
            console.error('Error creating customer:', error);
            throw error;
        }
    }

    static async update(id, customer) {
        try {
            const data = customer instanceof Customer ? customer.toJson() : customer;
            const response = await ApiClient.put(`/admin/customers/${id}`, data);
            return response.data;
        } catch (error) {
            console.error('Error updating customer:', error);
            throw error;
        }
    }

    static async delete(id) {
        try {
            await ApiClient.delete(`/admin/customers/${id}`);
            return true;
        } catch (error) {
            console.error('Error deleting customer:', error);
            throw error;
        }
    }
}

export default CustomerService;
