import ApiClient from '../core/api/ApiClient.js';
import { Plan } from '../models/Plan.js';

export class PlanService {
    static async getAll(params = {}) {
        try {
            const response = await ApiClient.get('/admin/plans', { params });
            return {
                items: response.data?.data?.plans?.map(planData => Plan.fromJson(planData)) || [],
                meta: response.data?.meta
            };
        } catch (error) {
            console.error('Error fetching plans:', error);
            throw error;
        }
    }

    static async create(plan) {
        try {
            const data = plan instanceof Plan ? plan.toJson() : plan;
            const response = await ApiClient.post('/admin/plans', data);
            return Plan.fromJson(response.data?.data?.plan);
        } catch (error) {
            console.error('Error creating plan:', error);
            throw error;
        }
    }

    static async update(id, plan) {
        try {
            const data = plan instanceof Plan ? plan.toJson() : plan;
            const response = await ApiClient.put(`/admin/plans/${id}`, data);
            return Plan.fromJson(response.data?.data?.plan);
        } catch (error) {
            console.error('Error updating plan:', error);
            throw error;
        }
    }

    static async delete(id) {
        try {
            await ApiClient.delete(`/admin/plans/${id}`);
            return true;
        } catch (error) {
            console.error('Error deleting plan:', error);
            throw error;
        }
    }
}

export default PlanService;
