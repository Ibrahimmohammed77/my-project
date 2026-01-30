import ApiClient from '../../../core/api/ApiClient.js';
import { API_ENDPOINTS, getEndpoint } from '../../../core/api/endpoints.js';
import { Plan } from '../models/Plan.js';

export class PlanService {
    static async getAll() {
        const response = await ApiClient.get(API_ENDPOINTS.ADMIN.PLANS.LIST);
        if (response.data && response.data.data && response.data.data.plans) {
            return response.data.data.plans.map(p => Plan.fromJson(p));
        }
        return [];
    }

    static async create(planData) {
        const data = planData instanceof Plan ? planData.toJson() : planData;
        const response = await ApiClient.post(API_ENDPOINTS.ADMIN.PLANS.CREATE, data);
        if (response.data && response.data.data && response.data.data.plan) {
            return Plan.fromJson(response.data.data.plan);
        }
        return response.data;
    }

    static async update(id, planData) {
        const data = planData instanceof Plan ? planData.toJson() : planData;
        const response = await ApiClient.put(getEndpoint(API_ENDPOINTS.ADMIN.PLANS.UPDATE, id), data);
        if (response.data && response.data.data && response.data.data.plan) {
            return Plan.fromJson(response.data.data.plan);
        }
        return response.data;
    }

    static async delete(id) {
        await ApiClient.delete(getEndpoint(API_ENDPOINTS.ADMIN.PLANS.DELETE, id));
    }
}

export default PlanService;
