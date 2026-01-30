export class Plan {
    constructor(data = {}) {
        this.id = data.id || data.plan_id || null;
        this.plan_id = this.id;
        this.name = data.name || '';
        this.description = data.description || '';
        this.price_monthly = data.price_monthly || 0;
        this.price_yearly = data.price_yearly || 0;
        this.storage_limit = data.storage_limit || 0;
        this.features = data.features || [];
        this.is_active = data.is_active !== undefined ? data.is_active : true;
        
        this.created_at = data.created_at || null;
        this.updated_at = data.updated_at || null;
    }

    static fromJson(json) {
        return new Plan(json);
    }

    toJson() {
        return {
            name: this.name,
            description: this.description,
            price_monthly: this.price_monthly,
            price_yearly: this.price_yearly,
            storage_limit: this.storage_limit,
            features: this.features,
            is_active: this.is_active
        };
    }
}
