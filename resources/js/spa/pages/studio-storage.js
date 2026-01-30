import { StudioStorageService } from '../services/StudioStorageService';
import { Toast } from '../components/Toast';

class StudioStoragePage {
    constructor() {
        this.libraries = [];
        this.customers = [];
        this.tableBody = document.querySelector('#storage-table tbody');
        this.searchField = document.getElementById('search');
        this.modal = document.getElementById('storage-modal');
        this.form = document.getElementById('storage-form');
        this.modalTitle = document.getElementById('modal-title');
        this.libraryIdInput = document.getElementById('library-id');
        this.subscriberSelect = document.getElementById('subscriber_id');
        this.subscriberWrapper = document.getElementById('subscriber-wrapper');
        
        this.init();
    }

    async init() {
        if (this.searchField) {
            this.searchField.addEventListener('input', (e) => this.handleSearch(e));
        }

        if (this.form) {
            this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        }

        await this.loadData();
    }

    async loadData() {
        try {
            this.renderLoading();
            [this.libraries, this.customers] = await Promise.all([
                StudioStorageService.getAll(),
                StudioStorageService.getCustomers()
            ]);
            this.renderTable(this.libraries);
            this.populateSubscribers();
        } catch (error) {
            Toast.error('خطأ في تحميل البيانات');
        }
    }

    renderLoading() {
        if (this.tableBody) {
            this.tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="py-10 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <i class="fas fa-spinner fa-spin text-accent text-2xl"></i>
                            <span class="text-sm text-gray-500">جاري تحميل البيانات...</span>
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
                            <i class="fa-solid fa-hard-drive text-gray-300 text-3xl"></i>
                        </div>
                        <h4 class="text-gray-800 font-bold">لا يوجد مكتبات تخزين</h4>
                        <p class="text-gray-500 text-sm mt-1">قم بإنشاء مكتبة جديدة لتخصيص مساحة للمشتركين</p>
                    </td>
                </tr>
            `;
        }
    }

    renderTable(libraries) {
        if (!this.tableBody) return;
        
        if (libraries.length === 0) {
            this.renderEmpty();
            return;
        }

        this.tableBody.innerHTML = libraries.map(lib => {
            const limitMB = (lib.storage_limit / 1048576).toFixed(2);
            const usedMB = (lib.storage_used / 1048576).toFixed(2);
            const percent = Math.min(100, (lib.storage_used / lib.storage_limit) * 100).toFixed(1);
            
            return `
                <tr class="hover:bg-gray-50/50 transition-colors group">
                    <td class="py-4 px-4">
                        <div class="flex flex-col">
                            <span class="font-bold text-gray-800 text-sm">${lib.name}</span>
                            <span class="text-[11px] text-gray-400">${lib.description || 'بدون وصف'}</span>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-sm text-gray-600">${lib.user?.full_name || lib.user?.username || 'غير معروف'}</td>
                    <td class="py-4 px-4">
                        <div class="flex flex-col gap-1.5 min-w-[120px]">
                            <div class="flex justify-between text-[10px] font-bold">
                                <span class="text-gray-400">${usedMB} MB / ${limitMB} MB</span>
                                <span class="${percent > 90 ? 'text-red-500' : 'text-accent'}">${percent}%</span>
                            </div>
                            <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-accent rounded-full transition-all duration-500" style="width: ${percent}%"></div>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-[11px] text-gray-500">${new Date(lib.created_at).toLocaleDateString('ar-EG')}</td>
                    <td class="py-4 px-4">
                        <div class="flex gap-2 justify-center">
                            <button onclick="window.storagePage.editLibrary(${lib.id})" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-accent hover:border-accent transition-all flex items-center justify-center shadow-soft">
                                <i class="fa-solid fa-pen text-xs"></i>
                            </button>
                            <button onclick="window.storagePage.deleteLibrary(${lib.id})" class="w-8 h-8 rounded-lg bg-white border border-gray-200 text-gray-400 hover:text-red-500 hover:border-red-500 transition-all flex items-center justify-center shadow-soft">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    populateSubscribers() {
        if (!this.subscriberSelect) return;
        this.subscriberSelect.innerHTML = '<option value="">اختر مشتركاً</option>' + 
            this.customers.map(c => `<option value="${c.id}">${c.full_name || c.username}</option>`).join('');
    }

    handleSearch(e) {
        const query = e.target.value.toLowerCase();
        const filtered = this.libraries.filter(lib => 
            lib.name.toLowerCase().includes(query) || 
            (lib.user && (lib.user.full_name || lib.user.username).toLowerCase().includes(query))
        );
        this.renderTable(filtered);
    }

    showCreateModal() {
        this.form.reset();
        this.libraryIdInput.value = '';
        this.modalTitle.textContent = 'تخصيص مساحة جديدة';
        this.subscriberWrapper.classList.remove('hidden');
        this.subscriberSelect.required = true;
        this.modal.classList.remove('hidden');
    }

    editLibrary(id) {
        const lib = this.libraries.find(l => l.id === id);
        if (!lib) return;

        this.form.reset();
        this.libraryIdInput.value = lib.id;
        this.modalTitle.textContent = 'تعديل بيانات المكتبة';
        this.subscriberWrapper.classList.add('hidden');
        this.subscriberSelect.required = false;

        document.getElementById('name').value = lib.name;
        document.getElementById('description').value = lib.description || '';
        document.getElementById('storage_limit').value = lib.storage_limit / (1024 * 1024);

        this.modal.classList.remove('hidden');
    }

    closeModal() {
        this.modal.classList.add('hidden');
    }

    async handleSubmit(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());
        const id = this.libraryIdInput.value;

        try {
            let response;
            if (id) {
                response = await StudioStorageService.update(id, data);
            } else {
                response = await StudioStorageService.create(data);
            }

            if (response.success) {
                Toast.success(response.message);
                this.closeModal();
                await this.loadData();
            }
        } catch (error) {
            Toast.error(error.response?.data?.message || 'حدث خطأ أثناء حفظ البيانات');
        }
    }

    async deleteLibrary(id) {
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
}

window.storagePage = new StudioStoragePage();
window.showCreateModal = () => window.storagePage.showCreateModal();
window.closeModal = () => window.storagePage.closeModal();
