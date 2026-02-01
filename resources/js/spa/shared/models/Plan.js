export class Plan {
    constructor(data = {}) {
        this.plan_id = data.plan_id;
        this.name = data.name;
        this.description = data.description;
        this.storage_limit = data.storage_limit;
        this.price_monthly = data.price_monthly;
        this.price_yearly = data.price_yearly;
        this.max_albums = data.max_albums;
        this.max_cards = data.max_cards;
        this.is_active = data.is_active;
    }

    static fromJson(json) {
        return new Plan(json);
    }

    toJson() {
        return {
            name: this.name,
            description: this.description,
            storage_limit: this.storage_limit,
            price_monthly: this.price_monthly,
            price_yearly: this.price_yearly,
            max_albums: this.max_albums,
            max_cards: this.max_cards,
            is_active: this.is_active
        };
    }

    get storage_limit_gb() {
        return this.storage_limit ? (this.storage_limit / 1024).toFixed(2) : '0';
    }

    get formatted_price_monthly() {
        return typeof this.price_monthly === 'number' ? this.price_monthly.toFixed(2) : this.price_monthly;
    }

    get formatted_price_yearly() {
        return typeof this.price_yearly === 'number' ? this.price_yearly.toFixed(2) : this.price_yearly;
    }
}

