import ApiClient from '../../../core/api/ApiClient.js';
import { API_ENDPOINTS, getEndpoint } from '../../../core/api/endpoints.js';
import { Role } from '../models/Role.js';

export class RoleService {
    /**
     * Get all roles
     * @returns {Promise<Array<Role>>}
     */
    static async getAll() {
        const response = await ApiClient.get(API_ENDPOINTS.ADMIN.ROLES.LIST);
        
        if (response.data && response.data.data && response.data.data.roles) {
            return response.data.data.roles.map(roleData => Role.fromJson(roleData));
        }
        
        return [];
    }

    /**
     * Create new role
     * @param {Object} roleData
     * @returns {Promise<Role>}
     */
    static async create(roleData) {
        const data = roleData instanceof Role ? roleData.toJson() : roleData;
        
        const response = await ApiClient.post(API_ENDPOINTS.ADMIN.ROLES.CREATE, data);
        
        if (response.data && response.data.data && response.data.data.role) {
            return Role.fromJson(response.data.data.role);
        }
        
        return response.data;
    }

    /**
     * Update existing role
     * @param {number} id
     * @param {Object} roleData
     * @returns {Promise<Role>}
     */
    static async update(id, roleData) {
        const data = roleData instanceof Role ? roleData.toJson() : roleData;
        
        const response = await ApiClient.put(getEndpoint(API_ENDPOINTS.ADMIN.ROLES.UPDATE, id), data);
        
        if (response.data && response.data.data && response.data.data.role) {
            return Role.fromJson(response.data.data.role);
        }
        
        return response.data;
    }

    /**
     * Delete role
     * @param {number} id
     * @returns {Promise<void>}
     */
    static async delete(id) {
        await ApiClient.delete(getEndpoint(API_ENDPOINTS.ADMIN.ROLES.DELETE, id));
    }

    /**
     * Get single role
     * @param {number} id
     * @returns {Promise<Role>}
     */
    static async getById(id) {
        const response = await ApiClient.get(getEndpoint(API_ENDPOINTS.ADMIN.ROLES.SHOW, id));
        
        if (response.data && response.data.data && response.data.data.role) {
            return Role.fromJson(response.data.data.role);
        }
        
        return null;
    }
}

export default RoleService;
