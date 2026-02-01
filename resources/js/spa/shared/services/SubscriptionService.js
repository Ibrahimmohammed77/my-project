import { Subscription } from '../models/Subscription.js';
import ApiClient from '../../core/api/ApiClient.js';

export class SubscriptionService {
    static async getAll(filters = {}) {
        try {
            const response = await ApiClient.get('/admin/subscriptions', { params: filters });
            return {
                items: response.data?.data?.subscriptions?.map(Subscription.fromJson) || [],
                meta: response.data?.meta
            };
        } catch (error) {
            console.error('Error fetching subscriptions:', error);
            throw error;
        }
    }

    static async get(id) {
        try {
            const response = await ApiClient.get(`/admin/subscriptions/${id}`);
            return Subscription.fromJson(response.data?.data?.subscription);
        } catch (error) {
            console.error(`Error fetching subscription ${id}:`, error);
            throw error;
        }
    }

    static async create(data) {
        try {
            const response = await ApiClient.post('/admin/subscriptions', data);
            return Subscription.fromJson(response.data?.data?.subscription);
        } catch (error) {
            console.error('Error creating subscription:', error);
            throw error;
        }
    }

    static async update(id, data) {
        try {
            const response = await ApiClient.put(`/admin/subscriptions/${id}`, data);
            return Subscription.fromJson(response.data?.data?.subscription);
        } catch (error) {
            console.error(`Error updating subscription ${id}:`, error);
            throw error;
        }
    }

    static async delete(id) {
        try {
            await ApiClient.delete(`/admin/subscriptions/${id}`);
            return true;
        } catch (error) {
            console.error(`Error deleting subscription ${id}:`, error);
            throw error;
        }
    }

    static async searchUsers(query, role = null) {
        try {
            const params = { q: query };
            
            if (role) {
                params.roles = [role];
            } else {
                params.roles = ['customer', 'studio_owner', 'school_owner'];
            }

            const response = await ApiClient.get('/admin/users/search', { params });
            return response.data?.data?.users || [];
        } catch (error) {
            console.error('Error searching users:', error);
            throw error;
        }
    }


    static async getPlans() {
        try {
            const response = await ApiClient.get('/admin/plans', { params: { is_active: 1, per_page: 100 } });
            return response.data?.data?.plans || [];
        } catch (error) {
            console.error('Error fetching plans:', error);
            throw error;
        }
    }
}

export default SubscriptionService;

