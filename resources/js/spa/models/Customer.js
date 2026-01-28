export class Customer {
    constructor(data = {}) {
        this.customer_id = data.customer_id || null;
        this.first_name = data.first_name || '';
        this.last_name = data.last_name || '';
        this.email = data.email || '';
        this.phone = data.phone || '';
        this.date_of_birth = data.date_of_birth || null;
        this.gender_id = data.gender_id || null;
        this.account_id = data.account_id || null;
        this.settings = data.settings || {};
        
        // Relations
        this.account = data.account || null;
        this.gender = data.gender || null;
    }
    
    get full_name() {
        return `${this.first_name} ${this.last_name}`;
    }

    static fromJson(json) {
        return new Customer(json);
    }

    toJson() {
        return {
            first_name: this.first_name,
            last_name: this.last_name,
            email: this.email,
            phone: this.phone,
            date_of_birth: this.date_of_birth,
            gender_id: this.gender_id,
            account_id: this.account_id,
            settings: this.settings
        };
    }
}
