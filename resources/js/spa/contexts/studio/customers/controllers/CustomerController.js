import StudioCustomerService from '../../../../shared/services/StudioCustomerService.js';
import { CustomerView } from '../views/CustomerView.js';
import { Toast } from '../../../../core/ui/Toast.js';

export class CustomerController {
    constructor() {
        this.view = new CustomerView();
        this.customers = [];
        this.init();
    }

    async init() {
        this.view.bindSearch(this.handleSearch.bind(this));
        await this.loadCustomers();
    }

    async loadCustomers() {
        try {
            this.customers = await StudioCustomerService.getAll();
            this.view.renderTable(this.customers);
        } catch (error) {
            Toast.error('خطأ في تحميل قائمة العملاء');
        }
    }

    handleSearch(query) {
        const lower = query.toLowerCase();
        const filtered = this.customers.filter(c => 
            (c.name && c.name.toLowerCase().includes(lower)) || 
            (c.email && c.email.toLowerCase().includes(lower)) ||
            (c.username && c.username.toLowerCase().includes(lower))
        );
        this.view.renderTable(filtered);
    }
}

