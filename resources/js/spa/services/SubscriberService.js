import axios from 'axios';
import { Subscriber } from '../models/Subscriber';

export class SubscriberService {
    static async getAll() {
        try {
            const response = await axios.get('/subscribers');
            return response.data.data.subscribers.map(data => Subscriber.fromJson(data));
        } catch (error) {
            console.error('Error fetching subscribers:', error);
            throw error;
        }
    }

    static async create(subscriber) {
        try {
            const response = await axios.post('/subscribers', subscriber.toJson());
            return response.data;
        } catch (error) {
            console.error('Error creating subscriber:', error);
            throw error;
        }
    }

    static async update(id, subscriber) {
        try {
            const response = await axios.put(`/subscribers/${id}`, subscriber.toJson());
            return response.data;
        } catch (error) {
            console.error('Error updating subscriber:', error);
            throw error;
        }
    }

    static async delete(id) {
        try {
            await axios.delete(`/subscribers/${id}`);
        } catch (error) {
            console.error('Error deleting subscriber:', error);
            throw error;
        }
    }
}
