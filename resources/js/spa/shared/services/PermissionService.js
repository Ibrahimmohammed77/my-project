import ApiClient from '../../core/api/ApiClient.js';
import { Permission } from '../models/Permission.js';

export class PermissionService {
    static async getAll() {
        try {
            const response = await ApiClient.get('/admin/permissions');
            return response.data?.data?.permissions?.map(permData => Permission.fromJson(permData)) || [];
        } catch (error) {
            console.error('Error fetching permissions:', error);
            throw error;
        }
    }

    static async create(permission) {
        try {
            const data = permission instanceof Permission ? permission.toJson() : permission;
            const response = await ApiClient.post('/admin/permissions', data);
            return response.data;
        } catch (error) {
            console.error('Error creating permission:', error);
            throw error;
        }
    }

    static async update(id, permission) {
        try {
            const data = permission instanceof Permission ? permission.toJson() : permission;
            const response = await ApiClient.put(`/admin/permissions/${id}`, data);
            return response.data;
        } catch (error) {
            console.error('Error updating permission:', error);
            throw error;
        }
    }

    static async delete(id) {
        try {
            await ApiClient.delete(`/admin/permissions/${id}`);
            return true;
        } catch (error) {
            console.error('Error deleting permission:', error);
            throw error;
        }
    }
}

export default PermissionService;

