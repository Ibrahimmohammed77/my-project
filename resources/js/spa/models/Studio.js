export class Studio {
    constructor(data = {}) {
        this.studio_id = data.studio_id || null;
        this.name = data.name || '';
        this.email = data.email || null;
        this.phone = data.phone || null;
        this.website = data.website || null;
        this.studio_status_id = data.studio_status_id || null;
        this.status = data.status || null; // Related object
        this.created_at = data.created_at || null;
    }

    static fromJson(json) {
        return new Studio(json);
    }

    toJson() {
        return {
            name: this.name,
            email: this.email,
            phone: this.phone,
            website: this.website,
            studio_status_id: this.studio_status_id
        };
    }
}
