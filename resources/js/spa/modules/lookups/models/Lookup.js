export class Lookup {
    constructor(data = {}) {
        this.id = data.id || data.lookup_value_id || null;
        this.lookup_value_id = this.id;
        this.master_lookup_id = data.master_lookup_id || null;
        this.code = data.code || '';
        this.name = data.name || '';
        this.description = data.description || '';
        this.is_active = data.is_active !== undefined ? data.is_active : true;
        this.is_system = data.is_system || false;
        
        // Related data
        this.master = data.master || null;
        
        this.created_at = data.created_at || null;
        this.updated_at = data.updated_at || null;
    }

    static fromJson(json) {
        return new Lookup(json);
    }

    toJson() {
        return {
            master_lookup_id: this.master_lookup_id,
            code: this.code,
            name: this.name,
            description: this.description,
            is_active: this.is_active
        };
    }
    
    get isEditable() {
        return !this.is_system;
    }
}
