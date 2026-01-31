export class CardListView {
    constructor() {
        this.tableBody = document.querySelector('#cards-table tbody');
        this.searchField = document.getElementById('search');
        this.statusFilter = document.getElementById('status-filter');
        this.typeFilter = document.getElementById('type-filter');
    }

    bindSearch(handler) {
        if (this.searchField) this.searchField.addEventListener('input', (e) => handler({ search: e.target.value }));
        if (this.statusFilter) this.statusFilter.addEventListener('change', (e) => handler({ status: e.target.value }));
        if (this.typeFilter) this.typeFilter.addEventListener('change', (e) => handler({ type: e.target.value }));
    }

    renderTable(cards) {
        if (!this.tableBody) return;
        this.tableBody.innerHTML = '';
        
        if (cards.length === 0) {
            this.renderEmpty();
            return;
        }

        cards.forEach(card => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50/50 transition-colors border-b border-gray-100';
            const detailUrl = `/school/cards/${card.card_id}`;

            tr.innerHTML = `
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-accent/10 flex items-center justify-center">
                            <i class="fas fa-id-card text-accent"></i>
                        </div>
                        <span class="font-bold text-gray-700"></span>
                    </div>
                </td>
                <td class="px-6 py-4 font-mono text-sm"></td>
                <td class="px-6 py-4">
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-lg"></span>
                </td>
                <td class="px-6 py-4 text-xs text-gray-500">${new Date(card.created_at).toLocaleDateString('ar-EG')}</td>
                <td class="px-6 py-4">
                    <span class="text-xs font-bold ${card.status?.name === 'نشط' ? 'text-green-600' : 'text-gray-400'}">
                        ${card.status?.name || 'غير محدد'}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <a href="${detailUrl}" class="inline-flex items-center gap-2 px-3 py-1.5 bg-accent/5 text-accent hover:bg-accent/10 rounded-lg transition-all text-xs font-bold group">
                        <i class="fas fa-link group-hover:rotate-45 transition-transform"></i>
                        <span>ربط الألبومات (${card.albums_count || card.albums?.length || 0})</span>
                    </a>
                </td>
            `;

            tr.querySelector('td:nth-child(1) span').textContent = card.title || 'بدون عنوان';
            tr.children[1].textContent = card.card_number;
            tr.querySelector('td:nth-child(3) span').textContent = card.type?.name || 'افتراضي';

            this.tableBody.appendChild(tr);
        });
    }

    renderEmpty() {
        this.tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="py-12 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                        <i class="far fa-id-card text-3xl mb-3 text-gray-300"></i>
                        <p>لا توجد كروت مطابقة للبحث</p>
                    </div>
                </td>
            </tr>
        `;
    }
}
