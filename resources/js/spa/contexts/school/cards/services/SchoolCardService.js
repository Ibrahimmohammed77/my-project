import ApiClient from '../../../../core/api/ApiClient.js';

export class SchoolCardService {
    static async getAll(params = {}) {
        return await ApiClient.get('/school/cards', { params });
    }

    static async linkAlbums(cardId, albumIds) {
        return await ApiClient.post(`/school/cards/${cardId}/link-albums`, { album_ids: albumIds });
    }
}

