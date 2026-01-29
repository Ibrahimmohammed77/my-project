import { StudioCardService } from '../services/StudioCardService';
import { Toast } from '../components/Toast';

class StudioCardDetailPage {
    constructor() {
        this.cardId = window.location.pathname.split('/').pop();
        this.form = document.getElementById('link-albums-form');
        this.init();
    }

    init() {
        if (this.form) {
            this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        }
    }

    async handleSubmit(e) {
        e.preventDefault();
        const selectedAlbums = Array.from(this.form.querySelectorAll('input[name="album_ids[]"]:checked'))
            .map(input => input.value);

        if (selectedAlbums.length === 0) {
            Toast.warning('يرجى اختيار ألبوم واحد على الأقل');
            return;
        }

        try {
            const response = await StudioCardService.linkAlbums(this.cardId, selectedAlbums);
            if (response.success) {
                Toast.success(response.message);
                // Redirect back to index after success
                setTimeout(() => {
                    window.location.href = '/studio/cards';
                }, 1500);
            }
        } catch (error) {
            Toast.error(error.response?.data?.message || 'خطأ في ربط الألبومات');
        }
    }
}

new StudioCardDetailPage();
