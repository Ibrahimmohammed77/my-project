import StudioStorageService from '../../../../shared/services/StudioStorageService.js';
import { StorageView } from '../views/StorageView.js';
import { Toast } from '../../../../core/ui/Toast.js';
import { InputValidator } from '../../../../core/security/InputValidator.js';

export class StorageController {
    constructor() {
        this.view = new StorageView();
        this.libraries = [];
        this.customers = [];
        this.init();
    }

    async init() {
        this.view.bindSearch(this.handleSearch.bind(this));
        this.view.bindSubmit(this.handleSubmit.bind(this));

        // Global functions for view events if needed (or cleaner event delegation)
        window.storageController = this;
        window.showCreateModal = () => this.view.openCreateModal();
        window.closeModal = () => this.view.closeModal();

        await this.loadData();
    }

    async loadData() {
        try {
            [this.libraries, this.customers] = await Promise.all([
                StudioStorageService.getAll(),
                StudioStorageService.getCustomers()
            ]);
            
            this.view.renderTable(this.libraries);
            this.view.populateSubscribers(this.customers);
        } catch (error) {
            Toast.error('خطأ في تحميل البيانات');
        }
    }

    async handleSubmit(data, id) {
        // Validation
        const errors = {};
        if (!InputValidator.validate(data.name, 'required')) {
            errors.name = ['اسم المكتبة مطلوب'];
        }
        if (!data.storage_limit || Number(data.storage_limit) <= 0) {
            errors.storage_limit = ['المساحة يجب أن تكون أكبر من 0'];
        }
        if (!id && !data.subscriber_id) {
            errors.subscriber_id = ['يجب اختيار مشترك'];
        }

        if (Object.keys(errors).length > 0) {
            import('../../../../utils/toast.js').then(({ showErrors }) => showErrors(errors));
            return;
        }

        try {
            this.view.setLoading(true);
            let response;
            if (id) {
                response = await StudioStorageService.update(id, data);
            } else {
                response = await StudioStorageService.create(data);
            }

            if (response.success) {
                Toast.success(response.message);
                this.view.closeModal();
                await this.loadData(); // Reload to refresh data
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
        const lib = this.libraries.find(l => l.id == id);
        if (lib) this.view.openEditModal(lib);
    }

    async delete(id) {
        if (!confirm('هل أنت متأكد من حذف هذه المكتبة؟')) return;

        try {
            const response = await StudioStorageService.delete(id);
            if (response.success) {
                Toast.success(response.message);
                await this.loadData();
            }
        } catch (error) {
            Toast.error(error.response?.data?.message || 'لا يمكن حذف المكتبة');
        }
    }

    handleSearch(query) {
        const lower = query.toLowerCase();
        const filtered = this.libraries.filter(lib => 
            lib.name.toLowerCase().includes(lower) || 
            (lib.user && (lib.user.full_name || lib.user.username).toLowerCase().includes(lower))
        );
        this.view.renderTable(filtered);
    }
}

