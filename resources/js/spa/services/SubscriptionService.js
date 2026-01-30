import axios from 'axios';
import { Subscription } from '../models/Subscription';

export const SubscriptionService = {
    async getAll(filters = {}) {
        const response = await axios.get('/admin/subscriptions', { params: filters });
        return response.data.data.map(item => new Subscription(item));
    },

    async save(data) {
        // Only support store for now as it's a "Grant" logic
        const response = await axios.post('/admin/subscriptions', data);
        return new Subscription(response.data.data.subscription);
    },

    async delete(id) {
        await axios.delete(`/admin/subscriptions/${id}`);
    }
};
