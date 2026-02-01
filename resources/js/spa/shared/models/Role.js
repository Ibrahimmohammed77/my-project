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

    /**
     * Get the number of permissions assigned to this role
     * @returns {number}
     */
    get permissionCount() {
        return Array.isArray(this.permissions) ? this.permissions.length : 0;
    }

    /**
     * Check if this role can be edited (non-system roles only)
     * @returns {boolean}
     */
    get isEditable() {
        return !this.is_system;
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

