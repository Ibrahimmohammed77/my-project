import ApiClient from '../../core/api/ApiClient';
import { School } from '../models/School';
import { API_ENDPOINTS, getEndpoint } from '../../core/api/endpoints';

export class SchoolService {
    static async getAll() {
        try {
            const response = await ApiClient.get(API_ENDPOINTS.ADMIN.SCHOOLS.LIST);
            return response.data.data.schools.map(schoolData => School.fromJson(schoolData));
        } catch (error) {
            console.error('Error fetching schools:', error);
            throw error;
        }
    }

    static async create(school) {
        try {
            const data = school instanceof School ? school.toJson() : school;
            const response = await ApiClient.post(API_ENDPOINTS.ADMIN.SCHOOLS.CREATE, data);
            if (response.data && response.data.data && response.data.data.school) {
                return School.fromJson(response.data.data.school);
            }
            return response.data;
        } catch (error) {
            console.error('Error creating school:', error);
            throw error;
        }
    }

    static async update(id, school) {
        try {
            const data = school instanceof School ? school.toJson() : school;
            const response = await ApiClient.put(getEndpoint(API_ENDPOINTS.ADMIN.SCHOOLS.UPDATE, id), data);
            if (response.data && response.data.data && response.data.data.school) {
                return School.fromJson(response.data.data.school);
            }
            return response.data;
        } catch (error) {
            console.error('Error updating school:', error);
            throw error;
        }
    }

    static async delete(id) {
        try {
            await ApiClient.delete(getEndpoint(API_ENDPOINTS.ADMIN.SCHOOLS.DELETE, id));
        } catch (error) {
            console.error('Error deleting school:', error);
            throw error;
        }
    }

    static async getStatistics(id) {
        try {
            const response = await ApiClient.get(getEndpoint(API_ENDPOINTS.ADMIN.SCHOOLS.STATISTICS, id));
            return response.data?.data?.statistics || {};
        } catch (error) {
            console.error('Error fetching school statistics:', error);
            throw error;
        }
    }
}


