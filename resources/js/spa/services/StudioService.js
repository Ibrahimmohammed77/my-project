import ApiClient from '../core/api/ApiClient';
import { Studio } from '../models/Studio';
import { API_ENDPOINTS, getEndpoint } from '../core/api/endpoints';

export class StudioService {
    static async getAll() {
        try {
            const response = await ApiClient.get(API_ENDPOINTS.ADMIN.STUDIOS.LIST);
            return response.data.data.studios.map(studioData => Studio.fromJson(studioData));
        } catch (error) {
            console.error('Error fetching studios:', error);
            throw error;
        }
    }

    static async create(studio) {
        try {
            const response = await ApiClient.post(API_ENDPOINTS.ADMIN.STUDIOS.CREATE, studio.toJson());
            return response.data;
        } catch (error) {
            console.error('Error creating studio:', error);
            throw error;
        }
    }

    static async update(id, studio) {
        try {
            const response = await ApiClient.put(getEndpoint(API_ENDPOINTS.ADMIN.STUDIOS.UPDATE, id), studio.toJson());
            return response.data;
        } catch (error) {
            console.error('Error updating studio:', error);
            throw error;
        }
    }

    static async delete(id) {
        try {
            await ApiClient.delete(getEndpoint(API_ENDPOINTS.ADMIN.STUDIOS.DELETE, id));
        } catch (error) {
            console.error('Error deleting studio:', error);
            throw error;
        }
    }
}
