import axios from 'axios';

export class StudioAlbumService {
    static async getAll() {
        try {
            const response = await axios.get('/studio/albums');
            return response.data.data.albums;
        } catch (error) {
            console.error('Error fetching studio albums:', error);
            throw error;
        }
    }

    static async create(data) {
        try {
            const response = await axios.post('/studio/albums', data);
            return response.data;
        } catch (error) {
            console.error('Error creating album:', error);
            throw error;
        }
    }

    static async update(id, data) {
        try {
            const response = await axios.put(`/studio/albums/${id}`, data);
            return response.data;
        } catch (error) {
            console.error('Error updating album:', error);
            throw error;
        }
    }

    static async delete(id) {
        try {
            const response = await axios.delete(`/studio/albums/${id}`);
            return response.data;
        } catch (error) {
            console.error('Error deleting album:', error);
            throw error;
        }
    }
}
