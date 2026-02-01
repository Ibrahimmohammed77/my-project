export default class CustomerAlbumService {
    constructor() {
        this.baseUrl = '/customer/albums';
    }

    async getAlbums(page = 1, search = '') {
        const params = new URLSearchParams({ page, search });
        const response = await axios.get(`${this.baseUrl}?${params}`);
        return response.data;
    }

    async getAlbum(id) {
        const response = await axios.get(`${this.baseUrl}/${id}`);
        return response.data;
    }

    async createAlbum(data) {
        const response = await axios.post(this.baseUrl, data);
        return response.data;
    }

    async updateAlbum(id, data) {
        const response = await axios.put(`${this.baseUrl}/${id}`, data);
        return response.data;
    }

    async deleteAlbum(id) {
        const response = await axios.delete(`${this.baseUrl}/${id}`);
        return response.data;
    }

    async uploadPhotos(albumId, formData) {
        const response = await axios.post(`${this.baseUrl}/${albumId}/photos`, formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        return response.data;
    }
}
