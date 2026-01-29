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
        this.password_confirmation = data.password_confirmation || null;
        this.account_type_code = data.account_type_code || null; // Virtual field for validation

        // Dynamic Fields
        this.studio_name = data.studio_name || null;
        this.studio_status_id = data.studio_status_id || null;
        
        this.school_name = data.school_name || null;
        this.school_type_id = data.school_type_id || null;
        this.school_level_id = data.school_level_id || null;
        this.school_status_id = data.school_status_id || null;
        
        this.subscriber_status_id = data.subscriber_status_id || null;
        
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
        
        // Add dynamic fields if present
        if (this.account_type_code) data.account_type_code = this.account_type_code;
        
        if (this.studio_name) data.studio_name = this.studio_name;
        if (this.studio_status_id) data.studio_status_id = this.studio_status_id;
        
        if (this.school_name) data.school_name = this.school_name;
        if (this.school_type_id) data.school_type_id = this.school_type_id;
        if (this.school_level_id) data.school_level_id = this.school_level_id;
        if (this.school_status_id) data.school_status_id = this.school_status_id;
        
        if (this.subscriber_status_id) data.subscriber_status_id = this.subscriber_status_id;
        
        if (this.password) {
            data.password = this.password;
            data.password_confirmation = this.password_confirmation;
        }
        
        return data;
    }
}
