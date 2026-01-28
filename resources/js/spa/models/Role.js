export class Role {
    constructor(data = {}) {
        this.role_id = data.role_id || null;
        this.name = data.name || '';
        this.description = data.description || '';
        this.is_system = data.is_system || false;
        
        // Related objects
        this.permissions = data.permissions || [];
        
        this.created_at = data.created_at || null;
    }

    static fromJson(json) {
        return new Role(json);
    }

    toJson() {
        return {
            name: this.name,
            description: this.description
        };
    }
}
