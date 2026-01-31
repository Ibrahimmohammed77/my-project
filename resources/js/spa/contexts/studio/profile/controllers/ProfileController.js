import { ProfileService } from '../services/ProfileService.js';
import { ProfileView } from '../views/ProfileView.js';
import { Toast } from '../../../../core/ui/Toast.js';
import { InputValidator } from '../../../../core/security/InputValidator.js';

export class ProfileController {
    constructor() {
        this.view = new ProfileView();
        this.init();
    }

    init() {
        this.view.bindUpdate(this.handleUpdate.bind(this));
    }

    async handleUpdate(data) {
        this.view.clearErrors();

        // 1. Client-side Validation (Mirroring Backend Rules)
        const errors = {};

        // name: string, max 255
        if (!InputValidator.validate(data.name, 'required')) {
             errors.name = ['اسم الاستوديو مطلوب'];
        } else if (data.name.length > 255) {
             errors.name = ['اسم الاستوديو يجب أن لا يتجاوز 255 حرفاً'];
        }

        // city: max 100
        if (data.city && data.city.length > 100) {
            errors.city = ['اسم المدينة يجب أن لا يتجاوز 100 حرف'];
        }

        // website: url
        // InputValidator might not have isUrl, checking implementation or using regex
        // Assuming InputValidator.isUrl exists or we use regex
        if (data.website && !this.isValidUrl(data.website)) {
            errors.website = ['رابط الموقع غير صحيح'];
        }

        if (Object.keys(errors).length > 0) {
            this.view.displayErrors(errors);
            return;
        }

        // 2. Submit Logic
        try {
            this.view.setLoading(true);
            const response = await ProfileService.update(data);
            
            if (response.data.success) {
                Toast.success(response.data.message || 'تم تحديث البيانات بنجاح');
                // Optional: Scroll to top?
            }
        } catch (error) {
            if (error.response?.status === 422) {
                this.view.displayErrors(error.response.data.errors);
                Toast.error('يرجى تصحيح الأخطاء في النموذج');
            } else {
                Toast.error(error.response?.data?.message || 'حدث خطأ أثناء الاتصال بالخادم');
            }
        } finally {
            this.view.setLoading(false);
        }
    }

    isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }
}

