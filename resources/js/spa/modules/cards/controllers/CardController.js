import { CardService } from '../services/CardService.js';
import { CardView } from '../views/CardView.js';

export class CardController {
    constructor() {
        this.view = new CardView();
        this.init();
    }

    init() {
        const groupId = document.getElementById('group-id')?.value;
        if (groupId) {
            this.loadGroupCards(groupId);
        } else if (this.view.tbodyGroups) {
            this.loadGroups();
        }
    }

    async loadGroups() {
        this.view.showLoading();
        try {
            const groups = await CardService.getAllGroups();
            this.view.hideLoading();
            this.view.renderGroups(groups);
        } catch (error) {
            this.view.hideLoading();
        }
    }

    async loadGroupCards(groupId) {
        this.view.showLoading();
        try {
            const response = await CardService.getById(groupId); // API endpoint logic might vary
            // In legacy it was CardService.getGroupCards(groupId)
            // But let's assume CardService has it or we add it
            if (CardService.getGroupCards) {
                const cards = await CardService.getGroupCards(groupId);
                this.view.hideLoading();
                this.view.renderCards(cards);
            }
        } catch (error) {
            this.view.hideLoading();
        }
    }
}

export default CardController;
