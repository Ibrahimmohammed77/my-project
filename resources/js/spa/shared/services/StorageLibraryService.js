import ApiClient from '../../core/api/ApiClient.js';

export class StorageLibraryService {
    /**
     * Get all storage libraries
     */
    static async getAll() {
        const response = await ApiClient.get('/studio/storage/libraries');
        return response.data?.data || [];
    }

    /**
     * Create a new storage library (with automatic hidden album)
     */
    static async create(data) {
        const response = await ApiClient.post('/studio/storage/libraries', data);
        return response.data?.data || null;
    }

    /**
     * Update storage library
     */
    static async update(id, data) {
        const response = await ApiClient.put(`/studio/storage/libraries/${id}`, data);
        return response.data?.data || null;
    }

    /**
     * Delete storage library
     */
    static async delete(id) {
        await ApiClient.delete(`/studio/storage/libraries/${id}`);
    }
}

export default StorageLibraryService;
