import { StudioPhotoReviewService } from '../services/StudioPhotoReviewService';
import { Toast } from '../components/Toast';

class PhotoReviewPage {
    constructor() {
        this.photos = [];
        this.grid = document.getElementById('photos-grid');
        this.emptyState = document.getElementById('empty-state');
        this.loadingState = document.getElementById('loading-state');
        this.pendingCount = document.getElementById('pending-count');
        this.rejectionModal = document.getElementById('rejection-modal');
        this.rejectionForm = document.getElementById('rejection-form');
        this.currentPhotoId = null;

        this.init();
    }

    async init() {
        if (this.rejectionForm) {
            this.rejectionForm.addEventListener('submit', (e) => this.handleRejectionSubmit(e));
        }

        await this.loadPhotos();
    }

    async loadPhotos() {
        try {
            this.showLoading(true);
            this.photos = await StudioPhotoReviewService.getPending();
            this.render();
        } catch (error) {
            Toast.error('حدث خطأ أثناء تحميل الصور');
        } finally {
            this.showLoading(false);
        }
    }

    showLoading(show) {
        if (this.loadingState) {
            this.loadingState.classList.toggle('hidden', !show);
        }
        if (this.grid && !show) {
            this.grid.classList.remove('hidden');
        } else if (this.grid) {
            this.grid.classList.add('hidden');
        }
    }

    render() {
        if (!this.grid) return;

        if (this.pendingCount) {
            this.pendingCount.textContent = this.photos.length;
        }

        if (this.photos.length === 0) {
            this.emptyState?.classList.remove('hidden');
            this.grid.innerHTML = '';
            return;
        }

        this.emptyState?.classList.add('hidden');
        this.grid.innerHTML = this.photos.map(photo => `
            <div id="photo-card-${photo.id}" class="group relative bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100">
                <div class="aspect-square overflow-hidden relative">
                    <img src="${photo.url}" alt="Review" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                        <button onclick="window.photoReviewPage.approve(${photo.id})" class="w-12 h-12 rounded-full bg-green-500 text-white flex items-center justify-center shadow-lg hover:bg-green-600 transition-colors transform hover:scale-110">
                            <i class="fas fa-check text-lg"></i>
                        </button>
                        <button onclick="window.photoReviewPage.openRejectionModal(${photo.id})" class="w-12 h-12 rounded-full bg-red-500 text-white flex items-center justify-center shadow-lg hover:bg-red-600 transition-colors transform hover:scale-110">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-bold text-accent uppercase tracking-wider">${photo.album?.name || 'ألبوم غير معروف'}</span>
                        <span class="text-sm font-bold text-gray-800 line-clamp-1">${photo.album?.storage_library?.user?.full_name || 'مشترك غير معروف'}</span>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-[10px] text-gray-400">
                        <span class="flex items-center gap-1">
                            <i class="far fa-calendar"></i>
                            ${new Date(photo.created_at).toLocaleDateString('ar-EG')}
                        </span>
                        <span class="px-2 py-0.5 rounded-full bg-yellow-50 text-yellow-600 font-bold border border-yellow-100">معلق</span>
                    </div>
                </div>
            </div>
        `).join('');
    }

    async approve(id) {
        try {
            const card = document.getElementById(`photo-card-${id}`);
            if (card) card.classList.add('scale-95', 'opacity-50');

            const response = await StudioPhotoReviewService.review(id, 'approved');
            Toast.success(response.message);
            
            this.photos = this.photos.filter(p => p.id !== id);
            this.render();
        } catch (error) {
            Toast.error(error.response?.data?.message || 'خطأ في مراجعة الصورة');
            const card = document.getElementById(`photo-card-${id}`);
            if (card) card.classList.remove('scale-95', 'opacity-50');
        }
    }

    openRejectionModal(id) {
        this.currentPhotoId = id;
        this.rejectionForm.reset();
        this.rejectionModal.classList.remove('hidden');
    }

    closeModal() {
        this.rejectionModal.classList.add('hidden');
        this.currentPhotoId = null;
    }

    async handleRejectionSubmit(e) {
        e.preventDefault();
        const reason = document.getElementById('rejection_reason').value;
        if (!reason) {
            Toast.warning('يرجى إدخال سبب الرفض');
            return;
        }

        try {
            const response = await StudioPhotoReviewService.review(this.currentPhotoId, 'rejected', reason);
            Toast.success(response.message);
            
            this.photos = this.photos.filter(p => p.id !== this.currentPhotoId);
            this.closeModal();
            this.render();
        } catch (error) {
            Toast.error(error.response?.data?.message || 'خطأ في رفض الصورة');
        }
    }
}

window.photoReviewPage = new PhotoReviewPage();
window.closeModal = () => window.photoReviewPage.closeModal();
