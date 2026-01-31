export class School {
    constructor(data = {}) {
        this.school_id = data.school_id || null;
        this.name = data.user?.name || data.name || '';
        this.email = data.user?.email || data.email || null;
        this.phone = data.user?.phone || data.phone || null;
        this.city = data.city || null;
        this.school_type_id = data.school_type_id || null;
        this.school_level_id = data.school_level_id || null;
        this.school_status_id = data.school_status_id || null;
        
        // Related objects
        this.type = data.type || null;
        this.level = data.level || null;
        this.status = data.user?.status || data.status || null;
        
        this.created_at = data.created_at || null;
    }

    static fromJson(json) {
        return new School(json);
    }

    toJson() {
        return {
            name: this.name,
            email: this.email,
            phone: this.phone,
            city: this.city,
            school_type_id: this.school_type_id,
            school_level_id: this.school_level_id,
            school_status_id: this.school_status_id,
            username: this.username || null,
            password: this.password || null
        };
    }
}

