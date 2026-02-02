import StudioAlbumService from '../../../../shared/services/StudioAlbumService.js';
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
        this.view.bindSubmit(this.handleSubmit.bind(this));
        this.view.bindSearch(this.handleSearch.bind(this));
        
        // Expose global for view callbacks (temporary pattern until full event delegation)
        window.albumController = this;
        window.showCreateModal = () => this.view.openCreateModal();
        window.closeModal = () => this.view.closeModal();

        await this.loadAlbums();
    }

    async loadAlbums() {
        try {
            this.albums = await StudioAlbumService.getAll();
            // Backend returns { albums: [], libraries: [] } or just array based on implementation. 
            // My backend change: data: { albums: [], libraries: [] } in JSON.
            // But StudioAlbumService.getAll() returns `response.data.data.albums`.
            // Let's verify StudioAlbumService.js implementation.
            // "return response.data.data.albums;" (Step 240)
            // So this.albums will be the array. Correct.
            this.view.renderTable(this.albums);
        } catch (error) {
            Toast.error('خطأ في تحميل الألبومات');
        }
    }

    async handleSubmit(data, id) {
        // Validation
        const errors = {};
        
        if (!InputValidator.validateRequired(data.name)) {
            errors.name = ['اسم الألبوم مطلوب'];
        } else if (data.name.length > 255) {
            errors.name = ['اسم الألبوم يجب أن لا يتجاوز 255 حرفاً'];
        }

        if (!data.storage_library_id) {
            // Check if select exists (might be hidden/empty)
             errors.storage_library_id = ['يرجى اختيار مكتبة التخزين'];
        }

        if (Object.keys(errors).length > 0) {
            import('../../../../utils/toast.js').then(({ showErrors }) => showErrors(errors));
            return;
        }

        try {
            this.view.setLoading(true);
            let response;
            
            if (id) {
                response = await StudioAlbumService.update(id, data);
                Toast.success('تم تحديث الألبوم بنجاح');
            } else {
                response = await StudioAlbumService.create(data);
                Toast.success('تم إنشاء الألبوم بنجاح');
            }

            this.view.closeModal();
            await this.loadAlbums();

        } catch (error) {
             if (error.response?.status === 422) {
                import('../../../../utils/toast.js').then(({ showErrors }) => showErrors(error.response.data.errors));
            } else {
                Toast.error(error.response?.data?.message || 'خطأ في حفظ الألبوم');
            }
        } finally {
            this.view.setLoading(false);
        }
    }

    edit(id) {
        const album = this.albums.find(a => a.album_id == id); // Loose equality for ID string/int
        if (album) {
            this.view.openEditModal(album);
        }
    }

    async delete(id) {
        if (!confirm('هل أنت متأكد من حذف هذا الألبوم؟')) return;
        
        try {
            await StudioAlbumService.delete(id);
            Toast.success('تم حذف الألبوم بنجاح');
            await this.loadAlbums();
        } catch (error) {
            Toast.error('خطأ في حذف الألبوم');
        }
    }

    handleSearch(query) {
        const lower = query.toLowerCase();
        const filtered = this.albums.filter(a => 
            a.name.toLowerCase().includes(lower) || 
            (a.description && a.description.toLowerCase().includes(lower))
        );
        this.view.renderTable(filtered);
    }
}

