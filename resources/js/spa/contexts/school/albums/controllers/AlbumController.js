import { SchoolAlbumService } from '../services/SchoolAlbumService.js';
import { AlbumView } from '../views/AlbumView.js';
import { Toast } from '../../../../core/ui/Toast.js';
import { InputValidator } from '../../../../core/security/InputValidator.js';

export class AlbumController {
    constructor() {
        this.view = new AlbumView();
        this.albums = [];
        this.init();
    }

    async init() {
        this.view.bindSearch(this.handleSearch.bind(this));
        this.view.bindSubmit(this.handleSubmit.bind(this));
        this.bindUploadForm();

        window.albumController = this;
        window.showCreateModal = () => this.view.openCreateModal();
        window.closeModal = () => this.view.closeModal();
        window.closeUploadModal = () => this.view.closeUploadModal();

        await this.loadAlbums();
    }

    async loadAlbums(query = '') {
        try {
            const params = query ? { search: query } : {};
            const response = await SchoolAlbumService.getAll(params);
            this.albums = response.data.data.albums; // Pagination handled by backend structure
            this.view.renderTable(this.albums);
        } catch (error) {
            Toast.error('خطأ في تحميل الألبومات');
        }
    }

    async handleSubmit(data, id) {
        if (!InputValidator.validate(data.name, 'required')) {
            import('../../../../utils/toast.js').then(({ showErrors }) => showErrors({ name: ['اسم الألبوم مطلوب'] }));
            return;
        }

        try {
            this.view.setLoading(true);
            let response;
            if (id) {
                response = await SchoolAlbumService.update(id, data);
            } else {
                response = await SchoolAlbumService.create(data);
            }

            if (response.data.success) {
                Toast.success(response.data.message);
                this.view.closeModal();
                await this.loadAlbums();
            }
        } catch (error) {
            if (error.response?.status === 422) {
                import('../../../../utils/toast.js').then(({ showErrors }) => showErrors(error.response.data.errors));
            } else {
                Toast.error(error.response?.data?.message || 'خطأ في حفظ البيانات');
            }
        } finally {
            this.view.setLoading(false);
        }
    }

    edit(id) {
        const album = this.albums.find(a => a.album_id == id);
        if (album) this.view.openEditModal(album);
    }

    async delete(id) {
        if (!confirm('هل أنت متأكد من حذف هذا الألبوم؟')) return;

        try {
            const response = await SchoolAlbumService.delete(id);
            if (response.data.success) {
                Toast.success(response.data.message);
                await this.loadAlbums();
            }
        } catch (error) {
            Toast.error(error.response?.data?.message || 'خطأ في الحذف');
        }
    }

    handleSearch(query) {
        // Debounce?
        if (this.searchTimeout) clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.loadAlbums(query);
        }, 500);
    }

    openUpload(albumId) {
        this.view.openUploadModal(albumId);
    }

    bindUploadForm() {
        const uploadForm = document.getElementById('upload-form');
        if (!uploadForm) return;

        uploadForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(uploadForm);
            const albumId = document.getElementById('upload-album-id').value;

            if (!formData.get('photos[]') || formData.getAll('photos[]').length === 0) {
                Toast.error('الرجاء اختيار صور للرفع');
                return;
            }

            try {
                const response = await SchoolAlbumService.uploadPhotos(albumId, formData);
                if (response.data.success) {
                    Toast.success(response.data.message);
                    this.view.closeUploadModal();
                    await this.loadAlbums();
                }
            } catch (error) {
                Toast.error(error.response?.data?.message || 'خطأ في رفع الصور');
            }
        });
    }
}

