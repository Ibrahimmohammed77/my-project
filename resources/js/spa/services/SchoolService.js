import axios from 'axios';
import { School } from '../models/School';

export class SchoolService {
    static async getAll() {
        try {
            const response = await axios.get('/schools');
            return response.data.data.schools.map(schoolData => School.fromJson(schoolData));
        } catch (error) {
            console.error('Error fetching schools:', error);
            throw error;
        }
    }

    static async create(school) {
        try {
            const response = await axios.post('/schools', school.toJson());
            return response.data;
        } catch (error) {
            console.error('Error creating school:', error);
            throw error;
        }
    }

    static async update(id, school) {
        try {
            const response = await axios.put(`/schools/${id}`, school.toJson());
            return response.data;
        } catch (error) {
            console.error('Error updating school:', error);
            throw error;
        }
    }

    static async delete(id) {
        try {
            await axios.delete(`/schools/${id}`);
        } catch (error) {
            console.error('Error deleting school:', error);
            throw error;
        }
    }
}
