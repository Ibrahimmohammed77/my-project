import ApiClient from '../../../../core/api/ApiClient.js';

export class ProfileService {
    /**
     * Update studio profile
     * @param {Object} data 
     * @returns {Promise}
     */
    static async update(data) {
        // Using POST with _method: PUT for file upload support in PHP
        return await ApiClient.post('/studio/profile', data);
    }
}

