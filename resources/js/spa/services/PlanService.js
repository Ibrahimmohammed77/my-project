import axios from 'axios';
import { Plan } from '../models/Plan';

export class PlanService {
    static async getAll() {
        try {
            const response = await axios.get('/plans');
            // Check if data is paginated or simple array
            const plansData = response.data.data.plans.data || response.data.data.plans;
            return plansData.map(planData => Plan.fromJson(planData));
        } catch (error) {
            console.error('Error fetching plans:', error);
            throw error;
        }
    }

    static async create(plan) {
        try {
            const response = await axios.post('/admin/plans', plan.toJson());
            return response.data;
        } catch (error) {
            console.error('Error creating plan:', error);
            throw error;
        }
    }

    static async update(id, plan) {
        try {
            const response = await axios.put(`/admin/plans/${id}`, plan.toJson());
            return response.data;
        } catch (error) {
            console.error('Error updating plan:', error);
            throw error;
        }
    }

    static async delete(id) {
        try {
            await axios.delete(`/admin/plans/${id}`);
        } catch (error) {
            console.error('Error deleting plan:', error);
            throw error;
        }
    }
}
