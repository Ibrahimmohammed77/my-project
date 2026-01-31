export class Account {
    constructor(data = {}) {
        this.id = data.id || data.account_id || data.user_id || null;
        this.account_id = this.id;
        this.username = data.username || '';
        this.full_name = data.full_name || data.name || '';
        this.email = data.email || null;
        this.phone = data.phone || '';
        this.profile_image = data.profile_image || null;
        this.account_status_id = data.user_status_id || data.account_status_id || data.status_id || null;
        this.account_type_id = data.user_type_id || data.account_type_id || data.type_id || null;
        
        // Handle role_id from first role if available, or direct property
        this.role_id = data.role_id || null;
        if (!this.role_id && data.roles && data.roles.length > 0) {
            this.role_id = data.roles[0].role_id || data.roles[0].id;
        }

        // Dynamic Fields
        this.studio_name = data.studio_name || null;
        this.studio_status_id = data.studio_status_id || null;
        
        this.school_name = data.school_name || null;
        this.school_type_id = data.school_type_id || null;
        this.school_level_id = data.school_level_id || null;
        this.school_status_id = data.school_status_id || null;
        
        this.subscriber_status_id = data.subscriber_status_id || null;
        
        // Profile fields flattened
        this.city = data.city || data.studio?.city || data.school?.city || null;
        this.address = data.address || data.studio?.address || data.school?.address || null;
        
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
        // Map frontend model to backend request fields
        const data = {
            username: this.username,
            full_name: this.full_name,
            email: this.email,
            phone: this.phone,
            user_status_id: this.account_status_id, // Map to correct backend field
            user_type_id: this.account_type_id,     // Map to correct backend field
            role_id: this.role_id,
            is_active: true // Force active status on creation/update
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
            data.password_confirmation = this.password_confirmation || this.password;
        }
        
        return data;
    }
}

