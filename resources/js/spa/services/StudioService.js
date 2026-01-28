import axios from 'axios';
import { Studio } from '../models/Studio';

export class StudioService {
    static async getAll() {
        try {
            const response = await axios.get('/studios');
            return response.data.data.studios.map(studioData => Studio.fromJson(studioData));
        } catch (error) {
            console.error('Error fetching studios:', error);
            throw error;
        }
    }

    static async create(studio) {
        try {
            const response = await axios.post('/studios', studio.toJson());
            return response.data;
        } catch (error) {
            console.error('Error creating studio:', error);
            throw error;
        }
    }

    static async update(id, studio) {
        try {
            const response = await axios.put(`/studios/${id}`, studio.toJson());
            return response.data;
        } catch (error) {
            console.error('Error updating studio:', error);
            throw error;
        }
    }

    static async delete(id) {
        try {
            await axios.delete(`/studios/${id}`);
        } catch (error) {
            console.error('Error deleting studio:', error);
            throw error;
        }
    }
}
