export class Studio {
    constructor(data = {}) {
        this.studio_id = data.studio_id || null;
        this.name = data.user?.name || data.name || '';
        this.email = data.user?.email || data.email || null;
        this.phone = data.user?.phone || data.phone || null;
        this.studio_status_id = data.studio_status_id || null;
        this.status = data.user?.status || data.status || null; // Related object
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
            studio_status_id: this.studio_status_id,
            username: this.username || null,
            password: this.password || null
        };
    }
}
