import { StudioCardService } from '../services/StudioCardService';
import { Toast } from '../components/Toast';

class StudioCardsPage {
    constructor() {
        this.cards = [];
        this.tableBody = document.querySelector('#cards-table tbody');
        this.searchField = document.getElementById('search');
        
        this.init();
    }

    async init() {
        if (this.searchField) {
            this.searchField.addEventListener('input', (e) => this.handleSearch(e));
        }
        await this.loadCards();
    }

    async loadCards() {
        try {
            this.renderLoading();
            this.cards = await StudioCardService.getAll();
            this.renderCards(this.cards);
        } catch (error) {
            Toast.error('خطأ في تحميل الكروت');
        }
    }

    renderLoading() {
        if (this.tableBody) {
            this.tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="py-10 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <i class="fas fa-spinner fa-spin text-accent text-2xl"></i>
                            <span class="text-sm text-gray-500">جاري تحميل الكروت...</span>
                        </div>
                    </td>
                </tr>
            `;
        }
    }

    renderEmpty() {
        if (this.tableBody) {
            this.tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="py-20 text-center">
                        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-gray-300">
                            <i class="fa-solid fa-id-card text-gray-300 text-3xl"></i>
                        </div>
                        <h4 class="text-gray-800 font-bold">لا يوجد كروت حالياً</h4>
                        <p class="text-gray-500 text-sm mt-1">لم يتم إصدار أي كروت بعد</p>
                    </td>
                </tr>
            `;
        }
    }

    renderCards(cards) {
        if (!this.tableBody) return;
        
        if (cards.length === 0) {
            this.renderEmpty();
            return;
        }

        this.tableBody.innerHTML = cards.map(card => `
            <tr class="hover:bg-gray-50/50 transition-colors group">
                <td class="py-4 px-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-accent/10 flex items-center justify-center text-accent font-bold">
                            <i class="fa-solid fa-id-card"></i>
                        </div>
                        <span class="font-bold text-gray-800">${card.title || 'بدون عنوان'}</span>
                    </div>
                </td>
                <td class="py-4 px-4 text-sm text-gray-600">${card.code}</td>
                <td class="py-4 px-4 text-sm text-gray-600">${card.type?.name || 'افتراضي'}</td>
                <td class="py-4 px-4 text-sm text-gray-500">${new Date(card.created_at).toLocaleDateString('ar-EG')}</td>
                <td class="py-4 px-4">
                    <span class="text-[10px] font-bold px-2 py-1 rounded-full ${card.status?.name === 'نشط' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-gray-50 text-gray-600 border border-gray-100'}">${card.status?.name || 'غير معروف'}</span>
                </td>
                <td class="py-4 px-4 text-center">
                    <div class="flex gap-2 justify-center">
                        <a href="/studio/cards/${card.card_id}" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-accent hover:border-accent transition-all flex items-center justify-center shadow-soft">
                            <i class="fa-solid fa-link text-xs"></i>
                        </a>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    handleSearch(e) {
        const query = e.target.value.toLowerCase();
        const filtered = this.cards.filter(c => 
            (c.title && c.title.toLowerCase().includes(query)) || 
            c.code.toLowerCase().includes(query)
        );
        this.renderCards(filtered);
    }
}

new StudioCardsPage();
