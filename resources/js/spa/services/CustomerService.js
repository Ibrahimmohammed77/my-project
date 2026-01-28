import axios from 'axios';
import { Customer } from '../models/Customer';

export class CustomerService {
    static async getAll() {
        try {
            const response = await axios.get('/customers');
            return response.data.data.customers.map(data => Customer.fromJson(data));
        } catch (error) {
            console.error('Error fetching customers:', error);
            throw error;
        }
    }

    static async create(customer) {
        try {
            const response = await axios.post('/customers', customer.toJson());
            return response.data;
        } catch (error) {
            console.error('Error creating customer:', error);
            throw error;
        }
    }

    static async update(id, customer) {
        try {
            const response = await axios.put(`/customers/${id}`, customer.toJson());
            return response.data;
        } catch (error) {
            console.error('Error updating customer:', error);
            throw error;
        }
    }

    static async delete(id) {
        try {
            await axios.delete(`/customers/${id}`);
        } catch (error) {
            console.error('Error deleting customer:', error);
            throw error;
        }
    }
}
