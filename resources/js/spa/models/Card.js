export class Card {
    constructor(data = {}) {
        this.id = data.card_id;
        this.uuid = data.card_uuid;
        this.number = data.card_number;
        this.groupId = data.card_group_id;
        this.holder = data.holder || null;
        this.type = data.type || null;
        this.status = data.status || null;
        this.activationDate = data.activation_date;
        this.expiryDate = data.expiry_date;
        this.lastUsed = data.last_used;
        this.notes = data.notes;
    }

    static fromJson(json) {
        return new Card(json);
    }

    get isActive() {
        return this.status?.code === 'ACTIVE';
    }
}
