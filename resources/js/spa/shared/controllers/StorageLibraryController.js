import { StorageLibraryService } from '../../../shared/services/StorageLibraryService.js';
import { StorageLibraryView } from '../../../shared/views/StorageLibraryView.js';
import { Toast } from '../../../core/ui/Toast.js';

export class StorageLibraryController {
    constructor() {
        this.libraries = [];
        this.view = new StorageLibraryView();
        this.container = null;
    }

    async init() {
        this.container = document.querySelector('#storage-libraries-container');
        if (!this.container) return;

        this.attachEventListeners();
        await this.loadLibraries();
    }

    attachEventListeners() {
        // Create library button
        const createBtn = document.querySelector('#create-library-btn');
        if (createBtn) {
            createBtn.addEventListener('click', () => this.showCreateModal());
        }

        // Delegate events for dynamically created elements
        this.container.addEventListener('click', (e) => {
            const editBtn = e.target.closest('.edit-library');
            const deleteBtn = e.target.closest('.delete-library');

            if (editBtn) {
                const id = editBtn.dataset.id;
                this.editLibrary(id);
            } else if (deleteBtn) {
                const id = deleteBtn.dataset.id;
                this.deleteLibrary(id);
            }
        });
    }

    async loadLibraries() {
        try {
            this.libraries = await StorageLibraryService.getAll();
            this.view.renderLibraries(this.libraries, this.container);
        } catch (error) {
            console.error('Error loading libraries:', error);
            Toast.error('فشل تحميل مكتبات التخزين');
        }
    }

    showCreateModal() {
        const modal = this.view.showLibraryModal();
        const form = modal.querySelector('#library-form');
        
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.handleFormSubmit(form);
        });
    }

    async editLibrary(id) {
        const library = this.libraries.find(l => l.storage_library_id == id);
        if (!library) return;

        const modal = this.view.showLibraryModal(library);
        const form = modal.querySelector('#library-form');
        
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.handleFormSubmit(form, id);
        });
    }

    async deleteLibrary(id) {
        if (!confirm('هل أنت متأكد من حذف مكتبة التخزين؟')) return;

        try {
            await StorageLibraryService.delete(id);
            Toast.success('تم حذف مكتبة التخزين بنجاح');
            await this.loadLibraries();
        } catch (error) {
            console.error('Error deleting library:', error);
            Toast.error(error.response?.data?.message || 'فشل حذف مكتبة التخزين');
        }
    }

    async handleFormSubmit(form, id = null) {
        const formData = new FormData(form);
        const data = {
            name: formData.get('name'),
            description: formData.get('description'),
            storage_limit: formData.get('storage_limit') || null
        };

        try {
            if (id) {
                await StorageLibraryService.update(id, data);
                Toast.success('تم تحديث مكتبة التخزين بنجاح');
            } else {
                await StorageLibraryService.create(data);
                Toast.success('تم إنشاء مكتبة التخزين والألبوم المخفي بنجاح');
            }

            $('#library-modal').modal('hide');
            await this.loadLibraries();
        } catch (error) {
            console.error('Error saving library:', error);
            Toast.error(error.response?.data?.message || 'فشل في حفظ مكتبة التخزين');
        }
    }
}

export default StorageLibraryController;
