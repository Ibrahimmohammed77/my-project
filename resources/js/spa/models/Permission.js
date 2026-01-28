export class Permission {
    constructor(data = {}) {
        this.permission_id = data.permission_id || null;
        this.name = data.name || '';
        this.resource_type = data.resource_type || null;
        this.action = data.action || null;
        this.description = data.description || '';
        this.created_at = data.created_at || null;
    }

    static fromJson(json) {
        return new Permission(json);
    }

    toJson() {
        return {
            name: this.name,
            resource_type: this.resource_type,
            action: this.action,
            description: this.description
        };
    }
}
