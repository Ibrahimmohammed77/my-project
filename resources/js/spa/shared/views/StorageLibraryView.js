export class StorageLibraryView {
    /**
     * Render storage libraries in a simple card grid
     */
    renderLibraries(libraries, container) {
        if (!container) return;

        if (!libraries || libraries.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <p>لا توجد مكتبات تخزين</p>
                    <button class="btn btn-primary" id="create-library-btn">
                        <i class="fas fa-plus"></i> إنشاء مكتبة تخزين
                    </button>
                </div>
            `;
            return;
        }

        const html = libraries.map(lib => `
            <div class="library-card" data-id="${lib.storage_library_id}">
                <div class="library-header">
                    <h3>${lib.name}</h3>
                    <div class="library-actions">
                        <button class="btn btn-sm btn-icon edit-library" data-id="${lib.storage_library_id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-icon btn-danger delete-library" data-id="${lib.storage_library_id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="library-body">
                    ${lib.description ? `<p class="library-description">${lib.description}</p>` : ''}
                    <div class="library-stats">
                        <div class="stat">
                            <i class="fas fa-folder-open"></i>
                            <span>الألبوم المخفي: ${lib.hidden_album?.name || 'غير محدد'}</span>
                        </div>
                        <div class="stat">
                            <i class="fas fa-credit-card"></i>
                            <span>الكروت: ${lib.cards?.length || 0}</span>
                        </div>
                        ${lib.storage_limit ? `
                            <div class="stat">
                                <i class="fas fa-hdd"></i>
                                <span>${(lib.storage_limit / 1024 / 1024).toFixed(0)} MB</span>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `).join('');

        container.innerHTML = html;
    }

    /**
     * Show create/edit library modal
     */
    showLibraryModal(library = null) {
        const isEdit = !!library;
        const title = isEdit ? 'تعديل مكتبة التخزين' : 'إنشاء مكتبة تخزين جديدة';

        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = 'library-modal';
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${title}</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <form id="library-form">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>اسم مكتبة التخزين *</label>
                                <input type="text" class="form-control" name="name" 
                                       value="${library?.name || ''}" required>
                            </div>
                            <div class="form-group">
                                <label>الوصف</label>
                                <textarea class="form-control" name="description" rows="3">${library?.description || ''}</textarea>
                            </div>
                            <div class="form-group">
                                <label>حد التخزين (MB)</label>
                                <input type="number" class="form-control" name="storage_limit" 
                                       value="${library?.storage_limit ? (library.storage_limit / 1024 / 1024) : ''}" 
                                       placeholder="اختياري">
                                <small class="form-text text-muted">اتركه فارغاً للسماح بغير محدود</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary">
                                ${isEdit ? 'تحديث' : 'إنشاء'}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        $(modal).modal('show');
        
        $(modal).on('hidden.bs.modal', () => {
            modal.remove();
        });

        return modal;
    }

    /**
     * Render storage library selector for card assignment
     */
    renderLibrarySelector(libraries, selectedId = null) {
        if (!libraries || libraries.length === 0) {
            return `<option value="">لا توجد مكتبات متاحة</option>`;
        }

        return libraries.map(lib => `
            <option value="${lib.storage_library_id}" ${selectedId == lib.storage_library_id ? 'selected' : ''}>
                ${lib.name} ${lib.hidden_album ? `(${lib.hidden_album.name})` : ''}
            </option>
        `).join('');
    }
}

export default StorageLibraryView;
