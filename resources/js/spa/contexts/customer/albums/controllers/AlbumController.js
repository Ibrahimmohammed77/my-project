import CustomerAlbumService from '../services/CustomerAlbumService.js';
import AlbumView from '../views/AlbumView.js';

export default class AlbumController {
    constructor() {
        this.service = new CustomerAlbumService();
        this.view = new AlbumView();
        this.currentPage = 1;
        this.searchTerm = '';
    }

    init() {
        this.loadAlbums();
        this.bindEvents();
        this.bindUploadForm();

        // Register global functions
        window.showCreateModal = () => this.openCreate();
        window.editAlbum = (id) => this.openEdit(id);
        window.deleteAlbum = (id) => this.delete(id);
        window.closeModal = () => this.view.closeModal();
        window.openUploadModal = (id) => this.openUpload(id);
        window.closeUploadModal = () => this.view.closeUploadModal();
    }

    bindEvents() {
        // Search
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.searchTerm = e.target.value;
                this.currentPage = 1;
                this.loadAlbums();
            });
        }

        // Form submission
        const form = document.getElementById('album-form');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.save();
            });
        }
    }

    bindUploadForm() {
        const uploadForm = document.getElementById('upload-form');
        if (uploadForm) {
            uploadForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const albumId = document.getElementById('upload-album-id').value;
                const photosInput = document.getElementById('photos');
                const caption = document.getElementById('upload-caption').value;

                if (!photosInput.files.length) {
                    this.view.showToast('الرجاء اختيار صور للرفع', 'error');
                    return;
                }

                const formData = new FormData();
                Array.from(photosInput.files).forEach(file => {
                    formData.append('photos[]', file);
                });
                if (caption) {
                    formData.append('caption', caption);
                }

                try {
                    await this.service.uploadPhotos(albumId, formData);
                    this.view.showToast('تم رفع الصور بنجاح', 'success');
                    this.view.closeUploadModal();
                    this.loadAlbums();
                } catch (error) {
                    this.view.showToast(error.message || 'حدث خطأ أثناء رفع الصور', 'error');
                }
            });
        }
    }

    async loadAlbums() {
        try {
            const response = await this.service.getAlbums(this.currentPage, this.searchTerm);
            this.view.renderTable(response.data.albums);
            this.view.renderPagination(response.data.pagination, (page) => {
                this.currentPage = page;
                this.loadAlbums();
            });
        } catch (error) {
            this.view.showToast('حدث خطأ أثناء تحميل الألبومات', 'error');
        }
    }

    openCreate() {
        this.view.openModal('إنشاء ألبوم جديد');
        document.getElementById('album-form').reset();
        document.getElementById('album-id').value = '';
    }

    async openEdit(id) {
        try {
            const response = await this.service.getAlbum(id);
            const album = response.data.album;

            this.view.openModal('تعديل الألبوم');
            document.getElementById('album-id').value = album.album_id;
            document.getElementById('name').value = album.name;
            document.getElementById('description').value = album.description || '';
            document.getElementById('is_visible').checked = album.is_visible;
        } catch (error) {
            this.view.showToast('حدث خطأ أثناء تحميل بيانات الألبوم', 'error');
        }
    }

    async save() {
        const albumId = document.getElementById('album-id').value;
        const data = {
            name: document.getElementById('name').value,
            description: document.getElementById('description').value,
            is_visible: document.getElementById('is_visible').checked ? 1 : 0,
        };

        try {
            if (albumId) {
                await this.service.updateAlbum(albumId, data);
                this.view.showToast('تم تحديث الألبوم بنجاح', 'success');
            } else {
                // Get first storage library for customer
                data.storage_library_id = 1; // TODO: Get from user's storage libraries
                await this.service.createAlbum(data);
                this.view.showToast('تم إنشاء الألبوم بنجاح', 'success');
            }

            this.view.closeModal();
            this.loadAlbums();
        } catch (error) {
            this.view.showToast(error.message || 'حدث خطأ أثناء حفظ الألبوم', 'error');
        }
    }

    async delete(id) {
        if (!confirm('هل أنت متأكد من حذف هذا الألبوم؟')) {
            return;
        }

        try {
            await this.service.deleteAlbum(id);
            this.view.showToast('تم حذف الألبوم بنجاح', 'success');
            this.loadAlbums();
        } catch (error) {
            this.view.showToast('حدث خطأ أثناء حذف الألبوم', 'error');
        }
    }

    openUpload(id) {
        this.view.openUploadModal(id);
    }
}
