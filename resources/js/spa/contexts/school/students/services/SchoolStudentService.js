import ApiClient from '../../../../core/api/ApiClient.js';

export class SchoolStudentService {
    static async getAll(params = {}) {
        return await ApiClient.get('/school/students', { params });
    }
}

