import axios from 'axios';
import { Permission } from '../models/Permission';

export class PermissionService {
    static async getAll() {
        try {
            const response = await axios.get('/permissions');
            return response.data.data.permissions.map(permData => Permission.fromJson(permData));
        } catch (error) {
            console.error('Error fetching permissions:', error);
            throw error;
        }
    }

    static async create(permission) {
        try {
            const response = await axios.post('/permissions', permission.toJson());
            return response.data;
        } catch (error) {
            console.error('Error creating permission:', error);
            throw error;
        }
    }

    static async update(id, permission) {
        try {
            const response = await axios.put(`/permissions/${id}`, permission.toJson());
            return response.data;
        } catch (error) {
            console.error('Error updating permission:', error);
            throw error;
        }
    }

    static async delete(id) {
        try {
            await axios.delete(`/permissions/${id}`);
        } catch (error) {
            console.error('Error deleting permission:', error);
            throw error;
        }
    }
}
