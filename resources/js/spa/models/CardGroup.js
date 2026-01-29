export class CardGroup {
    constructor(data = {}) {
        this.id = data.group_id;
        this.name = data.name;
        this.description = data.description;
        this.sub_card_available = data.sub_card_available;
        this.sub_card_used = data.sub_card_used;
        this.available_cards = data.available_cards; // accessor from backend
    }

    static fromJson(json) {
        return new CardGroup(json);
    }

    toJson() {
        return {
            name: this.name,
            description: this.description,
            sub_card_available: this.sub_card_available,
            sub_card_used: this.sub_card_used
        };
    }
}
