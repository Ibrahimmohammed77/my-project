import ApiClient from '../../../../core/api/ApiClient.js';

export class ProfileService {
    static async update(data) {
        return await ApiClient.put('/school/profile', data);
    }
}

