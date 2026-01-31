export class AlbumView {
    constructor() {
        this.tableBody = document.querySelector('#albums-table tbody');
        this.searchField = document.getElementById('search');
        this.modal = document.getElementById('album-modal');
        this.form = document.getElementById('album-form');
        this.modalTitle = document.getElementById('modal-title');
        this.albumIdInput = document.getElementById('album-id');
        this.submitBtn = this.form?.querySelector('button[type="submit"]');
        this.originalContent = this.submitBtn?.innerHTML;
    }

    bindSearch(handler) {
        if (!this.searchField) return;
        this.searchField.addEventListener('input', (e) => handler(e.target.value));
    }

    bindSubmit(handler) {
        if (!this.form) return;
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(this.form);
            const data = Object.fromEntries(formData.entries());
            const id = this.albumIdInput.value;
            // Handle card_ids array if present in form?
            // If uses multi-select, FormData handles it as multiple entries with same key?
            // Object.fromEntries takes LAST value.
            // Need custom handling for array inputs.
            const cardIds = formData.getAll('card_ids[]');
            if (cardIds.length > 0) {
                data.card_ids = cardIds;
            }
            handler(data, id);
        });
    }

    setLoading(loading) {
        if (!this.submitBtn) return;
        if (loading) {
            this.submitBtn.disabled = true;
            this.submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin ms-2"></i> جاري الحفظ...';
            this.submitBtn.classList.add('opacity-75');
        } else {
            this.submitBtn.disabled = false;
            this.submitBtn.innerHTML = this.originalContent;
            this.submitBtn.classList.remove('opacity-75');
        }
    }

    renderTable(albums) {
        if (!this.tableBody) return;
        this.tableBody.innerHTML = '';
        
        if (albums.length === 0) {
            this.renderEmpty();
            return;
        }

        albums.forEach(album => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50/50 transition-colors group border-b border-gray-100';
            
            tr.innerHTML = `
                <td class="py-4 px-4 font-bold text-gray-800"></td>
                <td class="py-4 px-4 text-sm text-gray-600"></td>
                <td class="py-4 px-4 text-sm text-gray-600">${album.photos_count || 0} صورة</td>
                <td class="py-4 px-4 text-[11px] text-gray-500">${new Date(album.created_at).toLocaleDateString('ar-EG')}</td>
                <td class="py-4 px-4">
                    <div class="flex gap-2 justify-center">
                        <button class="edit-btn w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-accent hover:border-accent transition-all flex items-center justify-center shadow-soft">
                            <i class="fa-solid fa-pen text-xs"></i>
                        </button>
                        <button class="delete-btn w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-red-500 hover:border-red-500 transition-all flex items-center justify-center shadow-soft">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </div>
                </td>
            `;

            tr.children[0].textContent = album.name;
            tr.children[1].textContent = album.description || '-';

            tr.querySelector('.edit-btn').addEventListener('click', () => window.albumController.edit(album.album_id));
            tr.querySelector('.delete-btn').addEventListener('click', () => window.albumController.delete(album.album_id));

            this.tableBody.appendChild(tr);
        });
    }

    renderEmpty() {
        this.tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="py-20 text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-gray-300">
                        <i class="fa-solid fa-images text-gray-300 text-3xl"></i>
                    </div>
                    <h4 class="text-gray-800 font-bold">لا توجد ألبومات</h4>
                    <p class="text-gray-500 text-sm mt-1">ابدأ بإنشاء ألبوم جديد</p>
                </td>
            </tr>
        `;
    }

    openCreateModal() {
        this.form.reset();
        this.albumIdInput.value = '';
        if (this.modalTitle) this.modalTitle.textContent = 'ألبوم جديد';
        this.modal.classList.remove('hidden');
    }

    openEditModal(album) {
        this.form.reset();
        this.albumIdInput.value = album.album_id;
        if (this.modalTitle) this.modalTitle.textContent = 'تعديل الألبوم';

        document.getElementById('name').value = album.name;
        document.getElementById('description').value = album.description || '';
        
        // Handle Card Selection if form has card select (multiselect?)
        // Assuming blade renders a multiselect for cards?
        // Or check legacy functionality.
        // Legacy 'school-albums.js' doesn't show card selection logic in detail.
        // Controller 'index' doesn't return cards.
        // Create/Update calls Link Use Case.
        // Maybe Modal expects user to enter card codes?
        // Or select from list?
        // If select from list, I need to fetch cards.
        // For now, support Name/Description.
        // If View has input for cards, it will be included in FormData.
        
        this.modal.classList.remove('hidden');
    }

    closeModal() {
        this.modal.classList.add('hidden');
    }
}
