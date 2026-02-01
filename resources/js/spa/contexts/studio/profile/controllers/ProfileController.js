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
        // 1. Client-side Validation (Mirroring Backend Rules)
        const errors = {};

        const name = data instanceof FormData ? data.get('name') : data.name;
        const city = data instanceof FormData ? data.get('city') : data.city;
        const website = data instanceof FormData ? data.get('website') : data.website;

        // name: string, max 255
        if (!InputValidator.validate(name, 'required')) {
            errors.name = ['اسم الاستوديو مطلوب'];
        } else if (name && name.length > 255) {
            errors.name = ['اسم الاستوديو يجب أن لا يتجاوز 255 حرفاً'];
        }

        // city: max 100
        if (city && city.length > 100) {
            errors.city = ['اسم المدينة يجب أن لا يتجاوز 100 حرف'];
        }

        // website: url
        if (website && !this.isValidUrl(website)) {
            errors.website = ['رابط الموقع غير صحيح'];
        }

        if (Object.keys(errors).length > 0) {
            this.view.displayErrors(errors);
            return;
        }

        // 2. Submit Logic
        try {
            this.view.setLoading(true);

            // Handle Method Spoofing for File Uploads in PHP
            if (data instanceof FormData) {
                data.append('_method', 'PUT');
            } else {
                data = { ...data, _method: 'PUT' };
            }

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

