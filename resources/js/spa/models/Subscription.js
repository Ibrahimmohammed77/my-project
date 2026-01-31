// resources/js/spa/models/Subscription.js
export class Subscription {
    constructor(data = {}) {
        this.subscription_id = data.subscription_id || null;
        this.user_id = data.user_id || null;
        this.plan_id = data.plan_id || null;
        this.user = data.user || null;
        this.plan = data.plan || null;
        this.status = data.status || null;
        this.start_date = data.start_date || null;
        this.end_date = data.end_date || null;
        this.renewal_date = data.renewal_date || null;
        this.auto_renew = data.auto_renew || false;
        this.billing_cycle = data.billing_cycle || 'monthly';
        this.created_at = data.created_at || null;
    }

    static fromJson(json) {
        return new Subscription(json);
    }

    toJson() {
        return {
            user_id: this.user_id,
            plan_id: this.plan_id,
            billing_cycle: this.billing_cycle,
            auto_renew: this.auto_renew,
            start_date: this.start_date,
            end_date: this.end_date
        };
    }

    isActive() {
        if (!this.end_date) return false;
        const endDate = new Date(this.end_date);
        return endDate >= new Date() && this.status?.code === 'ACTIVE';
    }

    getDaysRemaining() {
        if (!this.isActive()) return 0;
        const endDate = new Date(this.end_date);
        const today = new Date();
        const diff = Math.ceil((endDate - today) / (1000 * 60 * 60 * 24));
        return diff > 0 ? diff : 0;
    }

    getFormattedEndDate() {
        if (!this.end_date) return '-';
        const date = new Date(this.end_date);
        return date.toLocaleDateString('ar-EG', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }
}
