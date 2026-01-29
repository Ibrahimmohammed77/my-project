import { StudioAlbumService } from '../services/StudioAlbumService';
import { Toast } from '../components/Toast';

class StudioAlbumsPage {
    constructor() {
        this.albums = [];
        this.tableBody = document.querySelector('#albums-table tbody');
        this.modal = document.getElementById('album-modal');
        this.form = document.getElementById('album-form');
        this.searchField = document.getElementById('search');
        
        this.init();
    }

    async init() {
        this.setupEventListeners();
        await this.loadAlbums();
    }

    setupEventListeners() {
        if (this.form) {
            this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        }
        if (this.searchField) {
            this.searchField.addEventListener('input', (e) => this.handleSearch(e));
        }
    }

    async loadAlbums() {
        try {
            this.renderLoading();
            this.albums = await StudioAlbumService.getAll();
            this.renderAlbums(this.albums);
        } catch (error) {
            Toast.error('خطأ في تحميل الألبومات');
        }
    }

    renderLoading() {
        if (this.tableBody) {
            this.tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="py-10 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <i class="fas fa-spinner fa-spin text-accent text-2xl"></i>
                            <span class="text-sm text-gray-500">جاري تحميل الألبومات...</span>
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

    renderAlbums(albums) {
        if (!this.tableBody) return;
        
        if (albums.length === 0) {
            this.renderEmpty();
            return;
        }

        this.tableBody.innerHTML = albums.map(album => `
            <tr class="hover:bg-gray-50/50 transition-colors group">
                <td class="py-4 px-4 text-sm text-gray-800 font-bold">${album.name}</td>
                <td class="py-4 px-4 text-sm text-gray-500">${album.description || '-'}</td>
                <td class="py-4 px-4 text-sm text-gray-500">${album.photos_count || 0} صورة</td>
                <td class="py-4 px-4">
                    <span class="text-[10px] font-bold px-2 py-1 rounded-full ${album.is_visible ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-gray-50 text-gray-600 border border-gray-200'}">
                        ${album.is_visible ? 'عام' : 'مخفي'}
                    </span>
                </td>
                <td class="py-4 px-4">
                    <div class="flex gap-2 justify-center">
                        <button onclick="editAlbum(${album.album_id})" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-accent hover:border-accent transition-all flex items-center justify-center shadow-soft">
                            <i class="fa-solid fa-pen text-xs"></i>
                        </button>
                        <button onclick="deleteAlbum(${album.album_id})" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-500 hover:text-red-500 hover:border-red-500 transition-all flex items-center justify-center shadow-soft">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    handleSearch(e) {
        const query = e.target.value.toLowerCase();
        const filtered = this.albums.filter(album => 
            album.name.toLowerCase().includes(query) || 
            (album.description && album.description.toLowerCase().includes(query))
        );
        this.renderAlbums(filtered);
    }

    async handleSubmit(e) {
        e.preventDefault();
        const formData = new FormData(this.form);
        const data = Object.fromEntries(formData.entries());
        const id = document.getElementById('album-id').value;

        try {
            if (id) {
                await StudioAlbumService.update(id, data);
                Toast.success('تم تحديث الألبوم بنجاح');
            } else {
                await StudioAlbumService.create(data);
                Toast.success('تم إنشاء الألبوم بنجاح');
            }
            closeModal();
            this.loadAlbums();
        } catch (error) {
            Toast.error(error.response?.data?.message || 'خطأ في حفظ الألبوم');
        }
    }

    async deleteAlbum(id) {
        if (!confirm('هل أنت متأكد من حذف هذا الألبوم؟')) return;
        try {
            await StudioAlbumService.delete(id);
            Toast.success('تم حذف الألبوم بنجاح');
            this.loadAlbums();
        } catch (error) {
            Toast.error('خطأ في حذف الألبوم');
        }
    }

    editAlbum(id) {
        const album = this.albums.find(a => a.album_id == id);
        if (!album) return;

        document.getElementById('album-id').value = album.album_id;
        document.getElementById('name').value = album.name;
        document.getElementById('description').value = album.description || '';
        document.getElementById('is_visible').checked = !!album.is_visible;
        
        showCreateModal('تعديل ألبوم');
    }
}

// Global functions for inline attributes
window.albumPage = new StudioAlbumsPage();
window.editAlbum = (id) => window.albumPage.editAlbum(id);
window.deleteAlbum = (id) => window.albumPage.deleteAlbum(id);

window.showCreateModal = (title = 'ألبوم جديد') => {
    document.getElementById('album-modal').classList.remove('hidden');
    document.getElementById('album-modal-title').innerText = title;
};

window.closeModal = () => {
    document.getElementById('album-modal').classList.add('hidden');
    document.getElementById('album-form').reset();
    document.getElementById('album-id').value = '';
};
