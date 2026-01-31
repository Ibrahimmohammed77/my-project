import StudioCardService from '../../../../shared/services/StudioCardService.js';
import { CardDetailView } from '../views/CardDetailView.js';
import { Toast } from '../../../../core/ui/Toast.js';

export class CardDetailController {
    constructor() {
        this.view = new CardDetailView();
        // Get card ID from hidden input we added
        this.cardId = document.getElementById('card-id')?.value;
        this.init();
    }

    init() {
        if (!this.cardId) {
            console.error('Card ID not found in DOM');
            return;
        }
        this.view.bindSubmit(this.handleSubmit.bind(this));
    }

    async handleSubmit(albumIds) {
        // Validation: At least one album? Or allow clearing?
        // Backend doesn't strictly require array to be non-empty in logic (it syncs). 
        // If empty, it detaches all? 
        // LinkCardToAlbumUseCase probably uses `sync` or `attach`.
        // If strict requirement: "Please select albums".
        // Let's allow empty to unlink all if logic permits, but usually users want to link.
        // Legacy warned on empty. I'll maintain that warning behavior.
        
        if (albumIds.length === 0) {
            if (!confirm('لم يتم اختيار أي ألبوم. سيتم إلغاء ربط جميع الألبومات. هل أنت متأكد؟')) {
                return;
            }
        }

        try {
            this.view.setLoading(true);
            const response = await StudioCardService.linkAlbums(this.cardId, albumIds);
            
            if (response.success) {
                Toast.success(response.message || 'تم تحديث الربط بنجاح');
                // Optional: Redirect to list after short delay?
                setTimeout(() => {
                    window.location.href = '/studio/cards';
                }, 1500);
            }
        } catch (error) {
            Toast.error(error.response?.data?.message || 'خطأ في عملية الربط');
        } finally {
            this.view.setLoading(false);
        }
    }
}

