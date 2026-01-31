import ApiClient from '../core/api/ApiClient.js';
import { Role } from '../models/Role.js';

export class RoleService {
    static async getAll() {
        try {
            const response = await ApiClient.get('/admin/roles');
            return response.data?.data?.roles?.map(roleData => Role.fromJson(roleData)) || [];
        } catch (error) {
            console.error('Error fetching roles:', error);
            throw error;
        }
    }

    static async create(role) {
        try {
            const data = role instanceof Role ? role.toJson() : role;
            const response = await ApiClient.post('/admin/roles', data);
            return response.data;
        } catch (error) {
            console.error('Error creating role:', error);
            throw error;
        }
    }

    static async update(id, role) {
        try {
            const data = role instanceof Role ? role.toJson() : role;
            const response = await ApiClient.put(`/admin/roles/${id}`, data);
            return response.data;
        } catch (error) {
            console.error('Error updating role:', error);
            throw error;
        }
    }

    static async delete(id) {
        try {
            await ApiClient.delete(`/admin/roles/${id}`);
            return true;
        } catch (error) {
            console.error('Error deleting role:', error);
            throw error;
        }
    }
}

export default RoleService;
