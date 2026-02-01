import ApiClient from '../../../../core/api/ApiClient.js';

export class ProfileService {
    static async update(data) {
        // Using POST with _method: PUT for file upload support in PHP
        return await ApiClient.post('/school/profile', data);
    }
}

