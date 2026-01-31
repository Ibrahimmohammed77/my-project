export class Card {
    constructor(data = {}) {
        this.id = data.id || data.card_id || null;
        this.card_id = this.id;
        this.card_uuid = data.card_uuid || '';
        this.card_number = data.card_number || '';
        this.card_group_id = data.card_group_id || null;
        this.owner_type = data.owner_type || null;
        this.owner_id = data.owner_id || null;
        this.holder_id = data.holder_id || null;
        this.card_type_id = data.card_type_id || null;
        this.card_status_id = data.card_status_id || null;
        this.activation_date = data.activation_date || null;
        this.expiry_date = data.expiry_date || null;
        this.last_used = data.last_used || null;
        this.notes = data.notes || '';
        
        // Related data
        this.group = data.group || null;
        this.holder = data.holder || null;
        this.type = data.type || null;
        this.status = data.status || null;
        
        this.created_at = data.created_at || null;
        this.updated_at = data.updated_at || null;
    }

    static fromJson(json) {
        return new Card(json);
    }

    toJson() {
        return {
            card_number: this.card_number,
            card_group_id: this.card_group_id,
            card_type_id: this.card_type_id,
            card_status_id: this.card_status_id,
            activation_date: this.activation_date,
            expiry_date: this.expiry_date,
            notes: this.notes
        };
    }
}
