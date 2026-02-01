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

    get is_expired() {
        if (!this.expiryDate) return false;
        return new Date(this.expiryDate) < new Date();
    }

    get formatted_card_number() {
        if (!this.number) return '';
        const s = this.number.toString();
        return `${s.slice(0, 3)}-${s.slice(3, 6)}-${s.slice(6, 9)}`;
    }

    get holder_name() {
        return this.holder?.name || '-';
    }

    get owner_name() {
        return this.owner?.name || '-';
    }
}

