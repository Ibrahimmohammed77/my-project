import { StorageLibraryService } from '../../../../shared/services/StorageLibraryService.js';
import { StorageView } from '../views/StorageView.js';
import { Toast } from '../../../../core/ui/Toast.js';

export class StorageController {
    constructor() {
        this.view = new StorageView();
        this.libraries = [];
        this.init();
    }

    async init() {
        this.view.bindSearch(this.handleSearch.bind(this));
        this.view.bindSubmit(this.handleSubmit.bind(this));

        // Global functions for view events
        window.storageController = this;
        window.showCreateModal = () => this.view.openCreateModal();
        window.closeModal = () => this.view.closeModal();

        await this.loadData();
    }

    async loadData() {
        try {
            this.libraries = await StorageLibraryService.getAll();
            this.view.renderTable(this.libraries);
        } catch (error) {
            console.error('Error loading libraries:', error);
            Toast.error('خطأ في تحميل مكتبات التخزين');
        }
    }

    async handleSubmit(data, id) {
        // Simple validation
        const errors = {};
        
        if (!data.name || data.name.trim() === '') {
            errors.name = ['اسم المكتبة مطلوب'];
        }

        if (Object.keys(errors).length > 0) {
            import('../../../../utils/toast.js').then(({ showErrors }) => showErrors(errors));
            return;
        }

        try {
            this.view.setLoading(true);
            let response;
            
            if (id) {
                response = await StorageLibraryService.update(id, data);
            } else {
                response = await StorageLibraryService.create(data);
            }

            Toast.success(id ? 'تم تحديث المكتبة بنجاح' : 'تم إنشاء المكتبة والألبوم المخفي بنجاح');
            this.view.closeModal();
            await this.loadData();
        } catch (error) {
            console.error('Error saving library:', error);
            if (error.response?.status === 422) {
                import('../../../../utils/toast.js').then(({ showErrors }) => showErrors(error.response.data.errors));
            } else {
                Toast.error(error.response?.data?.message || 'خطأ في حفظ المكتبة');
            }
        } finally {
            this.view.setLoading(false);
        }
    }

    edit(id) {
        const lib = this.libraries.find(l => l.storage_library_id == id);
        if (lib) this.view.openEditModal(lib);
    }

    async delete(id) {
        if (!confirm('هل أنت متأكد من حذف هذه المكتبة؟\nملاحظة: سيتم حذف الألبوم المخفي المرتبط بها أيضاً.')) return;

        try {
            await StorageLibraryService.delete(id);
            Toast.success('تم حذف المكتبة بنجاح');
            await this.loadData();
        } catch (error) {
            console.error('Error deleting library:', error);
            Toast.error(error.response?.data?.message || 'لا يمكن حذف المكتبة');
        }
    }

    handleSearch(query) {
        const lower = query.toLowerCase();
        const filtered = this.libraries.filter(lib => 
            lib.name.toLowerCase().includes(lower) || 
            (lib.description && lib.description.toLowerCase().includes(lower)) ||
            (lib.hidden_album && lib.hidden_album.name.toLowerCase().includes(lower))
        );
        this.view.renderTable(filtered);
    }
}

