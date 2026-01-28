import axios from 'axios';
import { Role } from '../models/Role';

export class RoleService {
    static async getAll() {
        try {
            const response = await axios.get('/roles');
            return response.data.data.roles.map(roleData => Role.fromJson(roleData));
        } catch (error) {
            console.error('Error fetching roles:', error);
            throw error;
        }
    }

    static async create(role) {
        try {
            const response = await axios.post('/roles', role.toJson());
            return response.data;
        } catch (error) {
            console.error('Error creating role:', error);
            throw error;
        }
    }

    static async update(id, role) {
        try {
            const response = await axios.put(`/roles/${id}`, role.toJson());
            return response.data;
        } catch (error) {
            console.error('Error updating role:', error);
            throw error;
        }
    }

    static async delete(id) {
        try {
            await axios.delete(`/roles/${id}`);
        } catch (error) {
            console.error('Error deleting role:', error);
            throw error;
        }
    }
}
