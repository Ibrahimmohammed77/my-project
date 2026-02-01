export class StorageView {
    constructor() {
        this.tableBody = document.querySelector('#storage-table tbody');
        this.searchField = document.getElementById('search');
        this.modal = document.getElementById('storage-modal');
        this.form = document.getElementById('storage-form');
        this.modalTitle = document.getElementById('modal-title');
        this.libraryIdInput = document.getElementById('library-id');
        
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
            const id = this.libraryIdInput.value;
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

    renderTable(libraries) {
        if (!this.tableBody) return;
        
        this.tableBody.innerHTML = '';
        
        if (libraries.length === 0) {
            this.renderEmpty();
            return;
        }

        libraries.forEach(lib => {
            const limitMB = lib.storage_limit ? (lib.storage_limit / 1048576).toFixed(2) : 'غير محدود';
            const usedMB = (lib.used_storage / 1048576 || 0).toFixed(2);
            const percent = lib.storage_limit > 0 ? Math.min(100, ((lib.used_storage || 0) / lib.storage_limit) * 100).toFixed(1) : 0;
            
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50/50 transition-colors group border-b border-gray-100';
            
            tr.innerHTML = `
                <td class="py-4 px-4">
                    <div class="flex flex-col">
                        <span class="font-bold text-gray-800 text-sm"></span>
                        <span class="text-[11px] text-gray-400"></span>
                    </div>
                </td>
                <td class="py-4 px-4">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-folder-open text-blue-500 text-xs"></i>
                        <span class="text-sm text-gray-600"></span>
                    </div>
                </td>
                <td class="py-4 px-4">
                    <div class="flex flex-col gap-1.5 min-w-[120px]">
                        <div class="flex justify-between text-[10px] font-bold">
                            <span class="text-gray-400">${usedMB} MB ${lib.storage_limit ? '/ ' + limitMB + ' MB' : ''}</span>
                            ${lib.storage_limit ? `<span class="${percent > 90 ? 'text-red-500' : 'text-accent'}">${percent}%</span>` : ''}
                        </div>
                        ${lib.storage_limit ? `
                            <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-accent rounded-full transition-all duration-500" style="width: ${percent}%"></div>
                            </div>
                        ` : '<span class="text-xs text-green-600">✓ غير محدود</span>'}
                    </div>
                </td>
                <td class="py-4 px-4 text-[11px] text-gray-500">${new Date(lib.created_at).toLocaleDateString('ar-EG')}</td>
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

            // HTML Escape
            tr.querySelector('td:nth-child(1) span:nth-child(1)').textContent = lib.name;
            tr.querySelector('td:nth-child(1) span:nth-child(2)').textContent = lib.description || 'بدون وصف';
            tr.querySelector('td:nth-child(2) span').textContent = lib.hidden_album?.name || 'ألبوم مخفي';

            // Events
            tr.querySelector('.edit-btn').addEventListener('click', () => window.storageController.edit(lib.storage_library_id));
            tr.querySelector('.delete-btn').addEventListener('click', () => window.storageController.delete(lib.storage_library_id));

            this.tableBody.appendChild(tr);
        });
    }

    renderEmpty() {
        this.tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="py-20 text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-gray-300">
                        <i class="fa-solid fa-hard-drive text-gray-300 text-3xl"></i>
                    </div>
                    <h4 class="text-gray-800 font-bold">لا يوجد مكتبات تخزين</h4>
                    <p class="text-gray-500 text-sm mt-1">قم بإنشاء مكتبة جديدة - سيتم إنشاء ألبوم مخفي تلقائياً</p>
                </td>
            </tr>
        `;
    }

    openCreateModal() {
        this.form.reset();
        this.libraryIdInput.value = '';
        if(this.modalTitle) this.modalTitle.textContent = 'تخصيص مساحة جديدة';
        this.modal.classList.remove('hidden');
    }

    openEditModal(lib) {
        this.form.reset();
        this.libraryIdInput.value = lib.storage_library_id;
        if(this.modalTitle) this.modalTitle.textContent = 'تعديل بيانات المكتبة';

        document.getElementById('name').value = lib.name;
        document.getElementById('description').value = lib.description || '';
        if (lib.storage_limit) {
            document.getElementById('storage_limit').value = lib.storage_limit / (1024 * 1024); // Convert Bytes to MB
        }

        this.modal.classList.remove('hidden');
    }

    closeModal() {
        this.modal.classList.add('hidden');
    }
}
