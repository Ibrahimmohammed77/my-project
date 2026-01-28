import axios from 'axios';

export class AuthService {
    static async register(data) {
        try {
            const response = await axios.post('/register', data);
            return response.data;
        } catch (error) {
            throw error;
        }
    }

    static async login(credentials) {
        try {
            const response = await axios.post('/login', credentials);
            return response.data;
        } catch (error) {
            throw error;
        }
    }

    static async logout() {
        try {
            await axios.post('/logout');
            window.location.href = '/login';
        } catch (error) {
            console.error('Logout failed:', error);
        }
    }
}
