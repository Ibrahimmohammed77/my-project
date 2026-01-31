import ApiClient from '../../../../core/api/ApiClient.js';

export class SchoolAlbumService {
    static async getAll(params = {}) {
        return await ApiClient.get('/school/albums', { params });
    }

    static async create(data) {
        return await ApiClient.post('/school/albums', data);
    }

    static async update(id, data) {
        return await ApiClient.put(`/school/albums/${id}`, data);
    }

    static async delete(id) {
        return await ApiClient.delete(`/school/albums/${id}`);
    }
}

