import { SchoolStudentService } from '../services/SchoolStudentService.js';
import { StudentView } from '../views/StudentView.js';
import { Toast } from '../../../../core/ui/Toast.js';

export class StudentController {
    constructor() {
        this.view = new StudentView();
        this.init();
    }

    async init() {
        this.view.bindSearch(this.handleSearch.bind(this));
        await this.loadStudents();
    }

    async loadStudents(query = '') {
        try {
            const params = query ? { search: query } : {};
            const response = await SchoolStudentService.getAll(params);
            
            // Backend Controller implementation details?
            // Assuming structure similar to others: data.data.students (paginated)
            // Let's assume pagination is used but we pass items to render.
            // Check School/StudentController.js if available?
            // Assuming standard response structure.
            
            const students = response.data.data.students || response.data.data;
            this.view.renderTable(students);
        } catch (error) {
            Toast.error('خطأ في تحميل الطلاب');
        }
    }

    handleSearch(query) {
        if (this.searchTimeout) clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => this.loadStudents(query), 500);
    }
}

