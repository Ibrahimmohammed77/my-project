import StudioPhotoReviewService from '../../../../shared/services/StudioPhotoReviewService.js';
import { ReviewView } from '../views/ReviewView.js';
import { Toast } from '../../../../core/ui/Toast.js';

export class ReviewController {
    constructor() {
        this.view = new ReviewView();
        this.photos = [];
        this.currentPhotoId = null;
        this.init();
    }

    async init() {
        this.view.bindRejectionSubmit(this.handleRejection.bind(this));
        
        // Global Exposure
        window.reviewController = this;
        window.closeModal = () => this.view.closeRejectionModal();

        await this.loadPhotos();
    }

    async loadPhotos() {
        try {
            this.view.setLoading(true);
            this.photos = await StudioPhotoReviewService.getPending();
            this.view.renderGrid(this.photos);
        } catch (error) {
            Toast.error('خطأ في تحميل الصور');
        } finally {
            this.view.setLoading(false);
        }
    }

    async approve(id) {
        try {
            this.view.animateRemoval(id);
            const response = await StudioPhotoReviewService.review(id, 'approved');
            Toast.success(response.message);
            
            this.photos = this.photos.filter(p => p.id !== id);
            this.view.renderGrid(this.photos);
        } catch (error) {
            Toast.error(error.response?.data?.message || 'خطأ في الموافقة على الصورة');
            // Reload to restore state if animation happened
            await this.loadPhotos();
        }
    }

    openRejectionModal(id) {
        this.currentPhotoId = id;
        this.view.openRejectionModal(id);
    }

    async handleRejection(reason) {
        if (!reason) {
            Toast.warning('يرجى ذكر سبب الرفض');
            return;
        }

        try {
            this.view.setSubmitting(true);
            const response = await StudioPhotoReviewService.review(this.currentPhotoId, 'rejected', reason);
            Toast.success(response.message);
            
            this.view.closeRejectionModal();
            this.photos = this.photos.filter(p => p.id !== this.currentPhotoId);
            this.view.renderGrid(this.photos);
            this.currentPhotoId = null;
        } catch (error) {
            Toast.error(error.response?.data?.message || 'خطأ في رفض الصورة');
        } finally {
            this.view.setSubmitting(false);
        }
    }
}
