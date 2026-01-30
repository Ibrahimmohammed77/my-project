export class Customer {
    constructor(data = {}) {
        this.id = data.id || data.customer_id || null;
        this.customer_id = this.id;
        this.user_id = data.user_id || null;
        this.first_name = data.first_name || '';
        this.last_name = data.last_name || '';
        this.date_of_birth = data.date_of_birth || null;
        this.gender_id = data.gender_id || null;
        
        // Related data
        this.user = data.user || null;
        this.gender = data.gender || null;
        
        this.created_at = data.created_at || null;
        this.updated_at = data.updated_at || null;
    }

    static fromJson(json) {
        return new Customer(json);
    }

    toJson() {
        return {
            first_name: this.first_name,
            last_name: this.last_name,
            date_of_birth: this.date_of_birth,
            gender_id: this.gender_id
        };
    }
    
    get fullName() {
        return `${this.first_name} ${this.last_name}`.trim();
    }
}
