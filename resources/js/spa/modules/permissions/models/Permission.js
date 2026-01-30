export class Permission {
    constructor(data = {}) {
        this.id = data.id || data.permission_id || null;
        this.permission_id = this.id;
        this.name = data.name || '';
        this.resource_type = data.resource_type || '';
        this.action = data.action || '';
        this.description = data.description || '';
        this.is_active = data.is_active !== undefined ? data.is_active : true;
        
        this.created_at = data.created_at || null;
        this.updated_at = data.updated_at || null;
    }

    static fromJson(json) {
        return new Permission(json);
    }

    toJson() {
        return {
            name: this.name,
            resource_type: this.resource_type,
            action: this.action,
            description: this.description,
            is_active: this.is_active
        };
    }
}
