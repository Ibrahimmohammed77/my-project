import ApiClient from '../core/api/ApiClient.js';
import { Subscriber } from '../models/Subscriber.js';

export class SubscriberService {
    static async getAll() {
        try {
            const response = await ApiClient.get('/admin/subscribers');
            return response.data?.data?.subscribers?.map(data => Subscriber.fromJson(data)) || [];
        } catch (error) {
            console.error('Error fetching subscribers:', error);
            throw error;
        }
    }

    static async create(subscriber) {
        try {
            const data = subscriber instanceof Subscriber ? subscriber.toJson() : subscriber;
            const response = await ApiClient.post('/admin/subscribers', data);
            return response.data;
        } catch (error) {
            console.error('Error creating subscriber:', error);
            throw error;
        }
    }

    static async update(id, subscriber) {
        try {
            const data = subscriber instanceof Subscriber ? subscriber.toJson() : subscriber;
            const response = await ApiClient.put(`/admin/subscribers/${id}`, data);
            return response.data;
        } catch (error) {
            console.error('Error updating subscriber:', error);
            throw error;
        }
    }

    static async delete(id) {
        try {
            await ApiClient.delete(`/admin/subscribers/${id}`);
            return true;
        } catch (error) {
            console.error('Error deleting subscriber:', error);
            throw error;
        }
    }
}

export default SubscriberService;
