export class Subscription {
    constructor(data = {}) {
        this.subscription_id = data.subscription_id || null;
        this.user_id = data.user_id || null;
        this.plan_id = data.plan_id || null;
        this.start_date = data.start_date || null;
        this.end_date = data.end_date || null;
        this.renewal_date = data.renewal_date || null;
        this.auto_renew = data.auto_renew === undefined ? true : !!data.auto_renew;
        this.subscription_status_id = data.subscription_status_id || null;
        
        // Relations
        this.user = data.user || null;
        this.plan = data.plan || null;
        this.status = data.status || null;
    }

    toJson() {
        return {
            user_id: this.user_id,
            plan_id: this.plan_id,
            start_date: this.start_date,
            end_date: this.end_date,
            auto_renew: this.auto_renew,
            subscription_status_id: this.subscription_status_id
        };
    }
}
