export default class CustomerCardService {
    constructor() {
        this.baseUrl = '/customer/cards';
        this.albumsUrl = '/customer/albums';
    }

    async getCards(page = 1, search = '') {
        const params = new URLSearchParams({ page, search });
        const response = await axios.get(`${this.baseUrl}?${params}`);
        return response.data;
    }

    async getCard(id) {
        const response = await axios.get(`${this.baseUrl}/${id}`);
        return response.data;
    }

    async createCard(data) {
        const response = await axios.post(this.baseUrl, data);
        return response.data;
    }

    async updateCard(id, data) {
        const response = await axios.put(`${this.baseUrl}/${id}`, data);
        return response.data;
    }

    async deleteCard(id) {
        const response = await axios.delete(`${this.baseUrl}/${id}`);
        return response.data;
    }

    async linkAlbums(cardId, albumIds) {
        const response = await axios.post(`${this.baseUrl}/${cardId}/link-albums`, {
            album_ids: albumIds
        });
        return response.data;
    }

    async getAvailableAlbums() {
        const response = await axios.get(this.albumsUrl);
        return response.data;
    }
}
