export class Role {
    constructor(data = {}) {
        this.id = data.id || data.role_id || null;
        this.role_id = this.id;
        this.name = data.name || '';
        this.description = data.description || '';
        this.is_system = data.is_system || false;
        this.is_active = data.is_active !== undefined ? data.is_active : true;
        
        // Related data
        this.permissions = data.permissions || [];
        
        this.created_at = data.created_at || null;
        this.updated_at = data.updated_at || null;
    }

    static fromJson(json) {
        return new Role(json);
    }

    toJson() {
        return {
            name: this.name,
            description: this.description,
            is_active: this.is_active
        };
    }

    get permissionCount() {
        return this.permissions?.length || 0;
    }

    get isEditable() {
        return !this.is_system;
    }
}
