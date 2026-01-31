import ApiClient from '../../../../core/api/ApiClient.js';

export class ProfileService {
    /**
     * Update studio profile
     * @param {Object} data 
     * @returns {Promise}
     */
    static async update(data) {
        return await ApiClient.put('/studio/profile', data);
    }
}

