export class Account {
    constructor(data = {}) {
        this.account_id = data.account_id || null;
        this.username = data.username || '';
        this.full_name = data.full_name || '';
        this.email = data.email || null;
        this.phone = data.phone || '';
        this.profile_image = data.profile_image || null;
        this.account_status_id = data.account_status_id || null;
        this.account_type_id = data.account_type_id || null;
        this.password = data.password || null; // Only for creation/update
        
        // Related objects
        this.status = data.status || null;
        this.type = data.type || null;
        this.roles = data.roles || [];
        
        this.created_at = data.created_at || null;
    }

    static fromJson(json) {
        return new Account(json);
    }

    toJson() {
        const data = {
            username: this.username,
            full_name: this.full_name,
            email: this.email,
            phone: this.phone,
            account_status_id: this.account_status_id,
            account_type_id: this.account_type_id
        };
        
        if (this.password) {
            data.password = this.password;
        }
        
        return data;
    }
}
