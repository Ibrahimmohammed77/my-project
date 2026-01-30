import ApiClient from '../../../core/api/ApiClient.js';
import { API_ENDPOINTS, getEndpoint } from '../../../core/api/endpoints.js';
import { Lookup } from '../models/Lookup.js';

export class LookupService {
    static async getAll() {
        const response = await ApiClient.get(API_ENDPOINTS.ADMIN.LOOKUPS.LIST);
        if (response.data && response.data.data && response.data.data.lookups) {
            return response.data.data.lookups.map(l => Lookup.fromJson(l));
        }
        return [];
    }

    static async create(lookupData) {
        const data = lookupData instanceof Lookup ? lookupData.toJson() : lookupData;
        const response = await ApiClient.post(API_ENDPOINTS.ADMIN.LOOKUPS.CREATE, data);
        if (response.data && response.data.data && response.data.data.lookup) {
            return Lookup.fromJson(response.data.data.lookup);
        }
        return response.data;
    }

    static async update(id, lookupData) {
        const data = lookupData instanceof Lookup ? lookupData.toJson() : lookupData;
        const response = await ApiClient.put(getEndpoint(API_ENDPOINTS.ADMIN.LOOKUPS.UPDATE, id), data);
        if (response.data && response.data.data && response.data.data.lookup) {
            return Lookup.fromJson(response.data.data.lookup);
        }
        return response.data;
    }

    static async delete(id) {
        await ApiClient.delete(getEndpoint(API_ENDPOINTS.ADMIN.LOOKUPS.DELETE, id));
    }

    static async createValue(data) {
        const response = await ApiClient.post(API_ENDPOINTS.ADMIN.LOOKUPS.VALUES.CREATE, data);
        return response.data;
    }

    static async updateValue(id, data) {
        const response = await ApiClient.put(getEndpoint(API_ENDPOINTS.ADMIN.LOOKUPS.VALUES.UPDATE, id), data);
        return response.data;
    }

    static async deleteValue(id) {
        await ApiClient.delete(getEndpoint(API_ENDPOINTS.ADMIN.LOOKUPS.VALUES.DELETE, id));
    }
}

export default LookupService;
