import ApiClient from '../../spa/core/api/ApiClient.js';

export class AuthService {
    static async register(data) {
        const response = await ApiClient.post('/register', data);
        return response.data;
    }

    static async login(credentials) {
        const response = await ApiClient.post('/login', credentials);
        return response.data;
    }

    static async logout() {
        try {
            await ApiClient.post('/logout');
            window.location.href = '/login';
        } catch (error) {
            console.error('Logout failed:', error);
            // Fallback for logout if API fails
            window.location.href = '/login';
        }
    }
}
