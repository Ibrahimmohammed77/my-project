export class Subscriber {
    constructor(data = {}) {
        this.subscriber_id = data.subscriber_id || null;
        this.account_id = data.account_id || null;
        this.subscriber_status_id = data.subscriber_status_id || null;
        this.settings = data.settings || {};
        
        // Relations
        this.account = data.account || null;
        this.status = data.status || null;
    }

    static fromJson(json) {
        return new Subscriber(json);
    }

    toJson() {
        return {
            account_id: this.account_id,
            subscriber_status_id: this.subscriber_status_id,
            settings: this.settings
        };
    }
}

