import { CustomerService } from '../services/CustomerService.js';
import { CustomerView } from '../views/CustomerView.js';

export class CustomerController {
    constructor() {
        this.view = new CustomerView();
        this.customers = [];
        this.init();
    }

    init() {
        this.loadCustomers();
    }

    async loadCustomers() {
        this.view.showLoading();
        try {
            this.customers = await CustomerService.getAll();
            this.view.hideLoading();
            this.view.renderCustomers(this.customers);
        } catch (error) {
            this.view.hideLoading();
        }
    }
}

export default CustomerController;
