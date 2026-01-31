export class ReviewView {
    constructor() {
        this.grid = document.getElementById('photos-grid');
        this.emptyState = document.getElementById('empty-state');
        this.loadingState = document.getElementById('loading-state');
        this.pendingCount = document.getElementById('pending-count');
        this.rejectionModal = document.getElementById('rejection-modal');
        this.rejectionForm = document.getElementById('rejection-form');
        this.submitBtn = this.rejectionForm?.querySelector('button[type="submit"]');
        this.originalSubmitText = this.submitBtn?.innerHTML;
    }

    bindRejectionSubmit(handler) {
        if (!this.rejectionForm) return;
        this.rejectionForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const reason = document.getElementById('rejection_reason').value;
            handler(reason);
        });
    }

    setLoading(loading) {
        if (this.loadingState) {
            this.loadingState.classList.toggle('hidden', !loading);
        }
        if (this.grid) {
            if (loading) this.grid.classList.add('hidden');
            else this.grid.classList.remove('hidden');
        }
    }

    setSubmitting(submitting) {
        if (!this.submitBtn) return;
        if (submitting) {
            this.submitBtn.disabled = true;
            this.submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin ms-2"></i> جاري الرفض...';
            this.submitBtn.classList.add('opacity-75');
        } else {
            this.submitBtn.disabled = false;
            this.submitBtn.innerHTML = this.originalSubmitText;
            this.submitBtn.classList.remove('opacity-75');
        }
    }

    renderGrid(photos) {
        if (!this.grid) return;
        
        if (this.pendingCount) {
            this.pendingCount.textContent = photos.length;
        }

        if (photos.length === 0) {
            this.emptyState?.classList.remove('hidden');
            this.grid.innerHTML = '';
            return;
        }

        this.emptyState?.classList.add('hidden');
        this.grid.innerHTML = photos.map(photo => {
            // Safe Strings: name and user
            const albumName = photo.album?.name || 'ألبوم غير معروف';
            const userName = photo.album?.storage_library?.user?.full_name || 'مشترك غير معروف';
            const dateStr = new Date(photo.created_at).toLocaleDateString('ar-EG');
            
            // XSS Protection via textContent is harder with template literals map
            // Use manual escaping if simple replacing or use DOM construction.
            // Since this is a massive HTML block, I will assume basic escaping for now or implement a helper.
            // Wait, existing code used innerHTML. I should use DOM construction for safety.
            // But for complexity/speed, I will use a simple escape function here since I don't import one yet.
            // I'll assume text is reasonably safe or I can use a small helper.
            // I'll stick to innerHTML for structural parts but inject text carefully.
            // But map + join forces innerHTML.
            // I will use textContent by creating elements? Too verbose for this grid.
            // I will accept risk for now but add a comment.
            // OR I can use `DOM.escape` if I import it.
            return `
            <div id="photo-card-${photo.id}" class="group relative bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100">
                <div class="aspect-square overflow-hidden relative">
                    <img src="${photo.url}" alt="Review" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                        <button onclick="window.reviewController.approve(${photo.id})" class="w-12 h-12 rounded-full bg-green-500 text-white flex items-center justify-center shadow-lg hover:bg-green-600 transition-colors transform hover:scale-110">
                            <i class="fas fa-check text-lg"></i>
                        </button>
                        <button onclick="window.reviewController.openRejectionModal(${photo.id})" class="w-12 h-12 rounded-full bg-red-500 text-white flex items-center justify-center shadow-lg hover:bg-red-600 transition-colors transform hover:scale-110">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-bold text-accent uppercase tracking-wider album-name"></span>
                        <span class="text-sm font-bold text-gray-800 line-clamp-1 user-name"></span>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-[10px] text-gray-400">
                        <span class="flex items-center gap-1">
                            <i class="far fa-calendar"></i>
                            ${dateStr}
                        </span>
                        <span class="px-2 py-0.5 rounded-full bg-yellow-50 text-yellow-600 font-bold border border-yellow-100">معلق</span>
                    </div>
                </div>
            </div>`;
        }).join('');

        // NOW inject text content safely after rendering HTML
        photos.forEach(photo => {
            const card = document.getElementById(`photo-card-${photo.id}`);
            if (card) {
                card.querySelector('.album-name').textContent = photo.album?.name || 'ألبوم غير معروف';
                card.querySelector('.user-name').textContent = photo.album?.storage_library?.user?.full_name || 'مشترك غير معروف';
            }
        });
    }

    openRejectionModal(id) {
        if (!this.rejectionModal) return;
        this.rejectionForm.reset();
        this.rejectionModal.classList.remove('hidden');
    }

    closeRejectionModal() {
        if (!this.rejectionModal) return;
        this.rejectionModal.classList.add('hidden');
    }

    animateRemoval(id) {
        const card = document.getElementById(`photo-card-${id}`);
        if(card) {
            card.style.transform = 'scale(0.9)';
            card.style.opacity = '0';
        }
    }
}
