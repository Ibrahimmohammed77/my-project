import ApiClient from '../../../core/api/ApiClient.js';
import { API_ENDPOINTS, getEndpoint } from '../../../core/api/endpoints.js';
import { Permission } from '../models/Permission.js';

export class PermissionService {
    static async getAll() {
        const response = await ApiClient.get(API_ENDPOINTS.ADMIN.PERMISSIONS.LIST);
        if (response.data && response.data.data && response.data.data.permissions) {
            return response.data.data.permissions.map(p => Permission.fromJson(p));
        }
        return [];
    }

    static async create(permissionData) {
        const data = permissionData instanceof Permission ? permissionData.toJson() : permissionData;
        const response = await ApiClient.post(API_ENDPOINTS.ADMIN.PERMISSIONS.CREATE, data);
        if (response.data && response.data.data && response.data.data.permission) {
            return Permission.fromJson(response.data.data.permission);
        }
        return response.data;
    }

    static async update(id, permissionData) {
        const data = permissionData instanceof Permission ? permissionData.toJson() : permissionData;
        const response = await ApiClient.put(getEndpoint(API_ENDPOINTS.ADMIN.PERMISSIONS.UPDATE, id), data);
        if (response.data && response.data.data && response.data.data.permission) {
            return Permission.fromJson(response.data.data.permission);
        }
        return response.data;
    }

    static async delete(id) {
        await ApiClient.delete(getEndpoint(API_ENDPOINTS.ADMIN.PERMISSIONS.DELETE, id));
    }
}

export default PermissionService;
