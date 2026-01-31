import { DOM } from '../../../../core/utils/dom.js';
import { XssProtection } from '../../../../core/security/XssProtection.js';

export class AlbumView {
    constructor() {
        this.tableBody = document.querySelector('#albums-table tbody');
        this.searchField = document.getElementById('search');
        this.modal = document.getElementById('album-modal');
        this.form = document.getElementById('album-form');
        this.modalTitle = document.querySelector('#album-modal h3') || document.getElementById('modal-title'); // Check blade structure
        
        // Buttons
        this.submitBtn = this.form?.querySelector('button[type="submit"]');
        this.originalBtnContent = this.submitBtn?.innerHTML;
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
            
            // Fix checkbox
            data.is_visible = this.form.querySelector('#is_visible').checked ? 1 : 0;
            
            const id = document.getElementById('album-id').value;
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
            this.submitBtn.innerHTML = this.originalBtnContent;
            this.submitBtn.classList.remove('opacity-75');
        }
    }

    openCreateModal() {
        this.form.reset();
        document.getElementById('album-id').value = '';
        if (this.modalTitle) this.modalTitle.textContent = 'ألبوم جديد';
        this.modal.classList.remove('hidden');
        // Ensure first library is selected by default if available
        const select = document.getElementById('storage_library_id');
        if (select && select.options.length > 0) select.selectedIndex = 0;
    }

    openEditModal(album) {
        this.form.reset();
        document.getElementById('album-id').value = album.album_id;
        document.getElementById('name').value = album.name;
        document.getElementById('description').value = album.description || '';
        document.getElementById('is_visible').checked = !!album.is_visible;
        
        // Select Library
        const select = document.getElementById('storage_library_id');
        if (select && album.storage_library_id) {
            select.value = album.storage_library_id;
        }

        if (this.modalTitle) this.modalTitle.textContent = 'تعديل الألبوم';
        this.modal.classList.remove('hidden');
    }

    closeModal() {
        this.modal.classList.add('hidden');
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
            
            // Safe HTML construction
            tr.innerHTML = `
                <td class="px-6 py-4 text-sm font-bold text-gray-800"></td>
                <td class="px-6 py-4 text-sm text-gray-500"></td>
                <td class="px-6 py-4 text-sm text-gray-500">${album.photos_count || 0} صورة</td>
                <td class="px-6 py-4 text-center">
                    <span class="text-[10px] font-bold px-2 py-1 rounded-full ${album.is_visible ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-gray-50 text-gray-600 border border-gray-200'}">
                        ${album.is_visible ? 'عام' : 'مخفي'}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    <div class="flex gap-2 justify-center">
                        <button class="edit-btn w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-accent hover:border-accent transition-all flex items-center justify-center shadow-soft">
                            <i class="fa-solid fa-pen text-xs"></i>
                        </button>
                        <button class="delete-btn w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-red-500 hover:border-red-500 transition-all flex items-center justify-center shadow-soft">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </div>
                </td>
            `;

            // XSS Safe Text Insertion
            tr.children[0].textContent = album.name;
            tr.children[1].textContent = album.description || '-';

            // Event Listeners
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
                    <h4 class="text-gray-800 font-bold">لا توجد ألبومات حالياً</h4>
                    <p class="text-gray-500 text-sm mt-1">ابدأ بإنشاء أول ألبوم لك الآن</p>
                </td>
            </tr>
        `;
    }
}

